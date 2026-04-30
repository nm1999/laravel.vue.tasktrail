<template>
    <div class="space-y-4">
        <!-- Notifications Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900">
                Notifications
                <span v-if="unreadCount > 0" class="ml-2 inline-flex items-center justify-center rounded-full bg-red-500 px-2 py-0.5 text-xs font-bold text-white">
                    {{ unreadCount }}
                </span>
            </h2>
            <button
                v-if="allNotifications.length > 0"
                @click="handleMarkAllAsRead"
                class="text-sm text-blue-600 hover:text-blue-700 font-medium"
            >
                Mark all as read
            </button>
        </div>

        <!-- Toast container for real-time notifications -->
        <TransitionGroup name="toast" tag="div" class="space-y-2 mb-4">
            <div
                v-for="toast in toastQueue"
                :key="toast.id"
                class="flex items-start gap-3 rounded-lg border-l-4 border-blue-500 bg-blue-50 p-4 shadow-md"
            >
                <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-blue-900">{{ toast.title }}</p>
                    <p class="text-sm text-blue-700">{{ toast.message }}</p>
                </div>
                <button @click="dismissToast(toast.id)" class="text-blue-400 hover:text-blue-600">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </TransitionGroup>

        <!-- Notifications List -->
        <div v-if="allNotifications.length > 0" class="space-y-3">
            <div
                v-for="notification in allNotifications"
                :key="notification.id"
                :class="[
                    'bg-white rounded-lg p-4 border-l-4 transition-colors',
                    notification.is_read ? 'border-gray-200 bg-gray-50' : 'border-blue-500'
                ]"
            >
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="font-medium text-gray-900">{{ notification.title }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ notification.message }}</p>
                        <div class="mt-2 flex items-center gap-3">
                            <span class="text-xs text-gray-500">{{ formatDate(notification.created_at) }}</span>
                            <button
                                v-if="!notification.is_read"
                                @click="handleMarkAsRead(notification.id)"
                                class="text-xs text-blue-600 hover:text-blue-700 font-medium"
                            >
                                Mark as read
                            </button>
                            <a
                                v-if="notification.link"
                                :href="notification.link"
                                class="text-xs text-blue-600 hover:text-blue-700 font-medium"
                            >
                                View task →
                            </a>
                        </div>
                    </div>
                    <button
                        @click="handleDismiss(notification.id)"
                        class="text-gray-400 hover:text-gray-600 ml-4"
                    >
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
import { ref, computed } from "vue";
import { useNotifications } from "@/composables/useNotifications.js";

const props = defineProps({
    notifications: {
        type: Array,
        default: () => [],
    },
    unreadCount: {
        type: Number,
        default: 0,
    },
});

// Seed from server-side props
const serverNotifications = ref(props.notifications.map((n) => ({ ...n })));

// Toast queue for real-time notifications (temporary banners)
const toastQueue = ref([]);
let toastCounter = 0;

const dismissToast = (id) => {
    toastQueue.value = toastQueue.value.filter((t) => t.id !== id);
};

const pushToast = (title, message) => {
    const id = `toast-${++toastCounter}`;
    toastQueue.value.unshift({ id, title, message });
    setTimeout(() => dismissToast(id), 8000);
};

const { notifications: realtimeNotifications, unreadCount, markAsRead, markAllAsRead, dismissNotification } = useNotifications({
    onTaskCreated: (event) => {
        pushToast('New Task Assigned', `You have been assigned a new task: "${event.task.title}"`);
    },
    onTaskAssigned: (event) => {
        pushToast('Task Assigned to You', `You have been assigned to the task: "${event.task.title}"`);
    },
    onTaskStatusChanged: (event) => {
        pushToast('Task Progress Updated', `"${event.task.title}" was updated to ${event.new_status}`);
    },
});

// Initialize unread count from server-side data
unreadCount.value = props.unreadCount;

// Merge: server notifications + live real-time ones (avoid duplicates by id)
const allNotifications = computed(() => {
    const seen = new Set(serverNotifications.value.map((n) => n.id));
    const live = realtimeNotifications.value.filter((n) => !seen.has(n.id));
    return [...live, ...serverNotifications.value];
});

const handleMarkAsRead = async (id) => {
    await markAsRead(id);
    const local = serverNotifications.value.find((n) => n.id === id);
    if (local) local.is_read = true;
};

const handleMarkAllAsRead = async () => {
    await markAllAsRead();
    serverNotifications.value.forEach((n) => (n.is_read = true));
};

const handleDismiss = async (id) => {
    await dismissNotification(id);
    serverNotifications.value = serverNotifications.value.filter((n) => n.id !== id);
};

const formatDate = (value) => {
    if (!value) return '';
    return new Date(value).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.4s ease;
}
.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
