<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
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
