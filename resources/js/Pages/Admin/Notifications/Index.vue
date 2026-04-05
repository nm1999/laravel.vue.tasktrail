<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md p-4">
            <SideBar />
        </aside>
        <main class="flex-1 p-6 overflow-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">Notifications</h3>
                <button
                    @click="markAllAsRead"
                    class="px-4 py-2 text-sm bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors"
                >
                    Mark all as read
                </button>
            </div>

            <!-- Filter Tabs -->
            <div class="bg-white rounded-lg shadow-md mb-6 flex gap-0 overflow-x-auto">
                <button
                    v-for="filter in filters"
                    :key="filter"
                    @click="activeFilter = filter"
                    :class="[
                        'px-6 py-3 font-medium whitespace-nowrap transition-colors',
                        activeFilter === filter
                            ? 'text-blue-600 border-b-2 border-blue-600'
                            : 'text-gray-600 hover:text-gray-900'
                    ]"
                >
                    {{ filter }}
                    <span
                        v-if="filter !== 'All' && getFilterCount(filter) > 0"
                        class="ml-2 inline-block bg-red-500 text-white text-xs rounded-full px-2 py-0.5"
                    >
                        {{ getFilterCount(filter) }}
                    </span>
                </button>
            </div>

            <!-- Notifications List -->
            <div class="space-y-4">
                <div
                    v-if="filteredNotifications.length === 0"
                    class="bg-white rounded-lg shadow-md p-12 text-center"
                >
                    <p class="text-gray-500 text-lg">No notifications yet</p>
                    <p class="text-gray-400 text-sm mt-2">You're all caught up!</p>
                </div>

                <div
                    v-for="notification in filteredNotifications"
                    :key="notification.id"
                    :class="[
                        'bg-white rounded-lg shadow-md p-4 flex items-start gap-4 transition-colors hover:shadow-lg',
                        !notification.read ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                    ]"
                >
                    <!-- Icon/Badge -->
                    <div
                        :class="[
                            'flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center text-white',
                            getIconColor(notification.type)
                        ]"
                    >
                        <span class="text-lg">{{ getIcon(notification.type) }}</span>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h4
                                    :class="[
                                        'font-semibold text-gray-900',
                                        !notification.read ? 'text-base' : 'text-sm'
                                    ]"
                                >
                                    {{ notification.title }}
                                </h4>
                                <p class="text-gray-600 text-sm mt-1">
                                    {{ notification.message }}
                                </p>
                            </div>
                            <div v-if="!notification.read" class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                        </div>

                        <!-- Meta Info -->
                        <div class="flex items-center gap-4 mt-3">
                            <span class="text-xs text-gray-500">{{ getTimeAgo(notification.timestamp) }}</span>
                            <span v-if="notification.relatedItem" class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                {{ notification.relatedItem }}
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2 mt-3">
                            <button
                                @click="toggleRead(notification.id)"
                                class="text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors"
                            >
                                {{ notification.read ? 'Mark as unread' : 'Mark as read' }}
                            </button>
                            <button
                                @click="deleteNotification(notification.id)"
                                class="text-xs text-red-600 hover:text-red-800 font-medium transition-colors"
                            >
                                Delete
                            </button>
                            <a
                                v-if="notification.link"
                                :href="notification.link"
                                class="text-xs text-green-600 hover:text-green-800 font-medium transition-colors"
                            >
                                View Details →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import { ref, computed } from 'vue';
import SideBar from "../SideBar.vue";

export default {
    name: "Notifications",
    components: {
        SideBar,
    },
    setup() {
        const activeFilter = ref('All');
        const filters = ['All', 'Unread', 'Task Updates', 'Assignments', 'Deadlines', 'System'];

        // Sample notifications data - replace with API call
        const notifications = ref([
            {
                id: 1,
                type: 'assignment',
                title: 'You\'ve been assigned a task',
                message: 'You were assigned to "Fix login bug" by Sarah Williams',
                timestamp: new Date(Date.now() - 5 * 60000), // 5 minutes ago
                read: false,
                relatedItem: 'Fix login bug',
                link: '/tasks/1',
            },
            {
                id: 2,
                type: 'deadline',
                title: 'Task deadline approaching',
                message: '"Implement user dashboard" is due in 2 hours',
                timestamp: new Date(Date.now() - 30 * 60000), // 30 minutes ago
                read: false,
                relatedItem: 'Implement user dashboard',
                link: '/tasks/2',
            },
            {
                id: 3,
                type: 'update',
                title: 'Task status updated',
                message: 'John Doe marked "API Integration" as completed',
                timestamp: new Date(Date.now() - 2 * 60 * 60000), // 2 hours ago
                read: false,
                relatedItem: 'API Integration',
                link: '/tasks/3',
            },
            {
                id: 4,
                type: 'comment',
                title: 'New comment on task',
                message: 'Mike Johnson commented on "Design mockups": "Looks great!"',
                timestamp: new Date(Date.now() - 4 * 60 * 60000), // 4 hours ago
                read: true,
                relatedItem: 'Design mockups',
                link: '/tasks/4',
            },
            {
                id: 5,
                type: 'system',
                title: 'System maintenance scheduled',
                message: 'Scheduled maintenance on 2026-04-06 at 02:00 AM',
                timestamp: new Date(Date.now() - 24 * 60 * 60000), // 1 day ago
                read: true,
                relatedItem: null,
                link: null,
            },
        ]);

        const getIcon = (type) => {
            const icons = {
                assignment: '👤',
                deadline: '⏰',
                update: '✓',
                comment: '💬',
                system: '⚙️',
            };
            return icons[type] || '🔔';
        };

        const getIconColor = (type) => {
            const colors = {
                assignment: 'bg-blue-500',
                deadline: 'bg-red-500',
                update: 'bg-green-500',
                comment: 'bg-purple-500',
                system: 'bg-gray-500',
            };
            return colors[type] || 'bg-blue-500';
        };

        const getTypeLabel = (type) => {
            const labels = {
                assignment: 'Assignments',
                deadline: 'Deadlines',
                update: 'Task Updates',
                comment: 'Comments',
                system: 'System',
            };
            return labels[type] || 'Other';
        };

        const getTimeAgo = (timestamp) => {
            const now = new Date();
            const diff = now - timestamp;
            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(diff / 3600000);
            const days = Math.floor(diff / 86400000);

            if (minutes < 1) return 'Just now';
            if (minutes < 60) return `${minutes}m ago`;
            if (hours < 24) return `${hours}h ago`;
            return `${days}d ago`;
        };

        const filteredNotifications = computed(() => {
            if (activeFilter.value === 'All') {
                return notifications.value;
            }
            if (activeFilter.value === 'Unread') {
                return notifications.value.filter(n => !n.read);
            }
            // Map filter names to types
            const filterMap = {
                'Task Updates': 'update',
                'Assignments': 'assignment',
                'Deadlines': 'deadline',
                'System': 'system',
            };
            const typeFilter = filterMap[activeFilter.value];
            return notifications.value.filter(n => n.type === typeFilter);
        });

        const getFilterCount = (filter) => {
            if (filter === 'Unread') {
                return notifications.value.filter(n => !n.read).length;
            }
            const filterMap = {
                'Task Updates': 'update',
                'Assignments': 'assignment',
                'Deadlines': 'deadline',
                'System': 'system',
            };
            const typeFilter = filterMap[filter];
            return notifications.value.filter(n => n.type === typeFilter).length;
        };

        const toggleRead = (id) => {
            const notification = notifications.value.find(n => n.id === id);
            if (notification) {
                notification.read = !notification.read;
            }
        };

        const deleteNotification = (id) => {
            notifications.value = notifications.value.filter(n => n.id !== id);
        };

        const markAllAsRead = () => {
            notifications.value.forEach(n => n.read = true);
        };

        return {
            activeFilter,
            filters,
            notifications,
            filteredNotifications,
            getIcon,
            getIconColor,
            getTimeAgo,
            getFilterCount,
            toggleRead,
            deleteNotification,
            markAllAsRead,
        };
    },
};
</script>
