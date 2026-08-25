<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Account') · {{ config('app.name', 'AI Quiz') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="//unpkg.com/alpinejs" defer></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #f3f4f6;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── Top bar ─── */
        .acc-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #1e293b;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        @media (min-width: 768px) { .acc-topbar { padding: 1rem 2.5rem; } }

        .acc-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
            text-decoration: none;
            font-weight: 800;
            font-size: 1.0625rem;
            letter-spacing: -0.015em;
        }
        .acc-brand-icon {
            width: 1.75rem;
            height: 1.75rem;
            background: linear-gradient(135deg, #4154f1, #3743cc);
            border-radius: 0.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 12px rgba(65, 84, 241, 0.4);
        }

        .acc-back {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.5rem 0.875rem;
            border-radius: 0.5rem;
            border: 1px solid #334155;
            background: rgba(15, 23, 42, 0.6);
            transition: all 120ms;
        }
        .acc-back:hover {
            color: white;
            border-color: #4154f1;
            background: rgba(65, 84, 241, 0.08);
        }

        /* ─── Content shell ─── */
        .acc-shell {
            max-width: 44rem;
            margin: 2rem auto 4rem;
            padding: 0 1.25rem;
        }
        @media (min-width: 768px) { .acc-shell { margin: 3rem auto 5rem; padding: 0 2rem; } }

        .acc-header {
            margin-bottom: 2rem;
            text-align: center;
        }
        .acc-tag {
            display: inline-block;
            padding: 0.25rem 0.625rem;
            background: rgba(65, 84, 241, 0.15);
            color: #a5b4fc;
            border-radius: 9999px;
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.875rem;
        }
        .acc-h1 {
            font-size: 1.75rem;
            font-weight: 800;
            margin: 0 0 0.375rem;
            letter-spacing: -0.02em;
        }
        @media (min-width: 768px) { .acc-h1 { font-size: 2.125rem; } }
        .acc-sub {
            color: #9ca3af;
            font-size: 0.9375rem;
            margin: 0;
            line-height: 1.5;
        }

        /* ─── Section cards ─── */
        .acc-card {
            background: linear-gradient(180deg, #1e293b 0%, #172033 100%);
            border: 1px solid #334155;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }
        @media (min-width: 768px) { .acc-card { padding: 2rem; } }

        .acc-card-head {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #334155;
        }
        .acc-card-title {
            font-size: 1.0625rem;
            font-weight: 700;
            margin: 0;
            color: white;
            letter-spacing: -0.005em;
        }
        .acc-card-sub {
            color: #94a3b8;
            font-size: 0.8125rem;
            margin: 0.25rem 0 0;
            line-height: 1.5;
        }

        /* ─── Form fields (reused from auth) ─── */
        .auth-field { margin-bottom: 1rem; }
        .auth-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #d1d5db;
            margin-bottom: 0.375rem;
            letter-spacing: 0.01em;
        }
        .auth-input, .auth-textarea {
            width: 100%;
            padding: 0.75rem 0.875rem;
            border: 1.5px solid #334155;
            border-radius: 0.625rem;
            background: rgba(15, 23, 42, 0.7);
            color: #f3f4f6;
            font-size: 0.9375rem;
            font-family: inherit;
            transition: all 150ms;
        }
        .auth-textarea { min-height: 6rem; resize: vertical; }
        .auth-input::placeholder, .auth-textarea::placeholder { color: #6b7280; }
        .auth-input:focus, .auth-textarea:focus {
            outline: none;
            border-color: #4154f1;
            background: rgba(15, 23, 42, 0.95);
            box-shadow: 0 0 0 4px rgba(65, 84, 241, 0.15);
        }
        .auth-input:hover:not(:focus), .auth-textarea:hover:not(:focus) { border-color: #475569; }
        .auth-error {
            font-size: 0.8125rem;
            color: #fca5a5;
            margin-top: 0.375rem;
        }
        .auth-hint {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 0.375rem;
        }

        /* ─── Avatar uploader ─── */
        .acc-avatar-row {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #334155;
        }
        .acc-avatar {
            position: relative;
            width: 5rem;
            height: 5rem;
            border-radius: 9999px;
            overflow: hidden;
            flex-shrink: 0;
            border: 2px solid #4154f1;
            box-shadow: 0 4px 12px rgba(65, 84, 241, 0.25);
            background: rgba(65, 84, 241, 0.15);
        }
        .acc-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .acc-avatar-actions { flex: 1; min-width: 0; }
        .acc-avatar-title {
            font-size: 0.9375rem;
            font-weight: 600;
            color: white;
            margin: 0 0 0.25rem;
        }
        .acc-avatar-desc {
            font-size: 0.75rem;
            color: #94a3b8;
            margin: 0 0 0.625rem;
        }
        .acc-file-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.5rem 0.875rem;
            background: rgba(65, 84, 241, 0.15);
            color: #a5b4fc;
            border: 1px solid rgba(65, 84, 241, 0.35);
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 120ms;
        }
        .acc-file-btn:hover {
            background: rgba(65, 84, 241, 0.25);
            border-color: rgba(65, 84, 241, 0.55);
        }
        .acc-file-input { display: none; }
        .acc-file-name {
            display: inline-block;
            margin-left: 0.625rem;
            font-size: 0.75rem;
            color: #94a3b8;
            vertical-align: middle;
        }

        /* ─── Grid row (2 cols on md+) ─── */
        .auth-row-2 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        @media (min-width: 640px) { .auth-row-2 { grid-template-columns: 1fr 1fr; gap: 1rem; } }

        /* ─── Buttons ─── */
        .auth-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
            background: linear-gradient(135deg, #4154f1 0%, #3743cc 100%);
            color: white;
            padding: 0.6875rem 1.5rem;
            border-radius: 0.625rem;
            font-weight: 600;
            font-size: 0.9375rem;
            border: 0;
            cursor: pointer;
            transition: all 180ms;
            box-shadow: 0 4px 12px rgba(65, 84, 241, 0.25);
        }
        .auth-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(65, 84, 241, 0.4);
        }
        .auth-btn-primary:active { transform: translateY(0); }

        /* ─── Danger card (delete account) ─── */
        .acc-card-danger {
            background: linear-gradient(180deg, rgba(127, 29, 29, 0.18) 0%, rgba(127, 29, 29, 0.06) 100%);
            border: 1px solid rgba(239, 68, 68, 0.35);
        }
        .acc-card-danger .acc-card-head { border-bottom-color: rgba(239, 68, 68, 0.22); }
        .acc-card-danger .acc-card-title { color: #fca5a5; }

        .auth-btn-danger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 0.6875rem 1.25rem;
            border-radius: 0.625rem;
            font-weight: 600;
            font-size: 0.9375rem;
            border: 0;
            cursor: pointer;
            transition: all 180ms;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
        }
        .auth-btn-danger:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
        }
        .auth-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
            background: transparent;
            color: #cbd5e1;
            padding: 0.6875rem 1.25rem;
            border-radius: 0.625rem;
            font-weight: 600;
            font-size: 0.9375rem;
            border: 1.5px solid #475569;
            cursor: pointer;
            transition: all 150ms;
        }
        .auth-btn-secondary:hover { border-color: #64748b; color: white; }

        .danger-warn {
            display: flex;
            gap: 0.625rem;
            padding: 0.875rem;
            background: rgba(127, 29, 29, 0.18);
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-radius: 0.625rem;
            font-size: 0.8125rem;
            color: #fecaca;
            line-height: 1.55;
            margin-bottom: 1.25rem;
        }
        .danger-warn-icon {
            flex-shrink: 0;
            color: #f87171;
            margin-top: 0.125rem;
        }

        /* ─── Modal ─── */
        [x-cloak] { display: none !important; }
        .acc-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(2, 6, 23, 0.7);
            backdrop-filter: blur(4px);
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .acc-modal-panel {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            border: 1px solid #334155;
            border-radius: 1rem;
            padding: 1.75rem;
            max-width: 28rem;
            width: 100%;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
            animation: accModalIn 220ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes accModalIn {
            from { opacity: 0; transform: scale(0.95) translateY(8px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        .acc-modal-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 9999px;
            background: rgba(239, 68, 68, 0.18);
            color: #fca5a5;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .acc-modal-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: white;
            margin: 0 0 0.5rem;
            letter-spacing: -0.005em;
        }
        .acc-modal-desc {
            font-size: 0.875rem;
            color: #cbd5e1;
            margin: 0 0 1.25rem;
            line-height: 1.55;
        }
        .acc-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.625rem;
            margin-top: 1.5rem;
        }
        @media (max-width: 480px) {
            .acc-modal-actions { flex-direction: column-reverse; }
            .acc-modal-actions > * { width: 100%; }
        }

        /* ─── Status pill ─── */
        .acc-saved {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            color: #34d399;
            font-size: 0.8125rem;
            font-weight: 600;
            margin-left: 1rem;
        }
    </style>
</head>
<body>
    <header class="acc-topbar">
        <a href="/" class="acc-brand">
            <span class="acc-brand-icon">
                <svg style="width:1rem; height:1rem;" fill="currentColor" viewBox="0 0 24 24"><path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/></svg>
            </span>
            AI Quiz
        </a>
        <a href="{{ route('dashboard') }}" class="acc-back">
            <svg style="width:1rem; height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Back to dashboard
        </a>
    </header>

    <main class="acc-shell">
        <div class="acc-header">
            <span class="acc-tag">Your account</span>
            <h1 class="acc-h1">@yield('heading', 'Account settings')</h1>
            <p class="acc-sub">@yield('subheading', 'Manage your profile information, avatar, and password.')</p>
        </div>

        {{ $slot ?? '' }}
        @yield('content')
    </main>
</body>
</html>
