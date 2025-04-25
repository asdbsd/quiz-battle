<?php

namespace App\Broadcasting;

use App\Models\QuizRoom;
use App\Models\User;

class QuizRoomChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, string $quizRoom): array|bool
    {
        if($user->canJoinRoom($quizRoom)) {
            return ['user' => $user];
        }
        return false;
    }
}
