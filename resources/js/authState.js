import { reactive } from 'vue';
import axios from 'axios';

export const authState = reactive({
    isAuth: false,
    isAdmin: false,
    isSuperAdmin: false,
    user: null,

    checkAuth() {
        this.isAuth = !!localStorage.getItem('token');
        this.isAdmin = localStorage.getItem('is_admin') === 'true';
        this.isSuperAdmin = localStorage.getItem('is_super_admin') === 'true';
        
        try {
            const userStr = localStorage.getItem('user');
            this.user = userStr ? JSON.parse(userStr) : null;
        } catch (e) {
            console.error('Error parsing user from localStorage', e);
            this.user = null;
        }

        if (this.isAuth) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`;
        } else {
            delete axios.defaults.headers.common['Authorization'];
        }
    },

    login(token, user, isAdmin = false, isSuperAdmin = false) {
        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(user));
        localStorage.setItem('is_admin', isAdmin ? 'true' : 'false');
        localStorage.setItem('is_super_admin', isSuperAdmin ? 'true' : 'false');
        
        this.checkAuth();
    },

    logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        localStorage.removeItem('is_admin');
        localStorage.removeItem('is_super_admin');
        localStorage.removeItem('theme'); // Optional, depending on preference
        
        this.checkAuth();
    }
});

// Initialize on load
authState.checkAuth();
