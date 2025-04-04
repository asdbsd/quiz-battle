<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'quiz_room_id',
        'question',
        'options',
        'correct_answer',
        'order',
        'points'
    ];

    protected $casts = [
        'options' => 'array'
    ];

    public function quizRoom(): BelongsTo
    {
        return $this->belongsTo(QuizRoom::class);
    }
}
