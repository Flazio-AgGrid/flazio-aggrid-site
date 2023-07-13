<!-- eslint-disable vue/attribute-hyphenation -->
<template>
  <TextWelcomeLayout>
    <ag-grid-vue
      class="fullscreen"
      :class="theme"
      :columnDefs="columnDefs"
      :rowData="rowData"
      :gridOptions="gridOptions"
      singleClickEdit="true"
      stopEditingWhenCellsLoseFocus="true"
      enterNavigatesVerticallyAfterEdit="true"
      rowSelection="multiple"
      @cellValueChanged="onCellValueChanged"
    >
    </ag-grid-vue>
    <div id="buttonArea" style="position: fixed; bottom: 10px; right: 20px">
      <el-button type="primary" @click="load">Load status</el-button>
      <el-button
        type="success"
        v-if="modeStatusEdit && changeRowData.length > 0"
      >
        Save change
      </el-button>
    </div>
  </TextWelcomeLayout>
</template>

<script lang="ts">
import TextWelcomeLayout from "@/components/layout/TextWelcomeLayout.vue";
import { RowData, ColumnDef } from "@/models/DataStore.models";
import { AgGridVue } from "ag-grid-vue3";
import { useDataStore } from "@/store/data";
import { onMounted, ref, watch, Ref } from "vue";
import { CellValueChangedEvent, GridApi, ColumnApi } from "ag-grid-community";
import { isDark } from "@/utils";

export default {
  name: "App",
  components: {
    AgGridVue,
  },
  setup() {
    const dataStore = useDataStore();
    const columnDefs: Ref<ColumnDef[]> = ref(dataStore.getColumnDefs);
    const rowData: Ref<RowData[]> = ref(dataStore.getRowData);
    const changeRowData: Ref<RowData[]> = ref(dataStore.getChangeRowData);
    const modeStatusEdit: Ref<boolean> = ref(true);
    const gridOptions = {
      defaultColDef: {
        enableValue: true,
        enableRowGroup: true,
        enablePivot: true,
        sortable: true,
        filter: true,
        resizable: true,
      },
      sideBar: true,
      api: new GridApi(),
      columnApi: new ColumnApi(),
    };

    const theme: Ref<string> = ref("ag-theme-alpine");

    const autoSizeAll = (skipHeader = false) => {
      gridOptions.columnApi.autoSizeAllColumns(skipHeader);
      gridOptions.api.closeToolPanel();
    };

    onMounted(() => {
      dataStore.setCellEditorParams();
      dataStore.updateIdCat();
      dataStore.updateStatusCat();
      autoSizeAll();
      updateTheme(isDark.value); // Appel initial pour définir le thème
    });

    const onCellValueChanged = (params: CellValueChangedEvent) => {
      dataStore.setChangeRowData(params.data);
      autoSizeAll();
    };

    const load = () => {
      dataStore.setColumnDefStatusHide();
      modeStatusEdit.value = !modeStatusEdit.value;
      autoSizeAll();
    };
    /*
     *   Theme sombre Ag-Grid
     */
    watch(isDark, (newVal) => {
      updateTheme(newVal); // Mettre à jour le thème en fonction de la nouvelle valeur de isDark
    });

    const updateTheme = (darkMode: boolean) => {
      theme.value = darkMode ? "ag-theme-alpine-dark" : "ag-theme-alpine";
    };

    return {
      TextWelcomeLayout,
      columnDefs,
      rowData,
      changeRowData,
      modeStatusEdit,
      gridOptions,
      onCellValueChanged,
      theme,
      isDark,
      load,
    };
  },
};
</script>

<style scoped>
.fullscreen {
  height: 75vh;
}
</style>
