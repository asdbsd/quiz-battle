<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizRoom extends Model
{
    protected $fillable = [
        'name',
        'allowed_players_count',
        'questions_count',
        'status'
    ];

    public function players()
    {
        return $this->belongsToMany(User::class, 'quiz_room_user');
    }
}
