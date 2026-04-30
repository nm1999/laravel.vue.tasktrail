<template>
    <div class="flex h-screen bg-gray-100 dark:bg-gray-900 transition-colors">
        <!-- Sidebar -->
        <aside class="w-64 bg-white dark:bg-gray-800 shadow-md p-4">
            <SideBar />
        </aside>
        <main class="flex-1 p-6 overflow-auto">
            <NotificationCenter
                :notifications="page.props.notifications?.data ?? []"
                :unread-count="page.props.unreadCount ?? 0"
            />
        </main>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import SideBar from '../SideBar.vue';
import NotificationCenter from '@/Components/NotificationCenter.vue';

const page = usePage();
</script>
