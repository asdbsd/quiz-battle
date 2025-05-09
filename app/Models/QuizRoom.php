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
        'available_teams'
    ];

    public function players()
    {
        return $this->belongsToMany(User::class, 'quiz_room_user');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function isFull(): bool
    {
        return $this->players()->count() === $this->allowed_players_count;
    }

    public function getAvailableTeamsAttribute(): array
    {
        $teamsCount = 2;
        $teams = [];
        for ($i = 0; $i < $teamsCount; $i++) {
            $team = QuizRoomTeams::from($i);
            if ($this->players()->where('team', $team->value)->count() < $this->allowed_players_count / $teamsCount) {
                $teams[] = $team;
            }
        }
        return $teams;
    }
}
