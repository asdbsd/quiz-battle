<?php

namespace App\Http\Controllers;

use App\Enums\QuizRoomStatuses;
use App\Http\Requests\QuizRequest;
use App\Models\QuizRoom;
use Inertia\Inertia;

class QuizController extends Controller
{
    public function store(QuizRequest $request)
    {
        $validated = $request->validated();
        $validated['status'] = QuizRoomStatuses::WAITING_FOR_PLAYERS->value;
        $quizRoom = QuizRoom::create($validated);
        $quizRoom->players()->attach(auth()->user()->id);

        return Inertia::render('QuizBattleRoom', ['quizRoom' => $quizRoom, 'players' => $quizRoom->players]);
    }

    public function show(QuizRoom $quizRoom)
    {
        if(!in_array(auth()->user()->id, $quizRoom->players()->pluck('id')->toArray())) {
            $quizRoom->players()->attach(auth()->user()->id);
        }

        return Inertia::render('QuizBattleRoom', ['quizRoom' => $quizRoom, 'players' => $quizRoom->players]);
    }
}
