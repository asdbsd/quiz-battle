<?php

namespace App\Models;

use App\Casts\QuizStatusCast;
use App\Enums\QuizRoomTeams;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizRoom extends Model
{
    protected $fillable = [
        'name',
        'allowed_players_count',
        'questions_count',
        'status'
    ];

    protected $casts = [
        'status' => QuizStatusCast::class
    ];

    protected $appends = [
        'max_per_team'
    ];

    public function players()
    {
        return $this->belongsToMany(User::class, 'quiz_room_user')
            ->withPivot(['team', 'role']);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function isFull(): bool
    {
        return $this->players()->count() === $this->allowed_players_count;
    }

    public function isPlayerInRoom(User $user): bool
    {
        return $this->players()->where('user_id', $user->id)->exists();
    }

    public function getMaxPerTeamAttribute(): int
    {
        return $this->allowed_players_count / 2;
    }
}
