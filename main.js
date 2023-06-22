const gridOptions = {
  columnDefs: [
    { field: 'id', filter: 'agTextColumnFilter' },
    { field: 'AccountID' },
    { field: 'TaxRegID', minWidth: 180 },
    { field: 'Nome', minWidth: 180 },
    { field: 'Cognome', minWidth: 150 },
    { field: 'RagioneSociale', minWidth: 250 },
    { field: 'Email' },
    { field: 'Indirizzo', minWidth: 250 },
    { field: 'Comune' },
    { field: 'CAP', },
    { field: 'Provincia' },
    { field: 'Tel', minWidth: 180 },
    { field: 'NumMobile' },
    { field: 'NumPagineDaAttivare', minWidth: 150 },
    { field: 'NumPagineAttivate' },
    { field: 'status' },
    { field: 'create_dt', minWidth: 200 },
    { field: 'update_dt', minWidth: 200 },
    { field: 'readed' },
    { field: 'reseller_experience_manager_id' },
    { field: 'CAOName' },
  ],
  defaultColDef: {
    flex: 1,
    minWidth: 100,
    enableValue: true,
    enableRowGroup: true,
    enablePivot: true,
    sortable: true,
    filter: true,
    resizable: true,
  },
  autoGroupColumnDef: {
    minWidth: 200,
  },
  sideBar: true,
};



document.addEventListener('DOMContentLoaded', function () {
  var gridDiv = document.querySelector('#myGrid');
  new agGrid.Grid(gridDiv, gridOptions);

  // fetch('data.json')
  //   .then((response) => response.json())
  //   .then((data) => gridOptions.api.setRowData(data));
  fetch('backend.php')
    .then((response) => response.json())
    .then((data) => gridOptions.api.setRowData(data));
});
