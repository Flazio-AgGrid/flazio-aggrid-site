<!-- eslint-disable vue/attribute-hyphenation -->
<template>
  <div>
    <h1>Board</h1>
    <ag-grid-vue
      class="ag-theme-alpine fullscreen"
      :columnDefs="columnDefs"
      :rowData="rowData"
      singleClickEdit="true"
      stopEditingWhenCellsLoseFocus="true"
      enterNavigatesVerticallyAfterEdit="true"
      rowSelection="multiple"
    >
    </ag-grid-vue>
  </div>
</template>

<script lang="ts">
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
      dataStore.updateIdCat();
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
