// Firebase Cloud Messaging Service Worker
// This handles background notifications

self.addEventListener('push', (event) => {
    console.log('Push notification received:', event);

    if (event.data) {
        const data = event.data.json();
        const options = {
            body: data.notification?.body || 'You have a new notification',
            icon: data.notification?.icon || '/favicon.ico',
            badge: data.notification?.badge || '/favicon.ico',
            tag: data.notification?.tag || 'notification',
            data: data.data || {},
            click_action: data.notification?.click_action || '/',
        };

        event.waitUntil(
            self.registration.showNotification(
                data.notification?.title || 'New Notification',
                options
            )
        );
    }
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const clickAction = event.notification.data?.click_action || '/';
    
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then((clientList) => {
            // Check if there's already a window/tab open with the target URL
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url === clickAction && 'focus' in client) {
                    return client.focus();
                }
            }
            // If not, open a new window/tab with the target URL
            if (clients.openWindow) {
                return clients.openWindow(clickAction);
            }
        })
    );
});
