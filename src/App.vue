<template>
  <el-container id="app">
    <el-header>
      <NavBarLayout title="Mon Titre">
        <RouterLink to="/">Home</RouterLink> |
        <RouterLink to="/login">Login</RouterLink>
      </NavBarLayout>
    </el-header>

    <el-main>
      <RouterView />
    </el-main>
  </el-container>
</template>

<script lang="ts" setup>
import { RouterLink, RouterView } from "vue-router";
import NavBarLayout from "./components/layout/NavBarLayout.vue";
</script>

<style>
#app {
  text-align: center;
}
</style>
