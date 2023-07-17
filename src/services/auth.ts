/* eslint-disable @typescript-eslint/no-explicit-any */
import User from "@/models/Auth.models";
import ResponseDefault, { ErrorResponse } from "@/models/Response.models";
import axios, { AxiosError } from "axios";
import authHeader from "./auth-header";
import { showNotification } from "@/utils/notif";

const API_URL = "http://localhost:8090/api/auth/";

class AuthServices {
  async login(user: User): Promise<ResponseDefault> {
    try {
      const response = await axios.post(API_URL + "login", {
        user: {
          username: user.username,
          password: user.password,
        },
      });
      return response.data;
    } catch (error: any) {
      this.handleAxiosError(error);
      throw error;
    }
  }

  async logout(user: User): Promise<ResponseDefault> {
    try {
      const response = await axios.post(
        API_URL + "logout",
        {
          user: {
            username: user.username,
          },
        },
        { headers: authHeader() }
      );
      return response.data;
    } catch (error: any) {
      this.handleAxiosError(error);
      throw error;
    }
  }

  async register(user: User): Promise<ResponseDefault> {
    try {
      const response = await axios.post(
        API_URL + "register",
        {
          user: {
            username: user.username,
            password: user.password,
          },
        },
        { headers: authHeader() }
      );
      return response.data;
    } catch (error: any) {
      this.handleAxiosError(error);
      throw error;
    }
  }

  private handleAxiosError(error: AxiosError<ErrorResponse>) {
    if (error.response) {
      const errorMessage = error.response.data.message;
      console.log(error.response);
      console.error(errorMessage);
      // Utiliser la fonction showNotification pour afficher le message d'erreur
      showNotification("Error", errorMessage, "error");
    } else {
      console.error("An unknown error occurred.");
      // Utiliser la fonction showNotification pour afficher le message d'erreur
      showNotification("Error", "An unknown error occurred.", "error");
    }
  }
}

export default new AuthServices();
