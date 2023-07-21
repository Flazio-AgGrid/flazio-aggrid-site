export default class HistoryResellers {
  constructor(data) {
    this.data = data;
  }

  createGrid() {
    this.createDialog();
    var gridOptions = {
      columnDefs: this.getColumnDefs(),
      defaultColDef: {
        enableValue: true,
        enableRowGroup: true,
        enablePivot: true,
        sortable: true,
        filter: true,
        resizable: true,
      },
    };

    new agGrid.Grid(document.querySelector("#historyGrid"), gridOptions);

    gridOptions.api.setRowData(this.data);
    this.autoSizeAll(gridOptions);
  }

  createDialog() {
    const dialogElement = document.getElementById("dialog");
    if (dialogElement) {
      dialogElement.showModal();
      return false;
    } else {
      const dialog = document.createElement("dialog");
      dialog.id = "dialog";
      dialog.style.border = "none";
      dialog.style.borderRadius = "5px";

      // Utiliser Flexbox pour créer une mise en page flexible
      dialog.style.display = "flex";
      dialog.style.flexDirection = "column";
      dialog.style.alignItems = "center";
      dialog.style.justifyContent = "center";
      dialog.style.width = "calc(75% - 70px)";
      dialog.style.height = "calc(100% - 70px)";
      dialog.style.padding = "35px";
      dialog.style.boxSizing = "border-box";

      const divAgGrid = document.createElement("div");
      divAgGrid.id = "historyGrid";
      divAgGrid.className = "ag-theme-alpine";
      divAgGrid.style.width = "100%";
      divAgGrid.style.height = "100%";

      const closeButton = document.createElement("button");
      closeButton.textContent = "Close";
      closeButton.style.padding = "10px";
      closeButton.style.margin = "5px";
      closeButton.style.backgroundColor = "#007bff";
      closeButton.style.color = "#fff";
      closeButton.style.border = "none";
      closeButton.style.borderRadius = "5px";
      closeButton.style.cursor = "pointer";

      closeButton.addEventListener("mouseover", function () {
        closeButton.style.backgroundColor = "#004FA3";
      });

      closeButton.addEventListener("mouseout", function () {
        closeButton.style.backgroundColor = "#007bff";
      });

      closeButton.addEventListener("click", function () {
        const element = document.getElementById("dialog");
        element.parentNode.removeChild(element);
        dialog.close();
      });

      dialog.appendChild(divAgGrid);
      dialog.appendChild(closeButton);
      document.body.appendChild(dialog);
      dialog.showModal();
    }
  }
  getColumnDefs() {
    return [
      { headerName: "ID", field: "id" },
      { headerName: "Initiator", field: "username" },
      { headerName: "ID Reseller", field: "objectToLog" },
      { headerName: "Status", field: "status" },
      { headerName: "Date", field: "dateTime" },
      { headerName: "Old Value", field: "oldData" },
      { headerName: "New Value", field: "newData" },
    ];
  }
  /**
   * Redimensionne automatiquement toutes les colonnes de la grille.
   * @param {boolean} [skipHeader=false] - Indique si l'en-tête doit être exclu du redimensionnement.
   */
  autoSizeAll(gridOptions, skipHeader) {
    const allColumnIds = [];
    gridOptions.columnApi.getColumns().forEach((column) => {
      allColumnIds.push(column.getId());
    });
    console.log(allColumnIds);
    gridOptions.columnApi.autoSizeColumns(allColumnIds, skipHeader);
  }
}
