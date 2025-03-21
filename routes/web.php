<?php

use App\Http\Controllers\QuizController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('quiz-battle', function () {
    return Inertia::render('QuizDashboard', [
        'rooms' => auth()->user()->quizRooms()->with('players')->get(),
    ]);
})->middleware(['auth', 'verified'])->name('quiz-battle');

Route::post('/quiz-battle', [QuizController::class, 'store'])
    ->middleware([HandlePrecognitiveRequests::class,'auth', 'verified'])
    ->name('quiz-battle.store');
    
Route::get('quiz-battle/{quizRoom}', [QuizController::class, 'show'])->middleware(['auth', 'verified'])->name('quiz-battle.show');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
