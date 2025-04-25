<?php

use App\Broadcasting\QuizRoomChannel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('quizRooms.{id}', QuizRoomChannel::class);