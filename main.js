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
      cellEditorPopup: false,
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
var list_modified_row = [];
var statusCat = [];

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
      // Stocker les catégories dans une variable globale
      category = data.category;

      // Appliquer les données à la grille
      addOptionColumn("title_cat", data.category);
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
    if (event.column.getColId() === "title_cat") {
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
    } else if (event.column.getColId() === "status_cat") {
      const modifiedData = reassignationIdStatus(event.data);
      saveChangesToBackendAuto(modifiedData);
    }
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
 * Réaffecte l'identifiant de statut à une ligne modifiée en fonction de la valeur de la colonne "status_cat".
 * @param {object} rowModified - La ligne modifiée.
 * @returns {object} - La ligne modifiée avec l'identifiant de statut réaffecté.
 */
function reassignationIdStatus(rowModified) {
  // Recherche l'objet correspondant au titre de statut dans le tableau statusCat
  const newStatus = statusCat.find((el) => el.title === rowModified.status_cat);

  // Réaffecte l'identifiant de statut à la propriété "lead_status" de la ligne modifiée
  rowModified.lead_status = newStatus.id;

  // Retourne la ligne modifiée avec l'identifiant de statut réaffecté
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
 * Crée un bouton "Go to Management Page" et l'ajoute au document.
 */
function createManagementButton() {
  const buttonArea = document.getElementById("buttonArea");
  const managementButton = document.createElement("button");
  managementButton.id = "bouttonManagement";
  managementButton.textContent = "Go to Management Page";
  managementButton.style.padding = "10px";
  managementButton.style.margin = "5px";
  managementButton.style.backgroundColor = "#49a1ff";
  managementButton.style.color = "#fff";
  managementButton.style.border = "none";
  managementButton.style.borderRadius = "5px";
  managementButton.style.cursor = "pointer";

  managementButton.addEventListener("click", function () {
    load_manually_reseller_category();
    removeButton("bouttonManagement");
    removeButton("bouttonSave");
    createResetPageButton();
  });

  buttonArea.appendChild(managementButton);
}
/**
 * Crée un bouton "Go to Set Category" et l'ajoute au document.
 */
function createResetPageButton() {
  const buttonArea = document.getElementById("buttonArea");
  const resetButton = document.createElement("button");
  resetButton.id = "buttonReset";
  resetButton.textContent = "Go to Set Category";
  resetButton.style.padding = "10px";
  resetButton.style.margin = "5px";
  resetButton.style.backgroundColor = "#49a1ff";
  resetButton.style.color = "#fff";
  resetButton.style.border = "none";
  resetButton.style.borderRadius = "5px";
  resetButton.style.cursor = "pointer";

  resetButton.addEventListener("click", function () {
    location.reload(); // Recharge la page pour réinitialiser son état par défaut
  });

  buttonArea.appendChild(resetButton);
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
    if (el.id_cat_automatica !== null) {
      el.title_cat = categories[el.id_cat_automatica].title;
      el.id_cat = categories[el.id_cat_automatica].id;
    } else if (el.id_cat !== null) {
      el.title_cat = categories[el.id_cat].title;
    }
  });
}

/**
 * Met à jour le statut des données en fonction de la valeur de lead_status.
 * @param {Object} data - Les données à mettre à jour.
 */
function setStatus(data) {
  const status = data.status;
  const data_tmp = data.data;

  /**
   * Met à jour le statut d'un élément en fonction de sa valeur de lead_status.
   * @param {Object} el - L'élément à mettre à jour.
   */
  function updateStatus(el) {
    el.status_cat = status.find((sta) => sta.id == el.lead_status).title;
    el.lead_status = status.find((sta) => sta.id == el.lead_status).id;
  }

  data_tmp.forEach((el) => {
    switch (el.lead_status) {
      case "1": {
        updateStatus(el);
        break;
      }
      case "2": {
        updateStatus(el);
        break;
      }
      case "3": {
        updateStatus(el);
        break;
      }
      default: {
        console.log(el);
        updateStatus(el);
        break;
      }
    }
  });
}

/**
 * Envoie les modifications au backend pour sauvegarde.
 */
function saveChangesToBackend() {
  var data_tmp = list_modified_row;
  const php_user = null;
  if (document.cookie.match(/PHP_USER/) === null) {
    console.log(document.cookie.match(/PHP_USER/));
    createNotification(`Please log in!`);
    document.cookie = "";
    document.location.reload();
    return false;
  }
  {
    const php_user = document.cookie.match(/PHP_USER/);
  }
  fetch("backend.php?page=index", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ modifiedData: data_tmp, initiator: php_user }),
  })
    .then((response) => response.json())
    .then((result) => {
      list_modified_row = [];
      console.debug(result); // Affiche la réponse du backend
      result.messages.map((el) => createNotification(el.message));
      majDataFront("index");
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
 * Envoie les modifications automatiquement au backend pour sauvegarde.
 * @param {Object} data - Les données modifiées à envoyer.
 */
function saveChangesToBackendAuto(data) {
  const php_user = null;
  if (document.cookie.match(/PHP_USER/) === null) {
    console.log(document.cookie.match(/PHP_USER/));
    createNotification(`Please log in!`);
    document.cookie = "";
    document.location.reload();
    return false;
  }
  {
    const php_user = document.cookie.match(/PHP_USER/);
  }
  fetch("backend.php?page=management", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ modifiedData: data_tmp, initiator: php_user }),
  })
    .then((response) => response.json())
    .then((result) => {
      console.debug(result); // Affiche la réponse du backend
      result.messages.map((el) => createNotification(el.message));
      majDataFront("management");
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
 * Met à jour les données côté front-end en récupérant les données depuis le backend.
 * @param {string} mode - Le mode de mise à jour des données ("index" ou "management").
 */
function majDataFront(mode) {
  fetch("backend.php?page=" + mode)
    .then((response) => response.json())
    .then((data) => {
      // Met à jour les catégories
      setCategory(data);

      // Ajoute une colonne d'options pour "title_cat" avec les catégories
      addOptionColumn("title_cat", data.category);

      // Met à jour le statut si le mode est "management" et ajoute une colonne d'options pour "status_cat"
      if (mode === "management") {
        setStatus(data);
        addOptionColumn("status_cat", data.status);
      }

      // Met à jour les données de la grille
      gridOptions.api.setRowData(data.data);

      // Ajuste la taille des colonnes
      autoSizeAll();

      // Ferme le panneau d'outils de la grille
      gridOptions.api.closeToolPanel();
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération des données:", error);
      createNotification(
        `Erreur lors de la récupération des données: ${error}`
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

/**
 * Charge manuellement les catégories de revendeur depuis le backend et effectue des opérations supplémentaires.
 */
function load_manually_reseller_category() {
  fetch("backend.php?page=management")
    .then((response) => response.json())
    .then((data) => {
      // Désactive l'édition de la colonne "title_cat"
      editColumnProperties("title_cat", {
        editable: false,
      });

      // Ajoute une nouvelle colonne "status_cat" avec les propriétés spécifiées
      addColumn({
        field: "status_cat",
        headerName: "Status Choice",
        editable: true,
        pinned: "right",
        cellEditor: "agRichSelectCellEditor",
        cellEditorParams: {
          values: [],
        },
        cellStyle: function getStatusCellStyle(params) {
          const status = params.value;

          if (status === "open") {
            return { backgroundColor: "lightgreen", color: "black" };
          } else if (status === "working") {
            return { backgroundColor: "orange", color: "black" };
          } else if (status === "completed") {
            return { backgroundColor: "lightgray", color: "black" };
          }

          // Style par défaut pour les autres valeurs
          return null;
        },
      });

      // Met à jour les catégories
      setCategory(data);

      // Met à jour le statut
      setStatus(data);

      // Stocke les statuts dans une variable
      statusCat = data.status;

      // Ajoute une colonne d'options pour "status_cat" avec les statuts disponibles
      addOptionColumn("status_cat", data.status);

      // Met à jour les définitions de colonnes de la grille
      gridOptions.api.setColumnDefs(gridOptions.columnDefs);

      // Met à jour les données de la grille
      gridOptions.api.setRowData(data.data);

      // Ajuste la taille des colonnes
      autoSizeAll();

      // Ferme le panneau d'outils de la grille
      gridOptions.api.closeToolPanel();
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération des données:", error);
      createNotification(
        `Erreur lors de la récupération des données: ${error}`
      );
      const body = document.querySelector("body");
      const out = document.createElement("div");
      out.innerHTML = error;
      body.appendChild(out);
    });
}

/**
 * Modifie les propriétés d'une colonne spécifiée dans la grille.
 * @param {string} nomColonne - Le nom de la colonne à modifier.
 * @param {object} nouvellesProprietes - Les nouvelles propriétés à appliquer à la colonne.
 */
function editColumnProperties(nomColonne, nouvellesProprietes) {
  // Récupérer les définitions de colonnes actuelles
  var columnDefs = gridOptions.api.getColumnDefs();

  // Trouver la définition de colonne correspondante
  var colonne = columnDefs.find(function (col) {
    return col.field === nomColonne;
  });

  // Vérifier si la colonne existe
  if (colonne) {
    // Appliquer les nouvelles propriétés à la colonne
    Object.assign(colonne, nouvellesProprietes);

    // Mettre à jour les définitions de colonnes dans AG Grid
    gridOptions.api.setColumnDefs(columnDefs);
  }
}

/**
 * Ajoute une nouvelle colonne à la grille.
 * @param {object} nouvelleColonne - La définition de la nouvelle colonne à ajouter.
 */
function addColumn(nouvelleColonne) {
  // Récupérer les définitions de colonnes actuelles
  var columnDefs = gridOptions.api.getColumnDefs();

  // Ajouter la nouvelle colonne à la liste des colonnes existantes
  columnDefs.push(nouvelleColonne);

  // Mettre à jour les définitions de colonnes dans AG Grid
  gridOptions.api.setColumnDefs(columnDefs);
}

/**
 * Ajoute une colonne d'options à une colonne spécifiée dans la grille.
 * @param {string} columnName - Le nom de la colonne à laquelle ajouter les options.
 * @param {Array} arrayOption - Le tableau d'options à ajouter.
 */
function addOptionColumn(columnName, arrayOption) {
  // Créer un tableau pour stocker les options
  let cat_tab = [];

  // Parcourir le tableau d'options et extraire les titres
  arrayOption.map((el) => {
    cat_tab.push(el.title);
  });

  // Trouver la définition de colonne correspondante dans les définitions de colonnes de la grille
  var columnDef = gridOptions.columnDefs.find(
    (colDef) => colDef.field === columnName
  );

  // Vérifier si la colonne existe
  if (columnDef) {
    // Mettre à jour les valeurs des paramètres de l'éditeur de cellules pour inclure les options
    columnDef.cellEditorParams.values = cat_tab;
  }
}

function removeButton(id) {
  if (document.getElementById(id)) {
    const element = document.getElementById(id);
    element.parentNode.removeChild(element);
    return true;
  } else {
    return false;
  }
}
