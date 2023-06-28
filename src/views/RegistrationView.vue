<template>
  <div>
    <h2>Registration</h2>
    <el-form @submit.native.prevent="register">
      <el-form-item label="Username">
        <el-input v-model="username" required></el-input>
      </el-form-item>
      <el-form-item label="Password">
        <el-input v-model="password" type="password" required></el-input>
      </el-form-item>
      <el-button type="primary" native-type="submit">Register</el-button>
      <router-link to="/login">Login</router-link>
    </el-form>
  </div>
</template>

<script lang="ts">
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
      username,
      password,
      register,
    };
  },
});
</script>
