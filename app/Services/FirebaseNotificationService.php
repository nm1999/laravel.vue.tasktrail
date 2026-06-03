<?php

namespace App\Services;

use App\Models\FCMToken;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    /**
     * Send a notification to a user via Firebase Cloud Messaging.
     */
    public function sendToUser(User $user, array $data): bool
    {
        $tokens = FCMToken::where('user_id', $user->id)->pluck('token')->toArray();

        if (empty($tokens)) {
            Log::debug("No FCM tokens found for user {$user->id}");
            return false;
        }

        return $this->sendToTokens($tokens, $data);
    }

    /**
     * Send a notification to multiple users.
     */
    public function sendToUsers(array $userIds, array $data): bool
    {
        $tokens = FCMToken::whereIn('user_id', $userIds)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            Log::debug('No FCM tokens found for users');
            return false;
        }

        return $this->sendToTokens($tokens, $data);
    }

    /**
     * Send a notification to specific tokens.
     */
    public function sendToTokens(array $tokens, array $data): bool
    {
        $serverKey = config('services.firebase.server_key');

        if (!$serverKey) {
            Log::warning('Firebase server key not configured');
            return false;
        }

        $message = $this->buildMessage($data);

        foreach (array_chunk($tokens, 500) as $tokenBatch) {
            $this->sendBatch($tokenBatch, $message, $serverKey);
        }

        return true;
    }

    /**
     * Build the FCM message payload.
     */
    private function buildMessage(array $data): array
    {
        return [
            'notification' => [
                'title' => $data['title'] ?? 'TaskTrail',
                'body' => $data['body'] ?? '',
                'image' => $data['image'] ?? null,
                'click_action' => $data['click_action'] ?? '/',
            ],
            'data' => $data['data'] ?? [],
            'android' => [
                'ttl' => '3600s',
                'priority' => 'high',
            ],
            'webpush' => [
                'ttl' => 3600,
                'urgency' => 'high',
            ],
        ];
    }

    /**
     * Send a batch of notifications.
     */
    private function sendBatch(array $tokens, array $message, string $serverKey): void
    {
        $payload = [
            'registration_ids' => $tokens,
            'notification' => $message['notification'],
            'data' => $message['data'],
            'android' => $message['android'],
            'webpush' => $message['webpush'],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => "key={$serverKey}",
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            if ($response->failed()) {
                Log::error('FCM send failed', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);
            } else {
                Log::debug('FCM notifications sent', [
                    'count' => count($tokens),
                    'response' => $response->json(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('FCM send exception', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Send notification for task creation.
     */
    public function sendTaskCreatedNotification(User $user, array $taskData): bool
    {
        return $this->sendToUser($user, [
            'title' => 'New Task Created',
            'body' => $taskData['title'] ?? 'A new task has been created',
            'click_action' => route('admin.tasks.show', $taskData['task_id'] ?? '#'),
            'data' => [
                'type' => 'task_created',
                'task_id' => $taskData['task_id'] ?? '',
                'click_action' => route('admin.tasks.show', $taskData['task_id'] ?? '#'),
            ],
        ]);
    }

    /**
     * Send notification for task status change.
     */
    public function sendTaskStatusNotification(User $user, array $taskData): bool
    {
        return $this->sendToUser($user, [
            'title' => 'Task Status Updated',
            'body' => $taskData['message'] ?? 'A task status has been updated',
            'click_action' => route('admin.tasks.show', $taskData['task_id'] ?? '#'),
            'data' => [
                'type' => 'task_status_changed',
                'task_id' => $taskData['task_id'] ?? '',
                'click_action' => route('admin.tasks.show', $taskData['task_id'] ?? '#'),
            ],
        ]);
    }

    /**
     * Send notification for task assignment.
     */
    public function sendTaskAssignmentNotification(User $user, array $taskData): bool
    {
        return $this->sendToUser($user, [
            'title' => 'Task Assigned to You',
            'body' => $taskData['title'] ?? 'A new task has been assigned to you',
            'click_action' => route('admin.tasks.show', $taskData['task_id'] ?? '#'),
            'data' => [
                'type' => 'task_assigned',
                'task_id' => $taskData['task_id'] ?? '',
                'click_action' => route('admin.tasks.show', $taskData['task_id'] ?? '#'),
            ],
        ]);
    }

    /**
     * Send notification for task comment.
     */
    public function sendTaskCommentNotification(User $user, array $commentData): bool
    {
        return $this->sendToUser($user, [
            'title' => 'New Comment on Task',
            'body' => $commentData['comment'] ?? 'A new comment has been added',
            'click_action' => route('admin.tasks.show', $commentData['task_id'] ?? '#'),
            'data' => [
                'type' => 'task_comment',
                'task_id' => $commentData['task_id'] ?? '',
                'click_action' => route('admin.tasks.show', $commentData['task_id'] ?? '#'),
            ],
        ]);
    }
}
