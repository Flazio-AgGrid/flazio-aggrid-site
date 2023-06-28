import { defineStore } from "pinia";

export const useUserStore = defineStore("user", {
  state: () => ({
    authenticated: false,
  }),
  getters: {
    isAuthenticated: (state) => state.authenticated,
  },
  actions: {
    setAuthenticated(authenticated: boolean) {
      this.authenticated = authenticated;
    },
  },
});
