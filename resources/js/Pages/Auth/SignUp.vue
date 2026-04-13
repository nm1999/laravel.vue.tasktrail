<template>
    <div
        class="min-h-screen bg-gray-100 text-gray-900 flex items-center justify-center px-4 py-12"
    >
        <div
            class="w-full max-w-[440px] rounded-3xl border border-gray-200 bg-white p-8 shadow-2xl shadow-gray-900/20 backdrop-blur-xl"
        >
            <div class="mb-8 text-center">
                <p class="text-sm uppercase tracking-[0.32em] text-gray-600">
                    Get Started
                </p>
                <h1
                    class="mt-4 text-3xl font-semibold tracking-tight text-gray-900"
                >
                    Create your account
                </h1>
                <p class="mt-3 text-sm text-gray-600">
                    Join TaskTrail and start managing your work.
                </p>
            </div>

            <!-- Error Messages -->
            <div
                v-if="Object.keys(errors).length"
                class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 space-y-1"
            >
                <p
                    v-for="(msgs, field) in errors"
                    :key="field"
                    class="text-sm text-red-600"
                >
                    {{ Array.isArray(msgs) ? msgs[0] : msgs }}
                </p>
            </div>

            <form class="space-y-5" autocomplete="off" @submit.prevent="signUp">
                <!-- First name + Surname -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                            for="firstname"
                            >First name</label
                        >
                        <input
                            id="firstname"
                            v-model="form.firstname"
                            type="text"
                            placeholder="Jane"
                            class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                            required
                        />
                    </div>
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                            for="surname"
                            >Surname</label
                        >
                        <input
                            id="surname"
                            v-model="form.surname"
                            type="text"
                            placeholder="Doe"
                            class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                            required
                        />
                    </div>
                </div>

                <!-- Email -->
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

                <!-- Department -->
                <div>
                    <label
                        class="mb-2 block text-sm font-medium text-gray-700"
                        for="department"
                        >Department
                        <span class="text-gray-400 font-normal">(optional)</span></label
                    >
                    <input
                        id="department"
                        v-model="form.department"
                        type="text"
                        placeholder="e.g. Engineering"
                        class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                    />
                </div>

                <!-- Password -->
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
                        placeholder="Min. 8 characters"
                        class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        required
                    />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label
                        class="mb-2 block text-sm font-medium text-gray-700"
                        for="password_confirmation"
                        >Confirm password</label
                    >
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        placeholder="Re-enter your password"
                        class="w-full rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        required
                    />
                </div>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-xl shadow-blue-600/10 transition hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="loading">Creating account...</span>
                    <span v-else>Create account</span>
                </button>
            </form>

            <div
                class="mt-8 border-t border-gray-300 pt-6 text-center text-sm text-gray-600"
            >
                <p>
                    Already have an account?
                    <a
                        href="/login"
                        class="font-medium text-blue-600 hover:text-blue-800"
                        >Sign in</a
                    >
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const form = reactive({
    firstname: '',
    surname: '',
    email: '',
    department: '',
    password: '',
    password_confirmation: '',
});

const loading = ref(false);
const errors = ref({});

function signUp() {
    loading.value = true;
    errors.value = {};

    router.post('/register', form, {
        onError: (err) => {
            errors.value = err;
        },
        onFinish: () => {
            loading.value = false;
        },
    });
}
</script>
