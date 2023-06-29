<<<<<<< HEAD
<!-- eslint-disable vue/attribute-hyphenation -->
<template>
  <div>
    <h1>Board</h1>
    <ag-grid-vue
      class="ag-theme-alpine fullscreen"
      :columnDefs="columnDefs"
      :rowData="rowData"
    >
    </ag-grid-vue>
=======
<template>
  <div>
    <h2>Board</h2>
    <p>Welcome to the secure board!</p>
    <el-button @click="logout">Logout</el-button>
>>>>>>> 44afbda6e94f1d74e741c9e698115c8774aea110
  </div>
</template>

<script lang="ts">
<<<<<<< HEAD
import { AgGridVue } from "ag-grid-vue3";
import { useDataStore } from "@/store/data";
import { onMounted, ref } from "vue";

export default {
  name: "App",
  components: {
    AgGridVue,
  },
  setup() {
    const dataStore = useDataStore();
    const columnDefs = ref(dataStore.getColumnDefs);
    const rowData = ref(dataStore.getRowData);

    onMounted(() => {
      dataStore.setCellEditorParams();
    });
    return {
      columnDefs,
      rowData,
    };
  },
};
</script>

<style scoped>
.fullscreen {
  height: 90vh;
}
</style>
=======
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
>>>>>>> 44afbda6e94f1d74e741c9e698115c8774aea110
