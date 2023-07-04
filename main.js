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

/**
 * Fonction exécutée lorsque le DOM est chargé.
 */
document.addEventListener("DOMContentLoaded", function () {
  // Sélectionner l'élément de la grille dans le DOM
  var gridDiv = document.querySelector("#myGrid");

  // Créer une instance de la grille avec les options spécifiées
  new agGrid.Grid(gridDiv, gridOptions);

  // Récupérer les données à partir du backend
  fetch("backend.php?page=index")
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      // Extraire les titres des catégories pour les paramètres de l'éditeur de cellules
      let cat_tab = [];
      data.category.map((el) => {
        cat_tab.push(el.title);
      });

      // Mettre à jour les valeurs des paramètres de l'éditeur de cellules pour la colonne "title_cat"
      gridOptions.columnDefs.find(
        (colDef) => colDef.field === "title_cat"
      ).cellEditorParams.values = cat_tab;

      // Stocker les catégories dans une variable globale
      category = data.category;

      // Appliquer les données à la grille
      setCategory(data);
      gridOptions.api.setRowData(data.data);
      autoSizeAll();
      gridOptions.api.closeToolPanel();

      // Créer le bouton de gestion
      createManagementButton();
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération des données:", error);
      createNotification(
        `Erreur lors de l'envoi des données au backend: ${error}`
      );

      // Afficher l'erreur dans le DOM
      const body = document.querySelector("body");
      const out = document.createElement("div");
      out.innerHTML = error;
      body.appendChild(out);
    });

  /**
   * Gestionnaire d'événements pour les modifications de cellule dans la grille.
   */
  gridOptions.api.addEventListener("cellValueChanged", function (event) {
    // Vérifier si la valeur de la cellule a été supprimée
    if (event.newValue === null || event.newValue === "") {
      // Supprimer la ligne modifiée de la liste
      const index = list_modified_row.findIndex(
        (row) => row.id === event.data.id
      );
      if (index !== -1) {
        list_modified_row.splice(index, 1);
      }
    } else {
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
    }

    // Vérifier s'il faut créer ou supprimer le bouton de sauvegarde
    if (
      list_modified_row.length >= 1 &&
      !document.getElementById("bouttonSave")
    ) {
      createSaveButton();
    } else if (
      list_modified_row.length <= 0 &&
      document.getElementById("bouttonSave")
    ) {
      const button = document.getElementById("bouttonSave");
      button.parentNode.removeChild(button);
    }
    console.debug(list_modified_row);
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
  const buttonArea = document.getElementById("buttonArea");
  const saveButton = document.createElement("button");
  saveButton.id = "bouttonSave";
  saveButton.textContent = "Save Changes";
  saveButton.style.padding = "10px";
  saveButton.style.margin = "5px";
  saveButton.style.backgroundColor = "#007bff";
  saveButton.style.color = "#fff";
  saveButton.style.border = "none";
  saveButton.style.borderRadius = "5px";
  saveButton.style.cursor = "pointer";

  saveButton.addEventListener("click", function () {
    saveChangesToBackend();
  });

  buttonArea.appendChild(saveButton);
}

/**
 * Crée un bouton "Save Changes" et l'ajoute au document.
 */
function createManagementButton() {
  const buttonArea = document.getElementById("buttonArea");
  const saveButton = document.createElement("button");
  saveButton.textContent = "Go to Management Page";
  saveButton.style.padding = "10px";
  saveButton.style.margin = "5px";
  saveButton.style.backgroundColor = "#49a1ff";
  saveButton.style.color = "#fff";
  saveButton.style.border = "none";
  saveButton.style.borderRadius = "5px";
  saveButton.style.cursor = "pointer";

  saveButton.addEventListener("click", function () {
    load_manually_reseller_category();
  });

  buttonArea.appendChild(saveButton);
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
    console.debug(el.id, el.id_cat_automatica, el.id_cat);
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
  fetch("backend.php?page=index", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ modifiedData: data_tmp }),
  })
    .then((response) => response.json())
    .then((result) => {
      list_modified_row = [];
      console.debug(result); // Afficher la réponse du backend
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
  fetch("backend.php?page=index")
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
    notificationsContainer.style.bottom = "60px";
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

function load_manually_reseller_category() {
  fetch("backend.php?page=management")
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
