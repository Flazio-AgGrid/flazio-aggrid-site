<template>
  <el-menu
    mode="horizontal"
    router
    :ellipsis="false"
    :default-active="router.currentRoute.value.path"
  >
    <p style="margin-right: 20px">{{ title }}</p>
    <el-menu-item index="/">Home</el-menu-item>
    <el-menu-item v-if="isAuth" index="/board">Finance</el-menu-item>
    <el-menu-item v-if="isAuth" index="/settings">Settings</el-menu-item>
    <div class="flex-grow" />
    <el-menu-item @click="toggleD">
      <el-icon v-if="isDark"><Sunny /></el-icon>
      <el-icon v-else><Moon /></el-icon>
      {{ isDark ? "Light" : "Dark" }}
    </el-menu-item>
    <el-menu-item index="/login">{{
      isAuth ? "Logout" : "Login"
    }}</el-menu-item>
  </el-menu>
</template>

<script lang="ts">
import { defineComponent, ref, watch } from "vue";
import { useUserStore } from "../../store/user";
import { toggleDark, isDark } from "../../utils/index";
import { useRouter } from "vue-router";

export default defineComponent({
  name: "NavBarLayout",
  props: {
    title: {
      type: String,
      default: "MonTitre",
    },
  },
  setup() {
    const userStore = useUserStore();
    const isAuth = ref(userStore.isAuthenticated);
    const router = useRouter();

    const toggleD = () => {
      toggleDark();
    };

    // Observer les changements de la variable isAuth
    watch(
      () => userStore.isAuthenticated,
      (newValue) => {
        isAuth.value = newValue;
      }
    );

    return {
      isAuth,
      isDark,
      toggleD,
      router,
    };
  },
});
</script>

<style scoped>
.flex-grow {
  flex-grow: 1;
}
</style>
