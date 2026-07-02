<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

final class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = $request->user();
        $team = $user?->teams()->first();

        if ($team) {
            return redirect('/app/'.$team->slug);
        }

        return to_route('filament.admin.tenant.registration');
    }
}
