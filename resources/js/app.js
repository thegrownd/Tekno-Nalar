import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';

import App from './components/App.vue';
import Articles from './components/Articles.vue';

// Lazy load components for better performance
const Login = () => import('./components/Login.vue');
const Register = () => import('./components/Register.vue');
const ArticleForm = () => import('./components/ArticleForm.vue');
const ArticleDetail = () => import('./components/ArticleDetail.vue');
const About = () => import('./components/About.vue');
const AdminLogin = () => import('./components/AdminLogin.vue');
const NewsPage = () => import('./components/NewsPage.vue');
const MyArticles = () => import('./components/MyArticles.vue');
const PendingArticles = () => import('./components/PendingArticles.vue');
const UserManagement = () => import('./components/UserManagement.vue');
const GoogleCallback = () => import('./components/GoogleCallback.vue');
const NewsletterPage = () => import('./components/NewsletterPage.vue');

axios.defaults.baseURL = '/api';
axios.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', component: Articles },
        { path: '/about', component: About },
        { path: '/news', component: NewsPage },
        { path: '/login', component: Login },
        { path: '/register', component: Register },
        { path: '/articles/new', component: ArticleForm, meta: { requiresAuth: true } },
        { path: '/articles/:id/edit', component: ArticleForm, props: true, meta: { requiresAuth: true } },
        { path: '/articles/:id', component: ArticleDetail, props: true },
        { path: '/admin-login', component: AdminLogin },
        { path: '/my-articles', component: MyArticles, meta: { requiresAuth: true } },
        { path: '/admin/pending-articles', component: PendingArticles, meta: { requiresAuth: true, requiresAdmin: true } },
        { path: '/admin/users', component: UserManagement, meta: { requiresAuth: true, requiresAdmin: true } },
        { path: '/auth-google-callback', component: GoogleCallback },
        { path: '/newsletter', component: NewsletterPage },
    ],
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    const isAdmin = localStorage.getItem('is_admin') === 'true';

    // Check for admin routes
    if (to.meta.requiresAdmin) {
        if (!token) {
            return next('/admin-login');
        }
        if (!isAdmin) {
            // User logged in but not admin
            return next('/login');
        }
    }

    // Check for auth routes
    if (to.meta.requiresAuth && !token) {
        return next('/login');
    }

    next();
});

createApp(App).use(router).mount('#app');
