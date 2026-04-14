<template>
    <div class="space-y-4">
        <!-- Notifications Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Notifications</h2>
            <button v-if="notifications.length > 0" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                Mark all as read
            </button>
        </div>

        <!-- Notifications List -->
        <div v-if="notifications.length > 0" class="space-y-3">
            <div
                v-for="notification in notifications"
                :key="notification.id"
                :class="[
                    'bg-white rounded-lg p-4 border-l-4 transition-colors',
                    notification.read ? 'border-gray-200 bg-gray-50' : 'border-blue-500'
                ]"
            >
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="font-medium text-gray-900">{{ notification.title }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ notification.message }}</p>
                        <span class="text-xs text-gray-500 mt-2 block">{{ notification.created_at }}</span>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600 ml-4">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-lg p-12 text-center">
            <svg class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No notifications</h3>
            <p class="text-gray-600">You're all caught up! Check back later for updates.</p>
        </div>
    </div>
</template>

<script setup>
import { reactive } from "vue";

const notifications = reactive([
    {
        id: 1,
        title: "Task Assigned",
        message: "You have been assigned to 'Mobile app design'",
        created_at: "2 hours ago",
        read: false,
    },
    {
        id: 2,
        title: "Task Completed",
        message: "Your task 'Setup environment' has been marked as complete",
        created_at: "5 hours ago",
        read: false,
    },
    {
        id: 3,
        title: "Comment Added",
        message: "Team member commented on your task 'Wireframes'",
        created_at: "1 day ago",
        read: true,
    },
]);
</script>
