import User from "@/models/Auth.models";
import ResponseDefault from "@/models/Response.models";
import axios from "axios";
import authHeader from "./auth-header";

const API_URL = "http://localhost:8090/api/auth/";

class AuthServices {
  login(user: User): Promise<ResponseDefault> {
    return axios.post(API_URL + "login", {
      user: {
        username: user.username,
        password: user.password,
      },
    });
  }
  logout(user: User): Promise<ResponseDefault> {
    return axios.post(
      API_URL + "logout",
      {
        user: {
          username: user.username,
        },
      },
      { headers: authHeader() }
    );
  }
  register(user: User): Promise<ResponseDefault> {
    return axios.post(
      API_URL + "register",
      {
        user: {
          username: user.username,
          password: user.password,
        },
      },
      { headers: authHeader() }
    );
  }
}

export default new AuthServices();
