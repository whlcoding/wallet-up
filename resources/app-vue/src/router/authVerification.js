import { useAuthStore } from '../stores/authStore'

export default function authVerification(to, from, next) {
    if (to.meta?.auth && !useAuthStore().isAuthenticated) {
        next('auth/login')
    } else {
        next()
    }
}
