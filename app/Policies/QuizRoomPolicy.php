<?php

namespace App\Policies;

use App\Models\QuizRoom;
use App\Models\User;

class QuizRoomPolicy
{
    public function show(User $user, QuizRoom $room)
    {
        return $user->canJoinRoom($room->id);
    }
}
