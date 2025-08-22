<?php

namespace App\Events;

use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RoomActiveUsersWereUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct($room, $user)
    {
        $this->room = $room;
        $this->user = $user;
    }

    /**
     * Broadcast on presence channel for the room.
     */
    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel("quizRooms.{$this->room->id}");
    }

    /**
     * Payload sent to frontend.
     */
    public function broadcastWith(): array
    {
        return [
            'user' => $this->user
        ];
    }
}