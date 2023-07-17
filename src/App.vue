<template>
  <el-container id="app">
    <el-header>
      <NavBarLayout title="Mon Titre">
        <RouterLink to="/">Home</RouterLink> |
        <RouterLink to="/login">Login</RouterLink>
        <RouterLink to="/settings">Settings</RouterLink>
      </NavBarLayout>
    </el-header>

    <el-main>
      <RouterView />
    </el-main>
  </el-container>
</template> 

<script lang="ts" setup>
import { onMounted } from "vue";
import { RouterLink, RouterView } from "vue-router";
import NavBarLayout from "./components/layout/NavBarLayout.vue";
import { useUserStore } from "./store/user";

const userStore = useUserStore();

onMounted(() => {
	const isAuth = userStore.checkAuthenticated();
	userStore.setAuthenticated(isAuth);
});
</script>

<style>
#app {
  text-align: center;
}
</style>
