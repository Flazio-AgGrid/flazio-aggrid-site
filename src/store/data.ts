import {
  RowData,
  ColumnDef,
  CategoryData,
  StatusData,
} from "@/models/DataStore.models";
import { defineStore } from "pinia";
//import { useStorage } from "@vueuse/core";
export type Rootstate = {
  columnDefs: ColumnDef[];
  rowData: RowData[];
  categoryData: CategoryData[];
  changeRowData: RowData[];
  statusData: StatusData[];
};

export const useDataStore = defineStore("data", {
  state: () =>
    ({
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
          cellEditor: "agSelectCellEditor",
          cellEditorParams: {
            values: [],
          },
          hide: false,
        },
        {
          field: "title_status_cat",
          headerName: "Status Choice",
          editable: true,
          pinned: "right",
          cellEditor: "agRichSelectCellEditor",
          cellEditorParams: {
            values: [],
          },
          hide: true,
          cellStyle: (params: { value: never }) => {
            const status = params.value;

            switch (status) {
              case "open":
                return { backgroundColor: "lightgreen", color: "black" };
              case "working":
                return { backgroundColor: "orange", color: "black" };
              case "completed":
                return { backgroundColor: "lightgray", color: "black" };
              default:
                return null; // Valeur par défaut si aucune condition n'est satisfaite
            }
          },
        },
      ],
      rowData: [
        {
          id: "114080",
          AccountID: "2614336",
          TaxRegID: "",
          Nome: "CLAUDIO RICCARDO",
          Cognome: "STAICU",
          RagioneSociale: "STUDIO COPIA & PARTNERS SRL",
          Email: "---",
          Indirizzo: "VIA PASUBIO 10",
          Comune: "SAN BENEDETTO DEL TRONTO",
          CAP: "63039",
          Provincia: "AP",
          Tel: "----",
          NumMobile: "----",
          NumPagineDaAttivare: "5",
          NumPagineAttivate: "0",
          status: "0",
          create_dt: "2023-06-07 09:04:41",
          update_dt: "2023-06-07 09:04:41",
          readed: "0",
          reseller_experience_manager_id: null,
          CAOName: "2",
          id_cat: null,
          id_cat_automatica: null,
        },
        {
          id: "114081",
          AccountID: "1240926",
          TaxRegID: "0000000013680285",
          Nome: "LAROSI",
          Cognome: "LAROSI",
          RagioneSociale: "LAROSI S.A.S. DI LAROSI PIO & C.",
          Email: "---",
          Indirizzo: "VIA MARONCELLI 105/B",
          Comune: "PADOVA",
          CAP: "35129",
          Provincia: "PD",
          Tel: "----",
          NumMobile: "----",
          NumPagineDaAttivare: "3",
          NumPagineAttivate: "0",
          status: "0",
          create_dt: "2023-06-07 09:04:41",
          update_dt: "2023-06-07 09:04:41",
          readed: "0",
          reseller_experience_manager_id: null,
          CAOName: "2",
          id_cat: null,
          id_cat_automatica: null,
        },
        {
          id: "114083",
          AccountID: "2541820",
          TaxRegID: "0000000044290625",
          Nome: "PERLINGIERI",
          Cognome: "ALESSANDRO",
          RagioneSociale: "SOCIETA'' OLEARIA INDUSTRIE AFFINI SRL S.O.I.A.",
          Email: "---",
          Indirizzo: "VIA SCALO FERROVIARIO 66.",
          Comune: "SOLOPACA",
          CAP: "82036",
          Provincia: "BN",
          Tel: "----",
          NumMobile: "----",
          NumPagineDaAttivare: "1",
          NumPagineAttivate: "0",
          status: "0",
          create_dt: "2023-06-07 09:04:41",
          update_dt: "2023-06-07 09:04:41",
          readed: "0",
          reseller_experience_manager_id: null,
          CAOName: "2",
          id_cat: null,
          id_cat_automatica: null,
        },
        {
          id: "114084",
          AccountID: "1013734",
          TaxRegID: "0000000057960569",
          Nome: "ALFREDO",
          Cognome: "ANTONUZZI",
          RagioneSociale: "SOC. AGR. CANTINA OLEIFICIO SOCIALE DI GRADOLI",
          Email: "---",
          Indirizzo: "VIA ROMA 31",
          Comune: "GRADOLI",
          CAP: "01010",
          Provincia: "VT",
          Tel: "----",
          NumMobile: "----",
          NumPagineDaAttivare: "1",
          NumPagineAttivate: "0",
          status: "0",
          create_dt: "2023-06-07 09:04:41",
          update_dt: "2023-06-07 09:04:41",
          readed: "0",
          reseller_experience_manager_id: null,
          CAOName: "2",
          id_cat: null,
          id_cat_automatica: null,
        },
        {
          id: "114085",
          AccountID: "1009283",
          TaxRegID: "0000000059250126",
          Nome: "ALESSANDRO",
          Cognome: "CACCARO",
          RagioneSociale: "IMPRESA EDILE CACCARO DI CACCARO GIULIANO & C. S.R",
          Email: "---",
          Indirizzo: "VIA PASTRENGO N. 12",
          Comune: "SOMMA LOMBARDO",
          CAP: "21019",
          Provincia: "VA",
          Tel: "----",
          NumMobile: "----",
          NumPagineDaAttivare: "2",
          NumPagineAttivate: "0",
          status: "0",
          create_dt: "2023-06-07 09:04:41",
          update_dt: "2023-06-07 09:04:41",
          readed: "0",
          reseller_experience_manager_id: null,
          CAOName: "2",
          id_cat: null,
          id_cat_automatica: null,
        },
        {
          id: "114086",
          AccountID: "2287794",
          TaxRegID: "0000000062360565",
          Nome: "ANDREA",
          Cognome: "ROCCHI",
          RagioneSociale: "GIOACCHINI SANTE SAS DI ANDREA GIOACCHINI",
          Email: "---",
          Indirizzo: "VIA VITTORIO VENETO 108",
          Comune: "GROTTE DI CASTRO",
          CAP: "01025",
          Provincia: "VT",
          Tel: "----",
          NumMobile: "----",
          NumPagineDaAttivare: "4",
          NumPagineAttivate: "0",
          status: "0",
          create_dt: "2023-06-07 09:04:41",
          update_dt: "2023-06-07 09:04:41",
          readed: "0",
          reseller_experience_manager_id: null,
          CAOName: "2",
          id_cat: null,
          id_cat_automatica: null,
        },
        {
          id: "114087",
          AccountID: "1210706",
          TaxRegID: "0000000063150817",
          Nome: "CARLO",
          Cognome: "MONTALTO",
          RagioneSociale: "BAGLIO CURATOLO ARINI 1875 SOCIETA'' A RESPONSABIL",
          Email: "---",
          Indirizzo: "VIA VITO CURATOLO ARINI, 5",
          Comune: "MARSALA",
          CAP: "91025",
          Provincia: "TP",
          Tel: "----",
          NumMobile: "----",
          NumPagineDaAttivare: "8",
          NumPagineAttivate: "0",
          status: "0",
          create_dt: "2023-06-07 09:04:41",
          update_dt: "2023-06-07 09:04:41",
          readed: "0",
          reseller_experience_manager_id: null,
          CAOName: "2",
          id_cat: null,
          id_cat_automatica: null,
        },
        {
          id: "114088",
          AccountID: "2374583",
          TaxRegID: "0000000069150282",
          Nome: "LINO",
          Cognome: "IMPLATINI",
          RagioneSociale: "ACIPADOVA SERVIZI SRL - UNIPERSONALE",
          Email: "---",
          Indirizzo: "VIA SCROVEGNI 21",
          Comune: "PADOVA",
          CAP: "35121",
          Provincia: "PD",
          Tel: "----",
          NumMobile: "----",
          NumPagineDaAttivare: "2",
          NumPagineAttivate: "0",
          status: "0",
          create_dt: "2023-06-07 09:04:41",
          update_dt: "2023-06-07 09:04:41",
          readed: "0",
          reseller_experience_manager_id: null,
          CAOName: "2",
          id_cat: null,
          id_cat_automatica: null,
        },
        {
          id: "114089",
          AccountID: "2122468",
          TaxRegID: "0000000069800399",
          Nome: "CHIARA",
          Cognome: "GHINASSI",
          RagioneSociale: "VETRERIA LANDI S.R.L.",
          Email: "---",
          Indirizzo: "VIA POREC 100",
          Comune: "MASSA LOMBARDA",
          CAP: "48024",
          Provincia: "RA",
          Tel: "----",
          NumMobile: "----",
          NumPagineDaAttivare: "2",
          NumPagineAttivate: "0",
          status: "0",
          create_dt: "2023-06-07 09:04:41",
          update_dt: "2023-06-07 09:04:41",
          readed: "0",
          reseller_experience_manager_id: null,
          CAOName: "2",
          id_cat: null,
          id_cat_automatica: 9,
        },
        {
          id: "114090",
          AccountID: "2173677",
          TaxRegID: "0000000078360674",
          Nome: "MARCO",
          Cognome: "DI SABATINO",
          RagioneSociale: "CONGLOMERATI BITUMINOSI VOMANO DI DI SABATINO OSCA",
          Email: "---",
          Indirizzo: "VIA CESI SNC",
          Comune: "PENNA SANT''ANDREA",
          CAP: "64039",
          Provincia: "TE",
          Tel: "----",
          NumMobile: "----",
          NumPagineDaAttivare: "2",
          NumPagineAttivate: "0",
          status: "0",
          create_dt: "2023-06-07 09:04:41",
          update_dt: "2023-06-07 09:04:41",
          readed: "0",
          reseller_experience_manager_id: null,
          CAOName: "2",
          id_cat: null,
          id_cat_automatica: 5,
        },
      ],
      categoryData: [
        { id: "", title: "" },
        { id: "1", title: "Advertising" },
        { id: "2", title: "Blog" },
        { id: "3", title: "Booking" },
        { id: "4", title: "Business" },
        { id: "5", title: "Design" },
        { id: "6", title: "Health and Beauty" },
        { id: "7", title: "Personal website or Freelancer" },
        { id: "8", title: "Portfolio" },
        { id: "9", title: "Real Estate" },
        { id: "10", title: "Restaurant" },
        { id: "11", title: "Services" },
      ],
      statusData: [
        { id: "", title: "" },
        { id: "1", title: "open" },
        { id: "2", title: "working" },
        { id: "3", title: "completed" },
      ],
      changeRowData: [],
    } as Rootstate),
  getters: {
    getColumnDefs: (state: Rootstate): ColumnDef[] => state.columnDefs,
    getRowData: (state: Rootstate): RowData[] => state.rowData,
    getCategoryData: (state: Rootstate): CategoryData[] => state.categoryData,
    getChangeRowData: (state: Rootstate): RowData[] => state.changeRowData,
  },
  actions: {
    /**
     * Définit les titre des colonnes.
     * @param {ColumnDef[]} columnDefs - Les définitions de colonnes à définir.
     */
    setColumnDefs(columnDefs: ColumnDef[]) {
      this.columnDefs = columnDefs;
    },

    /**
     * Définit les données de chaque lignes.
     * @param {RowData[]} rowData - Les données de lignes à définir.
     */
    setRowData(rowData: RowData[]) {
      this.rowData = rowData;
    },

    /**
     * Définit les données dans la selection des catégories.
     * @param {CategoryData[]} categoryData - Les données de catégories à définir.
     */
    setCategoryData(categoryData: CategoryData[]) {
      this.categoryData = categoryData;
    },

    /**
     * Met à jour les données modifiées dans le tableau changeRowData.
     * Si la ligne existe déjà dans changeRowData, elle est mise à jour.
     * Sinon, la ligne est ajoutée au tableau.
     * @param {RowData} row - La ligne de données à mettre à jour.
     */
    setChangeRowData(row: RowData): boolean {
      try {
        const index = this.changeRowData.findIndex((el) => el.id === row.id);
        if (index >= 0) {
          // Mettre à jour la ligne existante
          this.changeRowData[index] = row;
        } else {
          // Ajouter la nouvelle ligne
          this.changeRowData.push(row);
        }
        return true;
      } catch (error) {
        console.error(error);
        return false;
      }
    },

    /**
     * Définit les paramètres de l'éditeur de cellules pour la colonne "title_cat".
     */
    setCellEditorParams() {
      const titleCategoryData: CategoryData["title"][] = [];
      const titleStatusData: StatusData["title"][] = [];

      this.categoryData.forEach((el: CategoryData) =>
        titleCategoryData.push(el.title)
      );

      this.statusData.forEach((el: StatusData) =>
        titleStatusData.push(el.title)
      );

      this.columnDefs.forEach((columnDef: ColumnDef) => {
        if (columnDef.field === "title_cat" && columnDef.cellEditorParams) {
          columnDef.cellEditorParams.values = titleCategoryData;
        } else if (
          columnDef.field === "title_status_cat" &&
          columnDef.cellEditorParams
        ) {
          columnDef.cellEditorParams.values = titleStatusData;
        }
      });
    },

    /**
     * Met à jour la valeur de la colonne "id_cat" en fonction de la valeur de "id_cat_automatica".
     */
    updateIdCat() {
      const newRowData: RowData[] = this.rowData.map((el: RowData) => {
        if (el.id_cat_automatica) {
          el.id_cat = el.id_cat_automatica;
          const title_cat: CategoryData | undefined = this.categoryData.find(
            (cat: CategoryData) => cat.id === el.id_cat_automatica
          );
          el.title_cat = title_cat?.title || "";
        } else {
          el.id_cat = "";
        }
        return el;
      });
      this.rowData = newRowData;
    },

    updateStatusCat() {
      const newRowData: RowData[] = this.rowData.map((el: RowData) => {
        if (el.lead_status_cat === null) {
          el.lead_status_cat = 1;
        }
        const title_status_cat: StatusData | undefined = this.statusData.find(
          (sta: StatusData) => sta.id === el.lead_status_cat
        );
        el.title_status_cat = title_status_cat?.title || "";
        return el;
      });
      this.rowData = newRowData;
    },

    setColumnDefStatusHide(): boolean {
      const status_cat: ColumnDef | undefined = this.columnDefs.find(
        (el: ColumnDef) => el.field == "title_status_cat"
      );
      const title_cat: ColumnDef | undefined = this.columnDefs.find(
        (el: ColumnDef) => el.field == "title_cat"
      );
      if (status_cat && title_cat) {
        status_cat.hide = !status_cat.hide;
        title_cat.hide = !title_cat.hide;
        return true;
      } else {
        return false;
      }
    },
  },
});
