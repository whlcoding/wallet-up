<script setup>
import { useLayout } from '@/layout/composables/layout';
import { ref, computed } from 'vue';
import { useToast } from 'primevue/usetoast';

import { useAuthStore } from '@/stores/authStore';
import { useWalletStore } from '@/stores/walletStore';

import { useRouter } from 'vue-router';

import axios from 'axios';
import { set } from 'lodash';
// import AppConfig from '@/layout/AppConfig.vue';

const { layoutConfig } = useLayout();
const toast = useToast();
const useAuth = useAuthStore();
const useWallet = useWalletStore();
const router = useRouter();
const email = ref('');
const password = ref('');
const checked = ref(false);

const logoUrl = computed(() => {
    return `layout/images/${layoutConfig.darkTheme.value ? 'logo-white' : 'logo-dark'}.svg`;
});

const showSuccess = () => {
    toast.add({ severity: 'success', summary: 'Success!', detail: 'You are successfully logged in.', life: 5000 });
};

const showError = (message) => {
    toast.add({ severity: 'error', summary: 'Error!', detail: message, life: 3000 });
};

const login = async () => {
    axios.post('http://localhost:8000/api/v1/login', {
        email: email.value,
        password: password.value
    }).then((response) => {
        let data = response.data;
        showSuccess();

        useAuth.setAuth(data.token_access, data.user);
        console.log(data.wallets);

        if (data.wallets) {
            useWallet.setWallets(data.wallets);
            useWallet.setSelectedWallet(data.wallets[0]);
        }


        setTimeout(() => {
            router.push({ name: 'dashboard' });
        }, 2000);

    }).catch((error) => {
        console.log(error);
        let data = error.response.data;
        console.log(data)
        if (data?.errors) {
            data.errors.forEach((error) => {
                showError(error);
            });
            return;
        }
        showError('Email or Password is incorrect.');
        console.log(error);
    })
};

</script>

<template>
    <div class="surface-ground flex align-items-center justify-content-center min-h-screen min-w-screen overflow-hidden">
        <Toast />
        <div class="flex flex-column align-items-center justify-content-center">
            <img src="/layout/images/logo-dark.svg" alt="Sakai logo" class="mb-5 w-6rem flex-shrink-0" />
            <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full surface-card py-8 px-5 sm:px-8" style="border-radius: 53px">
                    <div class="text-center mb-5">
                        <img src="/demo/images/login/avatar.png" alt="Image" height="50" class="mb-3" />
                        <div class="text-900 text-3xl font-medium mb-3">Welcome!</div>
                        <span class="text-600 font-medium">Sign in to continue</span>
                    </div>

                    <div>
                        <label for="email1" class="block text-900 text-xl font-medium mb-2">Email</label>
                        <InputText id="email1" type="text" placeholder="Email address" class="w-full md:w-30rem mb-5" style="padding: 1rem" v-model="email" />

                        <label for="password1" class="block text-900 font-medium text-xl mb-2">Password</label>
                        <Password id="password1" v-model="password" placeholder="Password" :toggleMask="true" class="w-full mb-3" inputClass="w-full" inputStyle="padding:1rem"></Password>
                        <!-- <input class="p-inputtext p-component p-password-input w-full" type="password" id="password1" v-model="password" aria-controls="pv_id_11_panel" aria-expanded="false" aria-haspopup="true" placeholder="Password" value="" style="padding: 1rem;"> -->

                        <div class="flex align-items-center justify-content-between mb-5 gap-5">
                            <!-- <div class="flex align-items-center">
                                <Checkbox v-model="checked" id="rememberme1" binary class="mr-2"></Checkbox>
                                <label for="rememberme1">Remember me</label>
                            </div> -->
                            <a class="font-medium no-underline ml-2 mt-2 text-right cursor-pointer" style="color: var(--primary-color)">Forgot password?</a>
                            <router-link to="/auth/signup" class="font-medium no-underline ml-2 mt-2 text-right cursor-pointer" style="color: var(--primary-color)">Create a new account</router-link>
                        </div>
                        <Button label="Sign In" class="w-full p-3 text-xl" @click="login"></Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <AppConfig simple /> -->
</template>

<style scoped>
.pi-eye {
    transform: scale(1.6);
    margin-right: 1rem;
}

.pi-eye-slash {
    transform: scale(1.6);
    margin-right: 1rem;
}
</style>
