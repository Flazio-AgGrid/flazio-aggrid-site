import { createRouter, createWebHistory, RouteRecordRaw } from "vue-router";
import HomeView from "../views/HomeView.vue";
import LoginView from "../views/LoginView.vue";
import RegistrationView from "../views/RegistrationView.vue";
import BoardView from "../views/BoardView.vue";
import { useUserStore } from "@/store/user";

const routes: Array<RouteRecordRaw> = [
  {
    path: "/",
    name: "home",
    component: HomeView,
    meta: { requiresAuth: false }, // Pas besoin d'authentification pour cette page
  },
  {
    path: "/login",
    name: "login",
    component: LoginView,
    meta: { requiresAuth: false }, // Pas besoin d'authentification pour cette page
  },
  {
    path: "/registration",
    name: "registration",
    component: RegistrationView,
    meta: { requiresAuth: false }, // Pas besoin d'authentification pour cette page
  },
  {
    path: "/board",
    name: "board",
    component: BoardView,
    meta: { requiresAuth: true }, // Authentification requise pour cette page
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

// Vérifier si l'utilisateur est authentifié avant de naviguer vers une page protégée
router.beforeEach((to, from, next) => {
  const userStore = useUserStore();
  if (to.meta.requiresAuth && !userStore.isAuthenticated) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas authentifié
    next({ name: "login" });
  } else {
    next();
  }
});

export default router;
