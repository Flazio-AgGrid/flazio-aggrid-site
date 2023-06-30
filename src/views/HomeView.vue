<template>
  <div>
    <h2>Home</h2>
    <div v-if="isAuthenticated">
      <router-link to="/login">Login</router-link>
      <br />
      <router-link to="/registration">Registration</router-link>
    </div>
    <div v-else>
      <router-link to="/board">Board</router-link>
      <br />
      <router-link to="/login" @click="logout()">Logout</router-link>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { useUserStore } from "../store/user";

export default defineComponent({
  name: "HomeView",
  setup() {
    const userStore = useUserStore();
    const isAuthenticated = userStore.isAuthenticated;

    const logout = () => {
      userStore.setAuthenticated(false);
    };

    return {
      isAuthenticated,
      logout,
    };
  },
});
</script>
