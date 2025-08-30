import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: localStorage.getItem('token') || null,
        user: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null,
    }),

    getters: {
        isAuthenticated: (state) => {
            return state.token !== null;
        },
    },

    actions: {
        setAuth(token, user) {
            this.token = token;
            this.user = user;
            localStorage.setItem('token', token);
            localStorage.setItem('user', JSON.stringify(user));
        },

        clearAuth() {
            this.token = null;
            this.user = null;
            localStorage.removeItem('token');
            localStorage.removeItem('user');
        }
    },
})
