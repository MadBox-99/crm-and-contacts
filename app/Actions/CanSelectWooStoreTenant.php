<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Gate for the filament-woocommerce plugin: only admins may reassign a
 * WooCommerce store to another team. Regular users belong to a single team,
 * so the store form forces their store to the current tenant instead.
 */
final class CanSelectWooStoreTenant
{
    public function __invoke(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->isAdmin();
    }
}
