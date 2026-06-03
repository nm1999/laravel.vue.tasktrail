import { ref } from 'vue';
import { messaging, setupFCMListener } from '@/config/firebase';
import { getToken } from 'firebase/messaging';
import axios from 'axios';

export function useFirebaseNotifications() {
    const isSupported = ref(false);
    const isPermissionGranted = ref(false);
    const fcmToken = ref(null);
    const isLoading = ref(false);
    const error = ref(null);

    // Check if browser supports Firebase Cloud Messaging
    function checkBrowserSupport() {
        isSupported.value =
            'serviceWorker' in navigator &&
            'Notification' in window &&
            'PushManager' in window;
        return isSupported.value;
    }

    // Check current notification permission status
    function checkPermissionStatus() {
        if (!isSupported.value) return false;
        isPermissionGranted.value = Notification.permission === 'granted';
        return isPermissionGranted.value;
    }

    // Request notification permission and get FCM token
    async function requestNotificationPermission() {
        isLoading.value = true;
        error.value = null;

        try {
            if (!checkBrowserSupport()) {
                throw new Error('Your browser does not support notifications');
            }

            if (Notification.permission === 'denied') {
                throw new Error('Notification permission has been denied');
            }

            // Request notification permission if not already granted
            if (Notification.permission !== 'granted') {
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    throw new Error('Notification permission was rejected');
                }
            }

            // Register service worker
            if ('serviceWorker' in navigator) {
                await navigator.serviceWorker.register(
                    '/firebase-messaging-sw.js'
                );
            }

            // Get FCM token
            const token = await getToken(messaging, {
                vapidKey: import.meta.env.VITE_FIREBASE_VAPID_KEY,
            });

            fcmToken.value = token;
            isPermissionGranted.value = true;

            // Send token to backend
            await saveFCMTokenToBackend(token);

            // Set up listener for foreground notifications
            setupFCMListener((payload) => {
                handleNotification(payload);
            });

            return token;
        } catch (err) {
            error.value = err.message;
            console.error('Error requesting notification permission:', err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    // Save FCM token to backend
    async function saveFCMTokenToBackend(token) {
        try {
            await axios.post('/api/notifications/fcm-token', {
                fcm_token: token,
            });
        } catch (err) {
            console.error('Error saving FCM token to backend:', err);
            throw err;
        }
    }

    // Handle incoming notifications
    function handleNotification(payload) {
        console.log('Notification payload:', payload);

        const notification = payload.notification || {};
        const data = payload.data || {};

        // Create a browser notification
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(notification.title || 'New Notification', {
                body: notification.body,
                icon: notification.image || '/favicon.ico',
                badge: '/favicon.ico',
                tag: data.tag || 'notification',
                data: data,
            });
        }

        // Emit custom event for application to handle
        const event = new CustomEvent('fcm:notification', {
            detail: { notification, data },
        });
        window.dispatchEvent(event);
    }

    // Unsubscribe from notifications
    async function unsubscribeNotifications() {
        try {
            if ('serviceWorker' in navigator) {
                const registrations =
                    await navigator.serviceWorker.getRegistrations();
                for (let registration of registrations) {
                    const subscription =
                        await registration.pushManager.getSubscription();
                    if (subscription) {
                        await subscription.unsubscribe();
                    }
                }
            }

            // Notify backend to remove token
            await axios.post('/api/notifications/fcm-token/unsubscribe');

            fcmToken.value = null;
            isPermissionGranted.value = false;
        } catch (err) {
            console.error('Error unsubscribing from notifications:', err);
            throw err;
        }
    }

    return {
        isSupported,
        isPermissionGranted,
        fcmToken,
        isLoading,
        error,
        checkBrowserSupport,
        checkPermissionStatus,
        requestNotificationPermission,
        unsubscribeNotifications,
    };
}
