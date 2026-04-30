<template>
    <div class="relative" ref="bellRef">
        <!-- Bell Button -->
        <button
            @click="toggleDropdown"
            class="relative p-2 rounded-lg text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors focus:outline-none"
            aria-label="Notifications"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                />
            </svg>
            <span
                v-if="unreadCount > 0"
                class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center min-w-[1.1rem] h-[1.1rem] rounded-full bg-red-500 text-white text-[10px] font-bold leading-none px-1"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown Panel -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="isOpen"
                class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden"
            >
                <!-- Header -->
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">
                        Notifications
                        <span
                            v-if="unreadCount > 0"
                            class="ml-1.5 inline-flex items-center justify-center rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white"
                        >
                            {{ unreadCount }}
                        </span>
                    </h3>
                    <button
                        v-if="unreadCount > 0"
                        @click="handleMarkAllAsRead"
                        class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium"
                    >
                        Mark all read
                    </button>
                </div>

                <!-- Real-time toast notifications -->
                <TransitionGroup name="toast" tag="div" class="px-3 pt-2 space-y-1">
                    <div
                        v-for="toast in toastQueue"
                        :key="toast.id"
                        class="flex items-start gap-2 rounded-lg border-l-4 border-blue-500 bg-blue-50 dark:bg-blue-900/30 px-3 py-2 text-xs"
                    >
                        <span class="mt-0.5 text-blue-500 flex-shrink-0">🔔</span>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-blue-900 dark:text-blue-200 truncate">{{ toast.title }}</p>
                            <p class="text-blue-700 dark:text-blue-300 truncate">{{ toast.message }}</p>
                        </div>
                        <button @click="dismissToast(toast.id)" class="text-blue-400 hover:text-blue-600 flex-shrink-0">✕</button>
                    </div>
                </TransitionGroup>

                <!-- Notifications List -->
                <div class="max-h-72 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                    <template v-if="recentNotifications.length > 0">
                        <div
                            v-for="notification in recentNotifications"
                            :key="notification.id"
                            :class="[
                                'px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-default',
                                !notification.is_read ? 'bg-blue-50 dark:bg-blue-900/20' : ''
                            ]"
                        >
                            <div class="flex items-start gap-2">
                                <span class="text-sm flex-shrink-0 mt-0.5">{{ getIcon(notification.type) }}</span>
                                <div class="flex-1 min-w-0">
                                    <p
                                        :class="[
                                            'text-xs font-medium truncate',
                                            !notification.is_read ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300'
                                        ]"
                                    >
                                        {{ notification.title }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">{{ notification.message }}</p>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">{{ formatDate(notification.created_at) }}</p>
                                </div>
                                <div v-if="!notification.is_read" class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1"></div>
                            </div>
                        </div>
                    </template>
                    <div v-else class="px-4 py-8 text-center">
                        <svg class="h-8 w-8 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <p class="text-xs text-gray-500 dark:text-gray-400">No notifications</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-2.5">
                    <a
                        :href="notificationsUrl"
                        class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium"
                    >
                        View all notifications →
                    </a>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useNotifications } from '@/composables/useNotifications.js';

const props = defineProps({
    notificationsUrl: {
        type: String,
        default: '/admin/notifications',
    },
    initialUnreadCount: {
        type: Number,
        default: 0,
    },
});

const isOpen = ref(false);
const bellRef = ref(null);

const toastQueue = ref([]);
let toastCounter = 0;

const dismissToast = (id) => {
    toastQueue.value = toastQueue.value.filter((t) => t.id !== id);
};

const pushToast = (title, message) => {
    const id = `bell-toast-${++toastCounter}`;
    toastQueue.value.unshift({ id, title, message });
    setTimeout(() => dismissToast(id), 6000);
};

const { notifications, unreadCount, markAllAsRead, fetchUnreadCount } = useNotifications({
    onTaskCreated: (event) => {
        pushToast('New Task Assigned', `You have been assigned: "${event.task.title}"`);
    },
    onTaskAssigned: (event) => {
        pushToast('Task Assigned', `You were assigned to: "${event.task.title}"`);
    },
    onTaskStatusChanged: (event) => {
        pushToast('Task Updated', `"${event.task.title}" was updated to ${event.new_status}`);
    },
});

// Seed from server-side prop
unreadCount.value = props.initialUnreadCount;

const recentNotifications = computed(() => notifications.value.slice(0, 5));

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        fetchUnreadCount();
    }
};

const handleMarkAllAsRead = async () => {
    await markAllAsRead();
};

const getIcon = (type) => {
    const icons = {
        task_created: '📋',
        task_assigned: '👤',
        task_status_changed: '🔄',
    };
    return icons[type] || '🔔';
};

const formatDate = (value) => {
    if (!value) return '';
    const date = new Date(value);
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);
    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes}m ago`;
    if (hours < 24) return `${hours}h ago`;
    return `${days}d ago`;
};

const handleOutsideClick = (event) => {
    if (bellRef.value && !bellRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('mousedown', handleOutsideClick);
});

onBeforeUnmount(() => {
    document.removeEventListener('mousedown', handleOutsideClick);
});
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}
.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
