<?php

namespace App\Policies;

use App\Models\FCMToken;
use App\Models\User;

class FCMTokenPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FCMToken $token): bool
    {
        return $user->id === $token->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FCMToken $token): bool
    {
        return $user->id === $token->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FCMToken $token): bool
    {
        return $user->id === $token->user_id;
    }
}
