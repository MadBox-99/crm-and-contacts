<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Chat session private channel - admins and guests can access
Broadcast::channel('chat.session.{sessionId}', function (?User $user, int $sessionId): bool {
    $session = ChatSession::query()->find($sessionId);

    if (! $session) {
        return false;
    }

    // Allow if:
    // 1. User is assigned to this session OR can access admin panel
    // 2. No user (guest) - allow for customer-side chat
    if ($user instanceof User) {
        return $session->user_id === $user->id || $user->hasRole(Role::Admin);
    }

    // For guests, allow access (customer side)
    return true;
});

// Online users presence channel - minden bejelentkezett admin láthatja
Broadcast::channel('chat.online-users', fn (User $user): array => [
    'id' => $user->id,
    'name' => $user->name,
]);
