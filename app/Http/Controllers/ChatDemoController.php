<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CustomerType;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

final class ChatDemoController extends Controller
{
    public function index(): View
    {
        // Demo customer létrehozása vagy meglévő használata
        $customer = Customer::query()->firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'unique_identifier' => 'DEMO-'.Str::random(10),
                'name' => 'Demo Customer',
                'email' => 'demo@example.com',
                'phone' => '+36 20 123 4567',
                'type' => CustomerType::Individual,
                'is_active' => true,
            ]
        );

        return view('chat-demo', ['customer' => $customer]);
    }
}
