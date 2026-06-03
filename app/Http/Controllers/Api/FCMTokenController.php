<?php

namespace App\Http\Controllers\Api;

use App\Models\FCMToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FCMTokenController extends Controller
{
    /**
     * Store or update FCM token.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fcm_token' => 'required|string|min:10',
            'device_name' => 'nullable|string|max:255',
            'device_type' => 'nullable|string|in:web,android,ios',
        ]);

        $user = auth()->user();

        // Check if token already exists
        $existingToken = FCMToken::where('token', $validated['fcm_token'])
            ->where('user_id', $user->id)
            ->first();

        if ($existingToken) {
            $existingToken->updateLastUsed();
            return response()->json([
                'message' => 'FCM token already registered',
                'token_id' => $existingToken->id,
            ]);
        }

        // Create new token
        $token = FCMToken::create([
            'user_id' => $user->id,
            'token' => $validated['fcm_token'],
            'device_name' => $validated['device_name'] ?? 'Unknown Device',
            'device_type' => $validated['device_type'] ?? 'web',
            'last_used_at' => now(),
        ]);

        return response()->json([
            'message' => 'FCM token registered successfully',
            'token_id' => $token->id,
        ], 201);
    }

    /**
     * Unsubscribe FCM token.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Delete all FCM tokens for this user (or you can be more selective)
        FCMToken::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'Unsubscribed from notifications successfully',
        ]);
    }

    /**
     * Get all FCM tokens for current user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $tokens = FCMToken::where('user_id', $user->id)->get();

        return response()->json([
            'tokens' => $tokens,
            'count' => $tokens->count(),
        ]);
    }

    /**
     * Delete specific FCM token.
     */
    public function destroy(Request $request, FCMToken $token): JsonResponse
    {
        $this->authorize('delete', $token);
        $token->delete();

        return response()->json([
            'message' => 'FCM token deleted successfully',
        ]);
    }
}
