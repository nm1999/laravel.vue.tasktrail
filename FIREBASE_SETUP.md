# Firebase Cloud Messaging Integration Guide

This guide walks you through integrating Firebase Cloud Messaging (FCM) for web push notifications into your TaskTrail application.

## Prerequisites

- A Firebase project (create at https://console.firebase.google.com)
- Admin access to Firebase Console
- Your application already running

## Step 1: Create a Firebase Project

1. Go to [Firebase Console](https://console.firebase.google.com)
2. Click **"Create a new project"**
3. Enter your project name (e.g., "TaskTrail")
4. Follow the setup wizard and create the project

## Step 2: Get Firebase Configuration

### Web App Configuration

1. In Firebase Console, click the **Settings** icon (⚙️) → **Project Settings**
2. Under **General** tab, scroll to **Your apps** section
3. Click the **Web** icon (`</>`), then register a new app
4. Copy your Firebase config object (you'll need this for `.env`)

Example config looks like:
```javascript
{
  apiKey: "AIzaSyD...",
  authDomain: "tasktrail-xxxxx.firebaseapp.com",
  projectId: "tasktrail-xxxxx",
  storageBucket: "tasktrail-xxxxx.appspot.com",
  messagingSenderId: "123456789",
  appId: "1:123456789:web:xxxxx"
}
```

### Server Key (for Backend Notifications)

1. In Firebase Console, go to **Project Settings** → **Service Accounts**
2. Click **Generate New Private Key**
3. A JSON file will download containing your service account credentials
4. Open the JSON file and copy the value of `"server_key"` field

## Step 3: Configure Environment Variables

1. Copy `.env.example` to `.env` (if not already done)
2. Add the following variables to your `.env` file:

```env
# Firebase Web Configuration
VITE_FIREBASE_API_KEY=AIzaSyD...
VITE_FIREBASE_AUTH_DOMAIN=tasktrail-xxxxx.firebaseapp.com
VITE_FIREBASE_PROJECT_ID=tasktrail-xxxxx
VITE_FIREBASE_STORAGE_BUCKET=tasktrail-xxxxx.appspot.com
VITE_FIREBASE_MESSAGING_SENDER_ID=123456789
VITE_FIREBASE_APP_ID=1:123456789:web:xxxxx
VITE_FIREBASE_VAPID_KEY=YOUR_VAPID_KEY_HERE

# Firebase Server Configuration
FIREBASE_SERVER_KEY=AAAAA...
```

### Getting VAPID Key

1. In Firebase Console, go to **Cloud Messaging** tab
2. Under **Web configuration** section, click **Generate Key Pair**
3. Copy the public key and paste as `VITE_FIREBASE_VAPID_KEY` in `.env`

## Step 4: Install Dependencies

```bash
npm install firebase
```

## Step 5: Run Database Migrations

```bash
php artisan migrate
```

This creates the `fcm_tokens` table to store user notification tokens.

## Step 6: Integrate Notification Component

Add the `NotificationSetup` component to your main app layout (e.g., `app.blade.php`):

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

## Step 7: Update Existing Events to Send Notifications

Your existing events (`TaskCreated`, `TaskAssigned`, `TaskStatusChanged`) can now trigger FCM notifications.

### Example: Update TaskAssigned Event

```php
<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskAssigned implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Task $task,
        public User $assignedTo,
    ) {}

    public function broadcastOn()
    {
        return new PrivateChannel("tasks.{$this->task->id}");
    }

    public function dispatchPriority()
    {
        // Send FCM notification when event is dispatched
        $notificationService = app(FirebaseNotificationService::class);
        $notificationService->sendTaskAssignmentNotification(
            $this->assignedTo,
            [
                'task_id' => $this->task->id,
                'title' => $this->task->title,
            ]
        );

        return;
    }
}
```

## Step 8: Sending Notifications from Your Code

### From a Controller or Job

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FirebaseNotificationService;

class TaskController extends Controller
{
    public function __construct(
        protected FirebaseNotificationService $firebaseService,
    ) {}

    public function sendNotification()
    {
        $user = User::find(1);

        $this->firebaseService->sendToUser($user, [
            'title' => 'Task Update',
            'body' => 'Your task has been updated',
            'click_action' => route('admin.tasks.index'),
            'data' => [
                'type' => 'task_update',
                'task_id' => '123',
            ],
        ]);
    }

    public function sendToMultiple()
    {
        $userIds = [1, 2, 3, 4, 5];

        $this->firebaseService->sendToUsers($userIds, [
            'title' => 'System Announcement',
            'body' => 'Important update for all users',
            'data' => [
                'type' => 'announcement',
            ],
        ]);
    }
}
```

### Predefined Notification Methods

```php
// Send task created notification
$firebaseService->sendTaskCreatedNotification($user, [
    'task_id' => $task->id,
    'title' => $task->title,
]);

// Send task status changed notification
$firebaseService->sendTaskStatusNotification($user, [
    'task_id' => $task->id,
    'message' => 'Task marked as complete',
]);

// Send task assigned notification
$firebaseService->sendTaskAssignmentNotification($user, [
    'task_id' => $task->id,
    'title' => $task->title,
]);

// Send task comment notification
$firebaseService->sendTaskCommentNotification($user, [
    'task_id' => $task->id,
    'comment' => 'Check this out!',
]);
```

## Frontend Usage

### Using the Composable in Vue Components

```vue
<template>
    <div>
        <p v-if="!isSupported">Your browser doesn't support notifications</p>
        <button v-else @click="requestNotificationPermission">
            {{ isPermissionGranted ? 'Notifications Enabled' : 'Enable Notifications' }}
        </button>
    </div>
</template>

<script setup>
import { useFirebaseNotifications } from '@/composables/useFirebaseNotifications';

const {
    isSupported,
    isPermissionGranted,
    requestNotificationPermission,
} = useFirebaseNotifications();
</script>
```

### Listening to Notification Events

```javascript
// In any Vue component or JavaScript file
window.addEventListener('fcm:notification', (event) => {
    const { notification, data } = event.detail;
    console.log('Received notification:', notification);
    console.log('Custom data:', data);

    // Show toast, update store, navigate to task, etc.
    if (data.type === 'task_assigned') {
        // Navigate to the task
        window.location.href = `/tasks/${data.task_id}`;
    }
});
```

## Testing Notifications

### Send Test Notification via Firebase Console

1. Go to **Cloud Messaging** in Firebase Console
2. Click **Send your first message**
3. Select **Web**
4. Enter title and message
5. Click **Send**

### Send via API (cURL)

```bash
curl -X POST https://fcm.googleapis.com/fcm/send \
  -H "Authorization: key=YOUR_FIREBASE_SERVER_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "notification": {
      "title": "Test Notification",
      "body": "This is a test"
    },
    "to": "USER_FCM_TOKEN"
  }'
```

## Troubleshooting

### Service Worker Not Registering

- Ensure `firebase-messaging-sw.js` is in the `public/` directory
- Check browser console for CORS errors
- Verify HTTPS is used (or localhost for development)

### FCM Token Not Being Saved

- Check that user is authenticated
- Verify Firebase configuration is correct
- Check browser console for errors
- Look at Laravel logs: `tail -f storage/logs/laravel.log`

### Notifications Not Being Received

- Verify FCM token is stored in database: `select * from fcm_tokens;`
- Check Firebase console quota usage
- Verify `FIREBASE_SERVER_KEY` is correctly set
- Check Laravel logs for errors
- Ensure service worker is registered: Open DevTools → Application → Service Workers

### Firebase Initialization Errors

- Verify all `VITE_FIREBASE_*` environment variables are set
- Check that `npm install firebase` was run
- Verify `.env.local` or `.env` has correct values
- Rebuild frontend: `npm run build`

## Architecture Overview

```
┌─────────────────────────────────────┐
│         Web Browser                 │
├─────────────────────────────────────┤
│ NotificationSetup.vue              │ ← User interacts
│ useFirebaseNotifications.js        │ ← Requests permission
│ firebase-messaging-sw.js           │ ← Service Worker
└────────────────────┬────────────────┘
                     │
         ┌───────────┴────────────┐
         ▼                        ▼
    ┌────────────┐         ┌─────────────┐
    │ Laravel    │         │   Firebase  │
    │ Backend    │         │   Console   │
    ├────────────┤         ├─────────────┤
    │ FCM Token  │────────▶│ Cloud       │
    │ Endpoint   │         │ Messaging   │
    │            │◀────────│             │
    │ Stores     │         │ Sends Push  │
    │ FCM Tokens │         │ Notif.      │
    │ in DB      │         │             │
    └────────────┘         └─────────────┘
```

## Next Steps

1. ✅ Set up Firebase project
2. ✅ Configure environment variables
3. ✅ Run migrations
4. ✅ Add NotificationSetup component to your layout
5. ✅ Update your events to send FCM notifications
6. ✅ Test notifications in development
7. ✅ Deploy to production

## Additional Resources

- [Firebase Cloud Messaging Documentation](https://firebase.google.com/docs/cloud-messaging)
- [Web Push API](https://developer.mozilla.org/en-US/docs/Web/API/Push_API)
- [Service Workers](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)
- [Laravel Broadcasting](https://laravel.com/docs/11.x/broadcasting)

## Support

For issues or questions, check:
1. Browser Developer Tools → Console tab
2. Laravel logs: `storage/logs/laravel.log`
3. Firebase Console → Cloud Messaging tab
4. Service Worker status: DevTools → Application → Service Workers
