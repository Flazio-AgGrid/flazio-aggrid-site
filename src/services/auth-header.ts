import User from "@/models/Auth.models";

export default function authHeader() {
  const user: User | null = JSON.parse(localStorage.getItem("user") || "");

  if (user && user.token) {
    return { Authorization: "Bearer " + user.token };
  } else {
    return {};
  }
}
