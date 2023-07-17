<template>
  <TextWelcomeLayout>
    <el-form @submit.native.prevent="handleLoginOrRegister" label-width="120px">
      <el-form-item label="Username" class="input">
        <el-input v-model="username" required></el-input>
      </el-form-item>
      <el-form-item label="Password" class="input">
        <el-input v-model="password" type="password" required></el-input>
      </el-form-item>
      <el-button type="primary" native-type="submit">{{
        isRegister ? "Login" : "Register"
      }}</el-button>
      <el-button @click="switchMode">
        {{ isRegister ? "Register" : "Login" }}
      </el-button>
      <!-- <router-link to="/registration" style="margin-left: 12px">
        <el-button> Register </el-button>
      </router-link> -->
    </el-form>
  </TextWelcomeLayout>
</template>

<script lang="ts">
import TextWelcomeLayout from "@/components/layout/TextWelcomeLayout.vue";
import { defineComponent, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { useUserStore } from "../store/user";
import { showNotification } from "@/utils";

export default defineComponent({
  name: "LoginView",
  setup() {
    const router = useRouter();
    const userStore = useUserStore();

    const username = ref("");
    const password = ref("");
    const isRegister = ref(true);

    onMounted(() => {
      const isAuth = userStore.isAuthenticated;
      if (isAuth) {
        userStore.logout();
      }
    });

    const handleLoginOrRegister = async () => {
      const user = {
        username: username.value,
        password: password.value,
      };

      let result;
      if (isRegister.value) {
        result = await userStore.login(user);
      } else {
        result = await userStore.register(user);
      }

      if (result) {
        router.push("/");
      }
    };

    const switchMode = () => {
      isRegister.value = !isRegister.value;
      showNotification(
        "",
        isRegister.value ? "Login form" : "Registration form",
        "info"
      );
    };

    return {
      TextWelcomeLayout,
      username,
      password,
      handleLoginOrRegister,
      switchMode,
      isRegister,
    };
  },
});
</script>

<style scoped>
.input {
  width: 50%;
  margin-left: 22vw;
  margin-right: 25vw;
}
</style>
