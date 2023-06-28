<template>
  <div>
    <h2>Login</h2>
    <el-form @submit.native.prevent="login">
      <el-form-item label="Username">
        <el-input v-model="username" required></el-input>
      </el-form-item>
      <el-form-item label="Password">
        <el-input v-model="password" type="password" required></el-input>
      </el-form-item>
      <el-button type="primary" native-type="submit">Login</el-button>
      <router-link to="/registration">Register</router-link>
    </el-form>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";
import { useRouter } from "vue-router";
import { useUserStore } from "../store/user";

export default defineComponent({
  name: "LoginView",
  setup() {
    const router = useRouter();
    const userStore = useUserStore();

    const username = ref("");
    const password = ref("");

    const login = () => {
      // Envoyer une requête POST pour l'authentification
      fetch("../backend/auth/backend.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          username: username.value,
          password: password.value,
          operation: "login",
        }),
      })
        .then((response) => {
          if (response.ok) {
            // Authentification réussie
            userStore.setAuthenticated(true);
            router.push({ name: "board" });
          } else {
            // Authentification échouée
            console.log("Erreur d'authentification");
          }
        })
        .catch((error) => {
          console.log("Erreur lors de la requête d'authentification :", error);
        });
    };

    return {
      username,
      password,
      login,
    };
  },
});
</script>
