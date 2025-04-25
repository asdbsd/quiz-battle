<?php

namespace App\Models;

use App\Casts\QuizStatusCast;
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
}
