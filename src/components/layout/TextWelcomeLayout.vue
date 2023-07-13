<template>
  <div>
    <h1>
      <el-text type="info" size="large"
        >This is an {{ route.name }} page</el-text
      >
    </h1>
    <p v-if="username">
      {{ isAuth ? "Welcome" : "Goodbye" }}
      <el-tooltip :content="`UserID : 'No userID'`">
        <el-text v-if="username" :type="typeText"> {{ username }}</el-text>
      </el-tooltip>
    </p>
    <slot />
  </div>
</template>
<script lang="ts" setup>
import { useRoute } from "vue-router";
import { useUserStore } from "@/store/user";
import { ref, computed, type Ref } from "vue";

const route = useRoute();
const userStore = useUserStore();
const isAuth: Ref<boolean> = ref(userStore.isAuthenticated);
const username: Ref<string> = ref(userStore.getUsername);
const typeText = computed(() => (isAuth.value ? "success" : "warning"));
</script>
