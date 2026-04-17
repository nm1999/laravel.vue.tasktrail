<!-- Full Employee Dashboard Layout with Sidebar and Top Bar -->
<template>
    <div class="flex h-screen bg-gradient-to-br from-gray-100 to-purple-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white p-5 shadow-lg">
            <h2 class="text-xl font-bold mb-6">🌍 TaskTrail</h2>

            <nav class="space-y-2 mb-4">
                <Link
                    href="/employee/dashboard"
                    class="flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100"
                    :class="isCurrent('/employee/dashboard') ? 'bg-gray-100 text-gray-900' : ''"
                >
                    <span>Home</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9" />
                    </svg>
                </Link>
                <Link
                    href="/employee/tasks"
                    class="flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100"
                    :class="isCurrent('/employee/tasks') ? 'bg-gray-100 text-gray-900' : ''"
                >
                    <span>My Tasks</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </Link>
                <Link
                    href="/employee/notifications"
                    class="flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100"
                    :class="isCurrent('/employee/notifications') ? 'bg-gray-100 text-gray-900' : ''"
                >
                    <span>Notifications</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </Link>

                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm font-medium text-gray-700 transition hover:bg-red-50 hover:text-red-600"
                >
                    <span>Logout</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </Link>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col">
            <!-- Top Bar -->
            <div class="bg-white shadow-md px-6 py-4 flex items-center justify-between">
                <div class="flex-1">
                    <h1 class="text-lg font-semibold text-gray-900">{{ getPageTitle() }}</h1>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-gray-700">{{ page.props.auth.user.firstname }}</span>
                    
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 p-6 overflow-auto">
                <component 
                    :is="currentComponent" 
                    :user="page.props.auth.user"
                    :stats="page.props.stats"
                    :recentTasks="page.props.recentTasks"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import HomePage from "./HomePage.vue";
import TasksPage from "./TasksPage.vue";
import NotificationsPage from "./NotificationsPage.vue";

const page = usePage();

const components = {
    HomePage,
    TasksPage,
    NotificationsPage,
};

const currentComponent = computed(() => {
    const url = page.url;
    if (url === '/employee/tasks' || url.startsWith('/employee/tasks?')) {
        return components.TasksPage;
    }
    if (url === '/employee/notifications' || url.startsWith('/employee/notifications?')) {
        return components.NotificationsPage;
    }
    return components.HomePage;
});

const isCurrent = (path) => page.url === path || page.url.startsWith(path + '?');

const getPageTitle = () => {
    const url = page.url;
    if (url === '/employee/dashboard' || url.startsWith('/employee/dashboard?')) return 'Home';
    if (url === '/employee/tasks' || url.startsWith('/employee/tasks?')) return 'My Tasks';
    if (url === '/employee/notifications' || url.startsWith('/employee/notifications?')) return 'Notifications';
    return 'Dashboard';
};
</script>
