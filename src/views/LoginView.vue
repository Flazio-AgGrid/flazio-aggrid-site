<template>
  <TextWelcomeLayout>
    <el-form @submit.native.prevent="login" label-width="120px">
      <el-form-item label="Username">
        <el-input v-model="username" required></el-input>
      </el-form-item>
      <el-form-item label="Password">
        <el-input v-model="password" type="password" required></el-input>
      </el-form-item>
      <el-button type="primary" native-type="submit">Login</el-button>
      <router-link to="/registration" style="margin-left: 12px">
        <el-button> Register </el-button>
      </router-link>
    </el-form>
  </TextWelcomeLayout>
</template>

<script lang="ts">
import TextWelcomeLayout from "@/components/layout/TextWelcomeLayout.vue";
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
            userStore.setUsername(username.value);
            router.push({ name: "board" });
          } else {
            // Authentification échouée
            userStore.setAuthenticated(true);
            userStore.setUsername(username.value);
            router.push({ name: "board" });
            console.log("Erreur d'authentification");
          }
        })
        .catch((error) => {
          console.log("Erreur lors de la requête d'authentification :", error);
        });
    };

    return {
      TextWelcomeLayout,
      username,
      password,
      login,
    };
  },
});
</script>
