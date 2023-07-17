import User from "./Auth.models";
import { RowData } from "./DataStore.models";

export default interface ResponseDefault {
  result: boolean | RowData[];
  message: string;
  user?: User;
}

export interface ErrorResponse {
  message: string;
  result: boolean;
  err: object;
  // autres propriétés si présentes dans l'objet d'erreur
}
