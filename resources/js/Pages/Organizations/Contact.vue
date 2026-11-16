<template>
    <div class="min-h-screen bg-gray-100 px-4 py-12 text-gray-900">
        <div class="mx-auto w-full max-w-xl rounded-3xl border border-gray-200 bg-white p-8 shadow-xl">
            <div class="mb-8 text-center">
                <p class="text-sm uppercase tracking-[0.32em] text-gray-600">Get in touch</p>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight">Contact an organisation</h1>
                <p class="mt-3 text-sm text-gray-600">
                    Enter the organisation ID they shared with you and your message will be sent to their team.
                </p>
            </div>

            <p v-if="flash?.success" class="mb-5 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                {{ flash.success }}
            </p>
            <div v-if="Object.keys(errors).length" class="mb-5 rounded-lg border border-red-200 bg-red-50 p-3">
                <p v-for="(messages, field) in errors" :key="field" class="text-sm text-red-600">
                    {{ Array.isArray(messages) ? messages[0] : messages }}
                </p>
            </div>

            <form class="space-y-5" @submit.prevent="submit">
                <div>
                    <label class="mb-2 block text-sm font-medium" for="organization_id">Organisation ID</label>
                    <input id="organization_id" v-model="form.organization_id" required class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium" for="name">Your name</label>
                    <input id="name" v-model="form.name" required maxlength="255" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium" for="email">Your email</label>
                    <input id="email" v-model="form.email" type="email" required maxlength="255" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium" for="message">Message</label>
                    <textarea id="message" v-model="form.message" required maxlength="5000" rows="5" class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm" />
                </div>
                <button type="submit" :disabled="processing" class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50">
                    {{ processing ? "Sending..." : "Send message" }}
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-600">
                <Link href="/" class="font-medium text-blue-600 hover:text-blue-800">Return to sign in</Link>
            </p>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

const form = reactive({ organization_id: "", name: "", email: "", message: "" });
const errors = ref({});
const processing = ref(false);
const flash = usePage().props.flash;

function submit() {
    processing.value = true;
    errors.value = {};

    router.post("/organizations/contact", form, {
        onError: (validationErrors) => {
            errors.value = validationErrors;
        },
        onFinish: () => {
            processing.value = false;
        },
        onSuccess: () => {
            form.name = "";
            form.email = "";
            form.message = "";
        },
    });
}
</script>
