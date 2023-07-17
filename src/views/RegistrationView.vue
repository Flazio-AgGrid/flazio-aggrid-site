<template>
  <TextWelcomeLayout>
    <el-form @submit.native.prevent="register" label-width="120px">
      <el-form-item label="Username">
        <el-input v-model="username" required></el-input>
      </el-form-item>
      <el-form-item label="Password">
        <el-input v-model="password" type="password" required></el-input>
      </el-form-item>
      <el-button type="primary" native-type="submit">Register</el-button>
      <router-link to="/login" style="margin-left: 12px">
        <el-button> Login </el-button>
      </router-link>
    </el-form>
  </TextWelcomeLayout>
</template>

<script lang="ts">
import TextWelcomeLayout from "../components/layout/TextWelcomeLayout.vue";
import { defineComponent, ref } from "vue";
import { useRouter } from "vue-router";

export default defineComponent({
  name: "RegistrationView",
  setup() {
    const router = useRouter();

    const username = ref("");
    const password = ref("");

    const register = () => {
      // Envoyer une requête POST pour l'inscription
      fetch("../backend/auth/backend.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          username: username.value,
          password: password.value,
          operation: "register",
        }),
      })
        .then((response) => {
          if (response.ok) {
            // Inscription réussie
            router.push({ name: "login" });
          } else {
            // Erreur lors de l'inscription
            console.log("Erreur lors de l'inscription");
          }
        })
        .catch((error) => {
          console.log("Erreur lors de la requête d'inscription :", error);
        });
    };

    return {
      TextWelcomeLayout,
      username,
      password,
      register,
    };
  },
});
</script>
