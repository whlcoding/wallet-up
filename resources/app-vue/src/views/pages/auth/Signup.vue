<script>

import axios from "axios";

export default {
    name: "Signup",
    data() {
        return {
            firstName: '',
            lastName: '',
            email: '',
            password: '',
            passwordConfirmation: ''
        }
    },
    methods: {
        save() {
            axios.post('http://localhost:8000/api/v1/signup', {
                first_name: this.firstName,
                last_name: this.lastName,
                email: this.email,
                password: this.password,
                password_confirmation: this.passwordConfirmation
            }).then((response) => {
                // let data = response.data;
                this.$toast.add({ severity: 'success', summary: 'Success!', detail: 'Your account has been created successfully. You can now log in.', life: 2000 });
                setTimeout(() => {
                    this.$router.push({ name: 'login' });
                }, 2000);
            }).catch((error) => {
                let data = error.response.data;
                let errors = data.errors;
                if (errors) {
                    errors.forEach(error => {
                        this.$toast.add({ severity: 'error', summary: 'Error!', detail: error, life: 3000 });
                    })
                    return;
                }
                this.$toast.add({ severity: 'error', summary: 'Error!', detail: 'An error occurred while creating your account. Please try again.', life: 3000});
            });
        }
    }
}
</script>

<template>
    <div class="surface-ground flex align-items-center justify-content-center min-h-screen min-w-screen overflow-hidden">
        <Toast />
        <div class="flex flex-column align-items-center justify-content-center">
            <img src="/layout/images/logo-dark.svg" alt="Sakai logo" class="mb-5 w-6rem flex-shrink-0" />
            <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full surface-card py-8 px-5 sm:px-8" style="border-radius: 53px">
                    <div class="text-center mb-5">
                        <img src="/demo/images/login/avatar.png" alt="Image" height="56" class="mb-3" />
                        <div class="text-900 text-3xl font-medium mb-3">Welcome!</div>
                        <span class="text-600 font-medium">Complete the form below to create your account</span>
                    </div>

                    <form>
                        <label for="first-name" class="block text-900 text-lg font-medium mb-2">First Name</label>
                        <InputText id="first-name" type="text" placeholder="First Name" class="w-full md:w-30rem mb-5" style="padding: 1rem" v-model="firstName" />

                        <label for="last-name" class="block text-900 text-lg font-medium mb-2">Last Name</label>
                        <InputText id="last-name" type="text" placeholder="Last Name" class="w-full md:w-30rem mb-5" style="padding: 1rem" v-model="lastName" />

                        <label for="email" class="block text-900 text-lg font-medium mb-2">Email</label>
                        <InputText id="email" type="text" placeholder="Email address" class="w-full md:w-30rem mb-5" style="padding: 1rem" v-model="email" />

                        <label for="new-password" class="block text-900 font-medium text-lg mb-2">Password</label>
                        <Password inputId="new-password" v-model="password" placeholder="Password" :toggleMask="true" class="w-full mb-3" inputClass="w-full" :inputStyle="{padding: '1rem'}"></Password>
                        <Password inputId="confirm-password" v-model="passwordConfirmation" placeholder="Confirm Password" :toggleMask="true" class="w-full mb-5" :feedback="false" inputClass="w-full" :inputStyle="{padding: '1rem' }"></Password>

                        <div class="flex align-items-center justify-content-between mb-5 gap-5">
                            <router-link to="/auth/login" class="font-medium no-underline ml-2 mt-2 text-right cursor-pointer" style="color: var(--primary-color)">Already have an account? Sign In</router-link>
                        </div>
                        <Button label="Create Account" class="w-full p-3 text-xl" @click="save"></Button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">

</style>
