# Firebase Cloud Messaging - Quick Start

## 1. Set Firebase Environment Variables

Add these to your `.env` file:

```env
# Firebase Web Configuration (from Firebase Console → Project Settings → Your apps)
VITE_FIREBASE_API_KEY=your_api_key_here
VITE_FIREBASE_AUTH_DOMAIN=your_project.firebaseapp.com
VITE_FIREBASE_PROJECT_ID=your_project_id
VITE_FIREBASE_STORAGE_BUCKET=your_project.appspot.com
VITE_FIREBASE_MESSAGING_SENDER_ID=your_sender_id
VITE_FIREBASE_APP_ID=1:your_app_id
VITE_FIREBASE_VAPID_KEY=your_public_vapid_key

# Firebase Server Configuration (from Firebase Console → Project Settings → Service Accounts)
FIREBASE_SERVER_KEY=your_server_key_here
```

## 2. Run Migrations

```bash
php artisan migrate
```

This creates the `fcm_tokens` table to store user device tokens.

## 3. Install Dependencies

```bash
npm install firebase
```

## 4. Add NotificationSetup Component to Your Layout

In your main app layout file (e.g., `resources/views/app.blade.php`):

```vue
<template>
    <div>
        <!-- Your existing layout -->
        <NotificationSetup />
    </div>
</template>

<script setup>
import NotificationSetup from '@/Components/NotificationSetup.vue';
</script>
```

## 5. Rebuild Frontend

```bash
npm run dev
# or
npm run build
```

## 6. Test Notifications

- Open your app in a browser
- A notification prompt should appear after a few seconds
- Click "Enable Notifications"
- Check Firebase Console → Cloud Messaging to send a test notification

## Files Created

- `resources/js/config/firebase.js` - Firebase configuration
- `resources/js/composables/useFirebaseNotifications.js` - Vue composable for handling notifications
- `resources/js/Components/NotificationSetup.vue` - Vue component for notification UI
- `public/firebase-messaging-sw.js` - Service worker for handling background notifications
- `app/Models/FCMToken.php` - Model for storing FCM tokens
- `app/Http/Controllers/Api/FCMTokenController.php` - API endpoints
- `app/Services/FirebaseNotificationService.php` - Service for sending notifications
- `app/Policies/FCMTokenPolicy.php` - Authorization policy
- `database/migrations/2026_05_12_000011_create_fcm_tokens_table.php` - Migration for FCM tokens table

## API Endpoints

- `POST /api/v1/notifications/fcm-token` - Register FCM token
- `POST /api/v1/notifications/fcm-token/unsubscribe` - Unregister FCM token
- `GET /api/v1/notifications/fcm-tokens` - List all FCM tokens
- `DELETE /api/v1/notifications/fcm-tokens/{token}` - Delete specific FCM token

## Sending Notifications

```php
use App\Services\FirebaseNotificationService;

$firebaseService = app(FirebaseNotificationService::class);

// Send to single user
$firebaseService->sendToUser($user, [
    'title' => 'Title',
    'body' => 'Message body',
    'data' => ['type' => 'custom_type'],
]);

// Send to multiple users
$firebaseService->sendToUsers([1, 2, 3], [
    'title' => 'Title',
    'body' => 'Message body',
]);

// Use predefined methods
$firebaseService->sendTaskAssignmentNotification($user, [
    'task_id' => $task->id,
    'title' => $task->title,
]);
```

## Troubleshooting

### Service Worker Issues
- Check DevTools → Application → Service Workers
- Ensure HTTPS (or localhost for development)
- Verify `firebase-messaging-sw.js` exists in `public/` directory

### Notifications Not Showing
- Check FCM tokens are saved: `SELECT * FROM fcm_tokens;`
- Verify Firebase credentials in `.env`
- Check browser notification permission
- Look at Laravel logs: `tail -f storage/logs/laravel.log`

### Firebase Not Initializing
- Verify all `VITE_FIREBASE_*` variables are set
- Run `npm install firebase`
- Rebuild with `npm run build`

## Next: Integrate with Existing Events

See `app/Events/TaskAssignedExample.php` for how to add FCM notifications to your existing events.

See [FIREBASE_SETUP.md](./FIREBASE_SETUP.md) for detailed instructions.
