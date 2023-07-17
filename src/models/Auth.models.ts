export default interface User {
  id?: number;
  username: string;
  password: string;
  token?: string | null;
  status?: string;
  lastconnection?: string;
  role?: string;
}
