<template>
  <div>
    <h1>
      <el-text type="info" size="large"
        >This is an {{ route.name }} page</el-text
      >
    </h1>
    <p v-if="user.id">
      {{ isAuth ? "Welcome" : "Goodbye" }}
      <el-tooltip :content="`UserID : ${user.id}`">
        <el-text v-if="user.username" :type="typeText">
          {{ user.username }}</el-text
        >
      </el-tooltip>
    </p>
    <slot />
  </div>
</template>
<script lang="ts" setup>
import { useRoute } from "vue-router";
import { useUserStore } from "../../store/user";
import { ref, computed, type Ref } from "vue";
import User from "../../models/Auth.models";

const route = useRoute();
const userStore = useUserStore();
const isAuth: Ref<boolean> = ref(userStore.isAuthenticated);
const user: Ref<User> = ref(userStore.getUser);
const typeText = computed(() => (isAuth.value ? "success" : "warning"));
</script>
