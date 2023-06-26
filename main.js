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
      field: "id_cat",
      headerName: "Category choice",
      pinned: "right",
      editable: true,
      cellEditor: "agRichSelectCellEditor",
      cellEditorPopup: true,
      cellEditorParams: {
        values: categoryValues(),
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

document.addEventListener("DOMContentLoaded", function () {
  var gridDiv = document.querySelector("#myGrid");
  new agGrid.Grid(gridDiv, gridOptions);

  fetch("backend.php")
    .then((response) => response.json())
    .then((data) => {
      setCategory(data);
      gridOptions.api.setRowData(data);
      autoSizeAll();
      gridOptions.api.closeToolPanel();
    });

  // Ajouter un gestionnaire d'événements pour écouter les modifications de cellule
  gridOptions.api.addEventListener("cellValueChanged", function (event) {
    // Récupérer les données modifiées
    var modifiedData = [event.data];
    // Envoyer les données modifiées au backend pour enregistrement
    saveChangesToBackend(modifiedData);
  });
});

function autoSizeAll(skipHeader) {
  const allColumnIds = [];
  gridOptions.columnApi.getColumns().forEach((column) => {
    allColumnIds.push(column.getId());
  });

  gridOptions.columnApi.autoSizeColumns(allColumnIds, skipHeader);
}

function categoryValues() {
  const category = [
    "2 - Personal website or Freelancer",
    "130 - Business",
    "137 - Blog",
    "125 - Services",
    "44 - Booking",
    "68 - Restaurant",
    "8 - Design ",
    "66 - Advertising",
    "1 - Real Estate",
    "37 - Health and Beatuy",
    "78 - Portfolio",
  ];

  return category;
}

function setCategory(data) {
  const category = categoryValues();
  const data_tmp = data;
  data_tmp.map((el) => {
    console.log(el.id_cat_automatica, el.id_cat);
    if (el.id_cat_automatica !== null) {
      el.id_cat = category[el.id_cat_automatica];
    } else if (el.id_cat !== null) {
      el.id_cat = category[el.id_cat];
    }
  });
}

function saveChangesToBackend(data) {
  // Envoyer les données modifiées au backend pour enregistrement
  // Utilisez ici votre logique d'enregistrement des modifications
  var category = categoryValues();
  var data_tmp = data;
  category.map((el) => {
    if (el === data_tmp[0].id_cat) {
      data_tmp[0].id_cat = category.indexOf(el);
    }
  });
  console.log("Données modifiées :", data_tmp);
  fetch("backend.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ modifiedData: data_tmp }),
  })
    .then((response) => response.text())
    .then((result) => {
      console.log(result); // Afficher la réponse du backend
      body = document.querySelector("body");
      out = document.createElement("div");
      out.innerHTML = result;
      body.appendChild(out);
    })
    .catch((error) => {
      console.error("Erreur lors de l'envoi des données au backend:", error);
      body = document.querySelector("body");
      out = document.createElement("div");
      out.innerHTML = error;
      body.appendChild(out);
    });
}
