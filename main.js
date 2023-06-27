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

function reassignationIdCat(rowModified) {
  const newIdCat = category.find((el) => el.title === rowModified.title_cat);
  rowModified.id_cat = newIdCat.id;
  return rowModified;
}

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

function autoSizeAll(skipHeader) {
  const allColumnIds = [];
  gridOptions.columnApi.getColumns().forEach((column) => {
    allColumnIds.push(column.getId());
  });

  gridOptions.columnApi.autoSizeColumns(allColumnIds, skipHeader);
}

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

function saveChangesToBackend() {
  var data_tmp = list_modified_row;
  for (let i in data_tmp) {
    const modifiedData = data_tmp[i];
    console.log("Données modifiées :", modifiedData);
    fetch("backend.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ modifiedData: modifiedData }),
    })
      .then((response) => response.text())
      .then((result) => {
        console.log(result); // Afficher la réponse du backend
      })
      .catch((error) => {
        console.error("Erreur lors de l'envoi des données au backend:", error);
        const body = document.querySelector("body");
        const out = document.createElement("div");
        out.innerHTML = error;
        body.appendChild(out);
      });
  }
}
