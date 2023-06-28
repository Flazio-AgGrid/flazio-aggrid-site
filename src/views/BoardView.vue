<template>
  <div>
    <h2>Board</h2>
    <p>Welcome to the secure board!</p>
    <el-button @click="logout">Logout</el-button>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { useRouter } from "vue-router";
import { useUserStore } from "../store/user";

export default defineComponent({
  name: "BoardView",
  setup() {
    const router = useRouter();
    const userStore = useUserStore();

    const logout = () => {
      // Clear user authentication status in the store
      userStore.setAuthenticated(false);

      // Redirect to the login view
      router.push({ name: "login" });
    };

    return {
      logout,
    };
  },
});
</script>
