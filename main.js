const gridOptions = {
  columnDefs: [
    { field: "id", filter: "agTextColumnFilter" },
    { field: "AccountID" },
    { field: "TaxRegID" },
    { field: "Nome" },
    { field: "Cognome" },
    { field: "RagioneSociale" },
    { field: "Email" },
    { field: "Indirizzo" },
    { field: "Comune" },
    { field: "CAP" },
    { field: "Provincia" },
    { field: "Tel" },
    { field: "NumMobile" },
    { field: "NumPagineDaAttivare" },
    { field: "NumPagineAttivate" },
    { field: "status" },
    { field: "create_dt" },
    { field: "update_dt" },
    { field: "readed" },
    { field: "reseller_experience_manager_id" },
    { field: "CAOName" },
    {
      field: "title_cat",
      headerName: "Category choice",
      pinned: "right",
      editable: true,
      cellEditor: "agRichSelectCellEditor",
      cellEditorPopup: true,
      cellEditorParams: {
        values: [],
      },
    },
  ],
  defaultColDef: {
    enableValue: true,
    enableRowGroup: true,
    enablePivot: true,
    sortable: true,
    filter: true,
    resizable: true,
  },
  sideBar: true,
};
var category = {};
list_modified_row = [];

document.addEventListener("DOMContentLoaded", function () {
  var gridDiv = document.querySelector("#myGrid");
  new agGrid.Grid(gridDiv, gridOptions);

  fetch("backend.php")
    .then((response) => response.json())
    .then((data) => {
      let cat_tab = [];
      data.category.map((el) => {
        cat_tab.push(el.title);
      });
      gridOptions.columnDefs.find(
        (colDef) => colDef.field === "title_cat"
      ).cellEditorParams.values = cat_tab;
      category = data.category;
      setCategory(data);
      gridOptions.api.setRowData(data.data);
      autoSizeAll();
      gridOptions.api.closeToolPanel();
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération des données:", error);
      createNotification(
        `Erreur lors de l'envoi des données au backend: ${error}`
      );
      const body = document.querySelector("body");
      const out = document.createElement("div");
      out.innerHTML = error;
      body.appendChild(out);
    });

  // Ajouter un gestionnaire d'événements pour écouter les modifications de cellule
  gridOptions.api.addEventListener("cellValueChanged", function (event) {
    // Récupérer les données modifiées
    const modifiedData = reassignationIdCat(event.data);
    const existingRow = list_modified_row.find(
      (row) => row.id === modifiedData.id
    );
    if (existingRow) {
      Object.assign(existingRow, modifiedData);
    } else {
      list_modified_row.push(modifiedData);
    }
    if (list_modified_row.length === 1) {
      createSaveButton();
    }
    console.log(list_modified_row);
  });
});

/**
 * Récupère les données modifiées et réassigne l'ID de la catégorie correspondante.
 * @param {Object} rowModified - Ligne modifiée.
 * @returns {Object} - Ligne modifiée avec l'ID de la catégorie réassigné.
 */
function reassignationIdCat(rowModified) {
  const newIdCat = category.find((el) => el.title === rowModified.title_cat);
  rowModified.id_cat = newIdCat.id;
  return rowModified;
}

/**
 * Crée un bouton "Save Changes" et l'ajoute au document.
 */
function createSaveButton() {
  const saveButton = document.createElement("button");
  saveButton.textContent = "Save Changes";
  saveButton.style.position = "fixed";
  saveButton.style.bottom = "20px";
  saveButton.style.right = "20px";
  saveButton.style.padding = "10px";
  saveButton.style.backgroundColor = "#007bff";
  saveButton.style.color = "#fff";
  saveButton.style.border = "none";
  saveButton.style.borderRadius = "5px";
  saveButton.style.cursor = "pointer";

  saveButton.addEventListener("click", function () {
    saveChangesToBackend();
  });

  document.body.appendChild(saveButton);
}

/**
 * Redimensionne automatiquement toutes les colonnes de la grille.
 * @param {boolean} [skipHeader=false] - Indique si l'en-tête doit être exclu du redimensionnement.
 */
function autoSizeAll(skipHeader) {
  const allColumnIds = [];
  gridOptions.columnApi.getColumns().forEach((column) => {
    allColumnIds.push(column.getId());
  });

  gridOptions.columnApi.autoSizeColumns(allColumnIds, skipHeader);
}

/**
 * Initialise les catégories et les données de la grille.
 * @param {Object} data - Données à utiliser.
 */
function setCategory(data) {
  const categories = data.category;
  const data_tmp = data.data;

  data_tmp.forEach((el) => {
    console.log(el.id_cat_automatica, el.id_cat);
    if (el.id_cat_automatica !== null) {
      el.title_cat = categories[el.id_cat_automatica].title;
      el.id_cat = categories[el.id_cat_automatica].id;
    } else if (el.id_cat !== null) {
      el.title_cat = categories[el.id_cat].title;
    }
  });
}

/**
 * Envoie les modifications vers le backend pour être sauvegardées.
 */
function saveChangesToBackend() {
  var data_tmp = list_modified_row;
  fetch("backend.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ modifiedData: data_tmp }),
  })
    .then((response) => response.json())
    .then((result) => {
      console.log(result); // Afficher la réponse du backend
      result.messages.map((el) => createNotification(el.message));
      majDataFront();
    })
    .catch((error) => {
      console.error("Erreur lors de l'envoi des données au backend:", error);
      createNotification(
        `Erreur lors de l'envoi des données au backend: ${error}`
      );
      const body = document.querySelector("body");
      const out = document.createElement("div");
      out.innerHTML = error;
      body.appendChild(out);
    });
}

/**
 * Met à jour les données affichées dans la grille après la sauvegarde.
 */
function majDataFront() {
  fetch("backend.php")
    .then((response) => response.json())
    .then((data) => {
      let cat_tab = [];
      data.category.map((el) => {
        cat_tab.push(el.title);
      });
      gridOptions.columnDefs.find(
        (colDef) => colDef.field === "title_cat"
      ).cellEditorParams.values = cat_tab;
      category = data.category;
      setCategory(data);
      gridOptions.api.setRowData(data.data);
      autoSizeAll();
      gridOptions.api.closeToolPanel();
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération des données:", error);
      createNotification(
        `Erreur lors de l'envoi des données au backend: ${error}`
      );
      const body = document.querySelector("body");
      const out = document.createElement("div");
      out.innerHTML = error;
      body.appendChild(out);
    });
}

/**
 * Crée une notification et l'ajoute à l'interface utilisateur.
 * @param {string} text - Texte de la notification.
 */
function createNotification(text) {
  var notificationsContainer = document.getElementById(
    "notifications-container"
  );
  if (!notificationsContainer) {
    notificationsContainer = document.createElement("div");
    notificationsContainer.id = "notifications-container";
    notificationsContainer.style.display = "flex";
    notificationsContainer.style.flexDirection = "column";
    notificationsContainer.style.position = "fixed";
    notificationsContainer.style.bottom = "20px";
    notificationsContainer.style.right = "20px";
    document.body.appendChild(notificationsContainer);
  }
  const notification = document.createElement("div");
  notification.textContent = text;

  notification.style.padding = "10px";
  notification.style.backgroundColor = "#007bff";
  notification.style.color = "#fff";
  notification.style.borderRadius = "5px";
  notification.style.boxShadow = "0 2px 5px rgba(0, 0, 0, 0.3)";
  notification.style.cursor = "pointer";
  notification.style.opacity = "0";
  notification.style.transition = "opacity 0.4s ease";
  notification.style.marginBottom = "5px";

  notificationsContainer.insertBefore(
    notification,
    notificationsContainer.firstChild
  );

  setTimeout(function () {
    notification.style.opacity = "1";
  }, 100);

  setTimeout(function () {
    notification.style.opacity = "0";
    setTimeout(function () {
      notification.parentNode.removeChild(notification);
    }, 300);
  }, 3000);
}
