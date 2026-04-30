import { ref, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

/**
 * Composable for managing real-time notifications via Laravel Echo.
 *
 * @param {Object} options
 * @param {Function} [options.onTaskCreated]      - Called when a new task is created and assigned to the user
 * @param {Function} [options.onTaskAssigned]     - Called when the user is newly assigned to a task
 * @param {Function} [options.onTaskStatusChanged]- Called when a task's status changes
 */
export function useNotifications({ onTaskCreated, onTaskAssigned, onTaskStatusChanged } = {}) {
    const page = usePage();
    const notifications = ref([]);
    const unreadCount = ref(0);

    let notificationCounter = 0;

    const fetchUnreadCount = async () => {
        try {
            const response = await window.axios.get('/api/v1/notifications/unread-count');
            unreadCount.value = response.data.unread_count;
        } catch {
            // silently fail – not critical
        }
    };

    const markAsRead = async (notificationId) => {
        try {
            await window.axios.patch(`/api/v1/notifications/${notificationId}/read`);
            const notification = notifications.value.find((n) => n.id === notificationId);
            if (notification) {
                notification.is_read = true;
            }
            if (unreadCount.value > 0) {
                unreadCount.value--;
            }
        } catch {
            // silently fail
        }
    };

    const markAllAsRead = async () => {
        try {
            await window.axios.patch('/api/v1/notifications/mark-all-read');
            notifications.value.forEach((n) => (n.is_read = true));
            unreadCount.value = 0;
        } catch {
            // silently fail
        }
    };

    const dismissNotification = async (notificationId) => {
        try {
            await window.axios.delete(`/api/v1/notifications/${notificationId}`);
            const index = notifications.value.findIndex((n) => n.id === notificationId);
            if (index !== -1) {
                if (!notifications.value[index].is_read && unreadCount.value > 0) {
                    unreadCount.value--;
                }
                notifications.value.splice(index, 1);
            }
        } catch {
            // silently fail
        }
    };

    const addToast = (notification) => {
        notifications.value.unshift(notification);
        if (!notification.is_read) {
            unreadCount.value++;
        }
    };

    let channel = null;

    onMounted(() => {
        const userId = page.props.auth?.user?.id;
        if (!userId || !window.Echo) return;

        fetchUnreadCount();

        channel = window.Echo.private(`employee.${userId}`);

        channel.listen('.task.created', (event) => {
            const notification = {
                id: `rt-${++notificationCounter}`,
                type: 'task_created',
                title: 'New Task Assigned',
                message: `You have been assigned a new task: "${event.task.title}"`,
                link: `/employee/tasks/${event.task.id}`,
                is_read: false,
                created_at: new Date().toISOString(),
                task: event.task,
            };
            addToast(notification);
            if (onTaskCreated) onTaskCreated(event);
        });

        channel.listen('.task.assigned', (event) => {
            const notification = {
                id: `rt-${++notificationCounter}`,
                type: 'task_assigned',
                title: 'Task Assigned to You',
                message: `You have been assigned to the task: "${event.task.title}"`,
                link: `/employee/tasks/${event.task.id}`,
                is_read: false,
                created_at: new Date().toISOString(),
                task: event.task,
            };
            addToast(notification);
            if (onTaskAssigned) onTaskAssigned(event);
        });

        channel.listen('.task.status_changed', (event) => {
            const notification = {
                id: `rt-${++notificationCounter}`,
                type: 'task_status_changed',
                title: 'Task Progress Updated',
                message: `${event.updated_by.name} updated "${event.task.title}" to ${event.new_status}`,
                link: `/employee/tasks/${event.task.id}`,
                is_read: false,
                created_at: new Date().toISOString(),
                task: event.task,
            };
            addToast(notification);
            if (onTaskStatusChanged) onTaskStatusChanged(event);
        });
    });

    onUnmounted(() => {
        const userId = page.props.auth?.user?.id;
        if (userId && window.Echo) {
            window.Echo.leave(`employee.${userId}`);
        }
    });

    return {
        notifications,
        unreadCount,
        fetchUnreadCount,
        markAsRead,
        markAllAsRead,
        dismissNotification,
    };
}
