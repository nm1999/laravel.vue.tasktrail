<template>
    <h2 class="text-xl font-semibold mb-6">Ojede Eric</h2>
    <h6 class="text-sm">Admin Dashboard</h6>

    <input
        type="text"
        placeholder="Search task..."
        class="w-full mb-4 px-3 py-2 border rounded-lg"
    />

    <nav class="space-y-2">
        <p class="text-gray-500 text-sm">Main Menu</p>
        <Link
            href="/admin/dashboard"
            class="block p-2 rounded hover:bg-gray-100"
            >Dashboard</Link
        >
        <Link
            href="/admin/notifications"
            class="block p-2 rounded hover:bg-gray-100"
            >Notifications</Link
        >
        <Link
            href="/admin/tasks"
            class="block p-2 rounded bg-blue-100 text-blue-600"
            >Tasks</Link
        >


    </nav>

    <div class="mt-6">
        <Link href="/admin/settings" class="block p-2 rounded hover:bg-gray-100">Settings</Link>
        <Link class="block p-2 rounded hover:bg-gray-100">Help</Link>
    </div>

   
</template>
<script setup>
import { Link, usePage } from "@inertiajs/vue3";
</script>
