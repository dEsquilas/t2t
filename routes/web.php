<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PersonalityProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Personality Profile generation routes
    Route::get('/personality-profiles', [PersonalityProfileController::class, 'index'])->name('personality-profiles.index');
    Route::get('/personality-profiles/create', [PersonalityProfileController::class, 'create'])->name('personality-profiles.create');
    Route::post('/personality-profiles', [PersonalityProfileController::class, 'store'])->name('personality-profiles.store');
    Route::get('/personality-profiles/{profile}', [PersonalityProfileController::class, 'show'])->name('personality-profiles.show');
    Route::get('/personality-profiles/{profile}/question/{questionNumber}', [PersonalityProfileController::class, 'getQuestion'])->name('personality-profiles.question');
    Route::post('/personality-profiles/{profile}/question/{questionNumber}', [PersonalityProfileController::class, 'answerQuestion'])->name('personality-profiles.answer');
    Route::post('/personality-profiles/{profile}/generate', [PersonalityProfileController::class, 'generateProfile'])->name('personality-profiles.generate');
    Route::get('/personality-profiles/{profile}/download', [PersonalityProfileController::class, 'downloadProfile'])->name('personality-profiles.download');
    Route::get('/personality-profiles/{profile}/chat-profile', [PersonalityProfileController::class, 'getChatProfile'])->name('personality-profiles.chat-profile');
    Route::delete('/personality-profiles/{profile}', [PersonalityProfileController::class, 'destroy'])->name('personality-profiles.destroy');
    
    // Chat routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/api/conversations', [ChatController::class, 'getConversations'])->name('api.conversations');
    Route::post('/api/conversations', [ChatController::class, 'createConversation'])->name('api.conversations.create');
    Route::get('/api/conversations/{id}', [ChatController::class, 'getConversation'])->name('api.conversations.show');
    Route::put('/api/conversations/{id}', [ChatController::class, 'updateConversation'])->name('api.conversations.update');
    Route::delete('/api/conversations/{id}', [ChatController::class, 'deleteConversation'])->name('api.conversations.delete');
    Route::post('/api/conversations/{id}/messages', [ChatController::class, 'sendMessage'])->name('api.conversations.messages');
    Route::get('/api/models', [ChatController::class, 'getAvailableModels'])->name('api.models');
});

require __DIR__.'/auth.php';
