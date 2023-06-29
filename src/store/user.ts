import { defineStore } from "pinia";
<<<<<<< HEAD
import { useStorage } from "@vueuse/core";

export const useUserStore = defineStore("user", {
  state: () => ({
    authenticated: useStorage("authenticated", false),
    username: useStorage("username", ""),
  }),
  getters: {
    isAuthenticated: (state) => state.authenticated,
    getUsername: (state) => state.username,
=======

export const useUserStore = defineStore("user", {
  state: () => ({
    authenticated: false,
  }),
  getters: {
    isAuthenticated: (state) => state.authenticated,
>>>>>>> 44afbda6e94f1d74e741c9e698115c8774aea110
  },
  actions: {
    setAuthenticated(authenticated: boolean) {
      this.authenticated = authenticated;
    },
<<<<<<< HEAD
    setUsername(username: string) {
      this.username = username;
    },
=======
>>>>>>> 44afbda6e94f1d74e741c9e698115c8774aea110
  },
});
