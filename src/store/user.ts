import { defineStore } from "pinia";
import { useStorage } from "@vueuse/core";
import User from "../models/Auth.models";
import AuthServices from "../services/auth";
import { showNotification } from "../utils";

export const useUserStore = defineStore("user", {
  state: () => ({
    authenticated: false,
    user: useStorage("user", {} as User),
  }),
  getters: {
    isAuthenticated: (state) => state.authenticated,
    getUser: (state) => state.user,
  },
  actions: {
    setAuthenticated(authenticated: boolean) {
      this.authenticated = authenticated;
    },
    setUser(user: User) {
      this.user = user;
    },
    checkAuthenticated() {
      if (this.user.id) {
        return true;
      } else {
        return false;
      }
    },
    async login(user: User): Promise<boolean> {
      try {
        const result = await AuthServices.login(user);
        console.log(result);
        if (result.result) {
          console.debug();
          showNotification("Success", result.message, "success");
          this.user = result.user || ({} as User);
          this.authenticated = true;
          return true;
        } else {
          console.error(result.message);
          showNotification("Failed", result.message, "error");
          return false;
        }
      } catch {
        return false;
      }
    },

    async logout(): Promise<boolean> {
      try {
        const result = await AuthServices.logout(this.user);
        if (result.result) {
          console.debug(result.message);
          this.user = {} as User;
          this.authenticated = false;
          showNotification("Success", result.message, "success");
          return true;
        } else {
          console.error(result.message);
          showNotification("Failed", result.message, "error");
          return false;
        }
      } catch {
        return false;
      }
    },

    async register(user: User): Promise<boolean> {
      try {
        const result = await AuthServices.register(user);
        if (result.result) {
          showNotification("Success", result.message, "success");
          return true;
        } else {
          console.error(result.message);
          showNotification("Failed", result.message, "error");
          return false;
        }
      } catch {
        return false;
      }
    },
  },
});
