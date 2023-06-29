import { defineStore } from "pinia";
import { useStorage } from "@vueuse/core";

export const useUserStore = defineStore("user", {
  state: () => ({
    authenticated: useStorage("authenticated", false),
    username: useStorage("username", ""),
  }),
  getters: {
    isAuthenticated: (state) => state.authenticated,
    getUsername: (state) => state.username,
  },
  actions: {
    setAuthenticated(authenticated: boolean) {
      this.authenticated = authenticated;
    },
    setUsername(username: string) {
      this.username = username;
    },
  },
});
