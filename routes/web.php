<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuizController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('quiz-battle', [QuizController::class, 'index'])
        ->name('quiz-battle');

    Route::post('/quiz-battle', [QuizController::class, 'store'])
        ->middleware([HandlePrecognitiveRequests::class])
        ->name('quiz-battle.store');
        
    Route::get('quiz-battle/{quizRoom}', [QuizController::class, 'show'])
        ->name('quiz-battle.show');

    Route::post('quiz/{quizRoom}/start', [QuizController::class, 'startGame'])
        ->name('quiz.start');
    Route::post('quiz/{quizRoom}/questions/{question}/answer', [QuizController::class, 'submitAnswer'])
        ->name('quiz.answer');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
