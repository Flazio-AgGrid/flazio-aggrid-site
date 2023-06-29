import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import ElementPlus from "element-plus";
//import "./element-variables.scss";
<<<<<<< HEAD
import "ag-grid-community/styles/ag-grid.css";
import "ag-grid-community/styles/ag-theme-alpine.css";
=======
>>>>>>> 44afbda6e94f1d74e741c9e698115c8774aea110
import { createPinia } from "pinia";

createApp(App).use(createPinia()).use(router).use(ElementPlus).mount("#app");
