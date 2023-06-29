<template>
  <div>
    <h2>Home</h2>
<<<<<<< HEAD
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
=======
    <router-link to="/login">Login</router-link>
    <br />
    <router-link to="/registration">Registration</router-link>
>>>>>>> 44afbda6e94f1d74e741c9e698115c8774aea110
  </div>
</template>

<script lang="ts">
<<<<<<< HEAD
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
=======
export default {
  name: "HomeView",
};
>>>>>>> 44afbda6e94f1d74e741c9e698115c8774aea110
</script>
