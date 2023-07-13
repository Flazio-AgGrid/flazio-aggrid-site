export interface RowData {
  id: string;
  AccountID: string;
  TaxRegID: string;
  Nome: string;
  Cognome: string;
  RagioneSociale: string;
  Email: string;
  Indirizzo: string;
  Comune: string;
  CAP: string;
  Provincia: string;
  Tel: string;
  NumMobile: string;
  NumPagineDaAttivare: string;
  NumPagineAttivate: string;
  status: string;
  create_dt: string;
  update_dt: string;
  readed: string;
  reseller_experience_manager_id: string | null;
  CAOName: string;
  id_cat: string | null;
  id_cat_automatica: string | null;
  title_cat?: string;
  lead_status_cat?: string | number;
  title_status_cat?: string;
}

export interface ColumnDef {
  field: string;
  filter?: string;
  headerName?: string;
  pinned?: string;
  editable?: boolean;
  cellEditor?: string;
  cellEditorParams?: {
    values: string[];
  };
  hide?: boolean;
  cellStyle?: { backgroundColor: string; color: string } | null;
}

export interface CategoryData {
  id: string;
  title: string;
}

export interface StatusData {
  id: string;
  title: string;
}
