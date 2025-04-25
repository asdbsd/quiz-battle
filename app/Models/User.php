<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\QuizRoomStatuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function quizRooms()
    {
        return $this->belongsToMany(QuizRoom::class, 'quiz_room_user');
    }

    public function canJoinRoom($roomId): bool
    {
        $room = QuizRoom::find($roomId);
        $isInActiveRoom = QuizRoom::whereIn('status', [QuizRoomStatuses::WAITING_FOR_PLAYERS->value, QuizRoomStatuses::IN_PROGRESS->value])
            ->whereHas('players', fn($q) => $q->where('user_id', $this->id))
            ->exists();
        return $room && ($room->players()->where('user_id', $this->id)->exists() || (!$room->isFull() && !$isInActiveRoom));
    }
    
}
