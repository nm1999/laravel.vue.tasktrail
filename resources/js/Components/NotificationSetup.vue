<template>
    <div v-if="showPrompt" class="fixed bottom-4 right-4 max-w-sm p-4 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900">Enable Notifications</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Get real-time notifications for task updates and important events
                </p>
            </div>
            <button
                @click="showPrompt = false"
                class="text-gray-400 hover:text-gray-600"
            >
                ✕
            </button>
        </div>

        <div v-if="error" class="mt-3 p-2 bg-red-50 border border-red-200 rounded text-sm text-red-700">
            {{ error }}
        </div>

        <div class="mt-4 flex gap-2">
            <button
                @click="enableNotifications"
                :disabled="isLoading"
                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
            >
                <span v-if="!isLoading">Enable</span>
                <span v-else>Enabling...</span>
            </button>
            <button
                @click="showPrompt = false"
                class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition"
            >
                Maybe Later
            </button>
        </div>

        <p class="text-xs text-gray-500 mt-3">
            You can enable notifications anytime in your notification settings
        </p>
    </div>

    <!-- Settings Section -->
    <div v-if="!showPrompt && isSupported" class="space-y-4">
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-semibold text-gray-900">Notifications</h4>
                    <p class="text-sm text-gray-600">
                        {{ isPermissionGranted ? '✓ Enabled' : 'Disabled' }}
                    </p>
                </div>
                <button
                    v-if="!isPermissionGranted"
                    @click="enableNotifications"
                    :disabled="isLoading"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 transition"
                >
                    {{ isLoading ? 'Enabling...' : 'Enable Notifications' }}
                </button>
                <button
                    v-else
                    @click="disableNotifications"
                    :disabled="isLoading"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50 transition"
                >
                    {{ isLoading ? 'Disabling...' : 'Disable Notifications' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useFirebaseNotifications } from '@/composables/useFirebaseNotifications';

const showPrompt = ref(false);
const {
    isSupported,
    isPermissionGranted,
    isLoading,
    error,
    checkBrowserSupport,
    checkPermissionStatus,
    requestNotificationPermission,
    unsubscribeNotifications,
} = useFirebaseNotifications();

const enableNotifications = async () => {
    try {
        await requestNotificationPermission();
        showPrompt.value = false;
    } catch (err) {
        console.error('Failed to enable notifications:', err);
    }
};

const disableNotifications = async () => {
    try {
        await unsubscribeNotifications();
    } catch (err) {
        console.error('Failed to disable notifications:', err);
    }
};

onMounted(() => {
    // Check if browser supports notifications
    if (!checkBrowserSupport()) {
        return;
    }

    // Check current permission status
    const hasPermission = checkPermissionStatus();

    // Show prompt if permission not yet granted or denied
    if (Notification.permission === 'default') {
        // Show prompt after a delay to avoid being intrusive
        setTimeout(() => {
            showPrompt.value = true;
        }, 3000);
    }

    // Listen for notifications
    window.addEventListener('fcm:notification', (event) => {
        const { notification, data } = event.detail;
        console.log('FCM Notification received in component:', notification);
        // You can emit an event or update a store here to show a toast or alert
    });
});
</script>
