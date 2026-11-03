<template>
    <div class="flex min-h-screen bg-gray-100">
        <aside class="w-64 bg-white p-4 shadow-md"><SideBar /></aside>
        <main class="flex-1 overflow-auto p-6">
            <h1 class="mb-2 text-2xl font-bold text-gray-900">Organisation messages</h1>
            <p class="mb-6 text-sm text-gray-600">Messages sent by people contacting your organisation with its ID.</p>
            <div v-if="flash?.success" class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ flash.success }}</div>
            <div v-if="messages.data.length" class="space-y-4">
                <article v-for="message in messages.data" :key="message.id" class="rounded-xl bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h2 class="font-semibold text-gray-900">{{ message.name }}</h2>
                            <a :href="`mailto:${message.email}`" class="text-sm text-blue-600 hover:underline">{{ message.email }}</a>
                            <p class="mt-1 text-xs text-gray-500">{{ formatDate(message.created_at) }}</p>
                        </div>
                        <button
                            v-if="!message.read_at"
                            type="button"
                            class="rounded-lg border border-blue-200 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-50"
                            @click="markAsRead(message.id)"
                        >
                            Mark as read
                        </button>
                        <span v-else class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-600">Read</span>
                    </div>
                    <p class="mt-4 whitespace-pre-wrap text-sm leading-6 text-gray-700">{{ message.message }}</p>
                </article>
            </div>
            <div v-else class="rounded-xl bg-white p-8 text-center text-sm text-gray-500 shadow-sm">
                No messages yet.
            </div>
            <div v-if="messages.links.length > 3" class="mt-6 flex flex-wrap gap-2">
                <Link
                    v-for="link in messages.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    class="rounded border px-3 py-2 text-sm"
                    :class="{ 'bg-blue-600 text-white': link.active, 'pointer-events-none opacity-50': !link.url }"
                    v-html="link.label"
                />
            </div>
        </main>
    </div>
</template>

<script setup>
import { Link, router, usePage } from "@inertiajs/vue3";
import SideBar from "../SideBar.vue";

defineProps({ messages: { type: Object, required: true } });
const flash = usePage().props.flash;

function markAsRead(id) {
    router.patch(`/admin/contact-messages/${id}/read`, {}, { preserveScroll: true });
}

function formatDate(value) {
    return new Date(value).toLocaleString();
}
</script>
