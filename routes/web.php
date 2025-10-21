<?php

declare(strict_types=1);

use App\Http\Controllers\ChatDemoController;
use App\Livewire\ComplaintSubmission;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): Factory|View => view('welcome'))->name('welcome');

// Chat demo route
Route::get('/chat-demo', [ChatDemoController::class, 'index'])->name('chat.demo');

// Complaint submission route
Route::get('/complaints/submit', ComplaintSubmission::class)->name('complaints.submit');
