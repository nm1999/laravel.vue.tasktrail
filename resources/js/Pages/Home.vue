<template>
    <div
        class="min-h-screen bg-gray-100 text-gray-900 flex items-center justify-center px-4 py-12"
    >
        <div
            class="w-full max-w-[400px] rounded-3xl border border-gray-200 bg-white p-8 shadow-2xl shadow-gray-900/20 backdrop-blur-xl"
        >
            <div class="mb-8 text-center">
                <p class="text-sm uppercase tracking-[0.32em] text-gray-600">
                    Welcome Back
                </p>
                <h1
                    class="mt-4 text-3xl font-semibold tracking-tight text-gray-900"
                >
                    Sign in to your account
                </h1>
                <p class="mt-3 text-sm text-gray-600">
                    Enter your credentials below to continue to TaskTrail.
                </p>
            </div>

            <!-- Error Messages -->
            <div v-if="errors.email || errors.password" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p v-if="errors.email" class="text-sm text-red-600">{{ errors.email[0] }}</p>
                <p v-if="errors.password" class="text-sm text-red-600">{{ errors.password[0] }}</p>
            </div>

            <form class="space-y-6" autocomplete="off" @submit.prevent="signIn()">
                <div>
                    <label
                        class="mb-2 block text-sm font-medium text-gray-700"
                        for="email"
                        >Email address</label
                    >
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        placeholder="you@example.com"
                        class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        required
                    />
                </div>

                <div>
                    <label
                        class="mb-2 block text-sm font-medium text-gray-700"
                        for="password"
                        >Password</label
                    >
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        placeholder=""
                        class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        required
                    />
                </div>

                <div
                    class="flex items-center justify-between text-sm text-gray-600"
                >
                    <label class="flex items-center gap-2">
                        <input
                            v-model="form.remember"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 bg-gray-50 text-blue-600 focus:ring-blue-500/60"
                        />
                        Remember me
                    </label>
                    <button
                        type="button"
                        class="font-medium text-blue-600 hover:text-blue-800"
                    >
                        Forgot password?
                    </button>
                </div>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-xl shadow-blue-600/10 transition hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="loading">Signing in...</span>
                    <span v-else>Sign in</span>
                </button>
            </form>

            <div
                class="mt-8 border-t border-gray-300 pt-6 text-center text-sm text-gray-600"
            >
                <p>
                    New to TaskTrail?
                    <button
                        type="button"
                        class="font-medium text-blue-600 hover:text-blue-800"
                    >
                        Create an account
                    </button>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from "@inertiajs/vue3";

const form = reactive({
    email: '',
    password: '',
    remember: false,
});

const loading = ref(false);
const errors = ref({});

async function signIn() {
    loading.value = true;
    errors.value = {};

    // navigate to admmin index page
    await router.get('/admin');


    // try {
    //     await router.post('/login', form, {
    //         onError: (err) => {
    //             errors.value = err;
    //         },
    //         onSuccess: () => {
    //             // Redirect will be handled by the controller
    //         }
    //     });
    // } catch (error) {
    //     console.error('Login error:', error);
    // } finally {
    //     loading.value = false;
    // }
}
</script>
