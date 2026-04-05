<template>
    <div
        class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 text-slate-100 flex items-center justify-center px-4 py-12"
    >
        <div
            class="w-full max-w-[400px] rounded-3xl border border-white/10 bg-slate-950/90 p-8 shadow-2xl shadow-slate-950/20 backdrop-blur-xl"
        >
            <div class="mb-8 text-center">
                <p class="text-sm uppercase tracking-[0.32em] text-slate-500">
                    Welcome Back
                </p>
                <h1
                    class="mt-4 text-3xl font-semibold tracking-tight text-white"
                >
                    Sign in to your account
                </h1>
                <p class="mt-3 text-sm text-slate-400">
                    Enter your credentials below to continue to TaskTrail.
                </p>
            </div>

            <form class="space-y-6" autocomplete="off" @submit.prevent="signIn()">
                <div>
                    <label
                        class="mb-2 block text-sm font-medium text-slate-300"
                        for="email"
                        >Email address</label
                    >
                    <input
                        id="email"
                        type="email"
                        placeholder="you@example.com"
                        class="w-full rounded-2xl border border-slate-700/90 bg-slate-900 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-500/20"
                    />
                </div>

                <div>
                    <label
                        class="mb-2 block text-sm font-medium text-slate-300"
                        for="password"
                        >Password</label
                    >
                    <input
                        id="password"
                        type="password"
                        placeholder=""
                        class="w-full rounded-2xl border border-slate-700/90 bg-slate-900 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-500/20"
                    />
                </div>

                <div
                    class="flex items-center justify-between text-sm text-slate-400"
                >
                    <label class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-sky-500 focus:ring-sky-500/60"
                        />
                        Remember me
                    </label>
                    <button
                        type="button"
                        class="font-medium text-sky-400 hover:text-sky-300"
                    >
                        Forgot password?
                    </button>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-2xl bg-gradient-to-r from-sky-500 to-cyan-400 px-4 py-3 text-sm font-semibold text-slate-950 shadow-xl shadow-cyan-500/10 transition hover:brightness-110"
                >
                    Sign in
                </button>
            </form>

            <div
                class="mt-8 border-t border-slate-700/60 pt-6 text-center text-sm text-slate-500"
            >
                <p>
                    New to TaskTrail?
                    <button
                        type="button"
                        class="font-medium text-white hover:text-sky-300"
                    >
                        Create an account
                    </button>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { router } from "@inertiajs/vue3";

function signIn() {
    // navigate to admin dashboard
    router.get("/admin/dashboard");
}
</script>
