<?php

namespace App\Http\Controllers;

use App\Enums\QuizRoomRoles;
use App\Enums\QuizRoomStatuses;
use App\Enums\QuizRoomTeams;
use App\Events\RoomActiveUsersWereUpdated;
use App\Http\Requests\QuizRequest;
use App\Models\QuizRoom;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
class QuizController extends Controller
{
    public function index(): Response
    {
        $rooms = QuizRoom::with('players')
        ->withCount('players')
        ->where(function($q) {
            $q->havingRaw('players_count < allowed_players_count')
            ->orWhereHas('players', function($q) {
                $q->where('user_id', auth()->user()->id);
            });
        })
        ->get();
        dd($rooms->first()->available_teams);
        return Inertia::render('QuizDashboard', [
            'rooms' => $rooms,

        ]);
    }

    public function store(QuizRequest $request)
    {
        $validated = $request->validated();
        $validated['status'] = QuizRoomStatuses::WAITING_FOR_PLAYERS->value;

        $quizRoom = QuizRoom::create($validated);
        $quizRoom->players()->attach(auth()->user()->id,
        [
            'role' => QuizRoomRoles::HOST->value,
            'team' => QuizRoomTeams::TEAM_ONE->value
        ]);

        return Inertia::render('QuizBattleRoom', [
            'quizRoom' => $quizRoom,
            'players' => $quizRoom->players,
            'currentQuestion' => null
        ]);
    }

    public function show(Request $request, QuizRoom $quizRoom)
    {
        Gate::authorize('show', $quizRoom);

        $quizRoom->players()->attach(auth()->user()->id, 
        [
            'role' => QuizRoomRoles::PARTICIPANT->value,
            'team' => $quizRoom->allowed_players_count
        ]);
        
        RoomActiveUsersWereUpdated::dispatch($quizRoom);

        return Inertia::render('QuizBattleRoom', [
            'quizRoom' => $quizRoom
        ]);
    }

    public function startGame(QuizRoom $quizRoom)
    {
        if ($quizRoom->status !== QuizRoomStatuses::WAITING_FOR_PLAYERS->value) {
            return response()->json(['error' => 'Game already started'], 400);
        }

        if ($quizRoom->players()->count() < 2) {
            return response()->json(['error' => 'Not enough players'], 400);
        }

        // Generate questions
        $questions = [
            [
                'question' => 'What is 2 + 2?',
                'options' => ['3', '4', '5', '6'],
                'correct_answer' => '4',
                'order' => 1
            ],
            [
                'question' => 'What is the capital of France?',
                'options' => ['London', 'Berlin', 'Paris', 'Madrid'],
                'correct_answer' => 'Paris',
                'order' => 2
            ],
            // Add more questions as needed
        ];

        foreach ($questions as $question) {
            $quizRoom->questions()->create($question);
        }

        $quizRoom->update(['status' => QuizRoomStatuses::IN_PROGRESS->value]);

        return response()->json([
            'message' => 'Game started',
            'currentQuestion' => $quizRoom->questions()->orderBy('order')->first()
        ]);
    }

    public function submitAnswer(Request $request, QuizRoom $quizRoom, Question $question)
    {
        if ($quizRoom->status !== QuizRoomStatuses::IN_PROGRESS->value) {
            return response()->json(['error' => 'Game not in progress'], 400);
        }

        $isCorrect = $question->correct_answer === $request->answer;
        $nextQuestion = $quizRoom->questions()
            ->where('order', '>', $question->order)
            ->orderBy('order')
            ->first();

        if (!$nextQuestion) {
            $quizRoom->update(['status' => QuizRoomStatuses::COMPLETED->value]);
        }

        return response()->json([
            'correct' => $isCorrect,
            'points' => $isCorrect ? $question->points : 0,
            'nextQuestion' => $nextQuestion
        ]);
    }

    protected function isTeamFull($quizRoom, $team)
    {
        return $quizRoom->players()
            ->where('team', $team)
            ->count() >= $quizRoom->allowed_players_count / 2;
    }
}
