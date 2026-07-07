@php
    use Illuminate\Support\Facades\Route;

    /** @var \Throwable|null $exception */
    $message = isset($exception) && $exception->getMessage() !== ''
        ? $exception->getMessage()
        : 'Nincs jogosultságod megtekinteni ezt az oldalt.';

    // Resolve the current panel's logout route regardless of its id ('admin' or 'app').
    $logoutRoute = collect(['filament.admin.auth.logout', 'filament.app.auth.logout', 'logout'])
        ->first(fn (string $name): bool => Route::has($name));

    $portalUrl = 'https://cegem360.eu/module-order';
@endphp
<!DOCTYPE html>
<html lang="hu" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hozzáférés megtagadva</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body {
            font-family: 'Figtree', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background: #1f2438;
            color: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .card {
            width: 100%;
            max-width: 30rem;
            background: #292f4c;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1.25rem;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 20px 40px -20px rgba(0, 0, 0, 0.6);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 9999px;
            background: rgba(251, 191, 36, 0.12);
            color: #fbbf24;
            margin-bottom: 1.25rem;
        }
        .badge svg { width: 1.75rem; height: 1.75rem; }
        h1 { font-size: 1.5rem; font-weight: 700; color: #fff; margin: 0 0 0.5rem; }
        p.msg { font-size: 0.95rem; line-height: 1.5; color: #b6bccb; margin: 0 0 1.75rem; }
        .actions { display: flex; flex-direction: column; gap: 0.75rem; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.7rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 0;
            transition: background-color 0.15s ease;
        }
        .btn-primary { background: #6366f1; color: #fff; }
        .btn-primary:hover { background: #4f46e5; }
        .btn-ghost { background: transparent; color: #e5e7eb; border: 1px solid rgba(255, 255, 255, 0.18); }
        .btn-ghost:hover { background: rgba(255, 255, 255, 0.06); }
        form { margin: 0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge" aria-hidden="true">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            </svg>
        </div>

        <h1>Hozzáférés megtagadva</h1>
        <p class="msg">{{ $message }}</p>

        <div class="actions">
            <a href="{{ $portalUrl }}" class="btn btn-primary">Előfizetés kezelése</a>

            @if ($logoutRoute)
                <form method="POST" action="{{ route($logoutRoute) }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost">Kijelentkezés</button>
                </form>
            @endif
        </div>
    </div>
</body>
</html>
