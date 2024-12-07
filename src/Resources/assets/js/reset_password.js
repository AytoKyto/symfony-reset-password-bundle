import { createApp } from 'vue'
import RequestResetPassword from './components/RequestResetPassword.vue'
import ResetPassword from './components/ResetPassword.vue'

// Initialiser le composant de demande de réinitialisation
const requestElement = document.getElementById('reset-password-request')
if (requestElement) {
    createApp(RequestResetPassword).mount('#reset-password-request')
}

// Initialiser le composant de réinitialisation
const resetElement = document.getElementById('reset-password')
if (resetElement) {
    const token = resetElement.dataset.token
    createApp(ResetPassword, { token }).mount('#reset-password')
}
