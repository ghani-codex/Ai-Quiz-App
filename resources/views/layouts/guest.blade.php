<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sign in') · {{ config('app.name', 'AI Quiz') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

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

        /* ─── Two-column shell ─── */
        .auth-wrap {
            display: grid;
            grid-template-columns: 1fr;
            min-height: 100vh;
        }
        @media (min-width: 1024px) {
            .auth-wrap { grid-template-columns: minmax(420px, 5fr) 7fr; }
        }

        /* ─── Brand panel (left) ─── */
        .auth-brand {
            background: linear-gradient(135deg, #4154f1 0%, #2d3aa5 100%);
            color: white;
            padding: 2rem 1.5rem;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 30vh;
        }
        @media (min-width: 1024px) {
            .auth-brand { padding: 3rem 3.5rem; min-height: 100vh; }
        }
        .auth-brand::before {
            content: '';
            position: absolute;
            top: -25%;
            right: -25%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, transparent 70%);
            pointer-events: none;
        }
        .auth-brand::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -25%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .auth-brand > * { position: relative; z-index: 1; }

        .auth-brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            font-size: 1.375rem;
            font-weight: 800;
            color: white;
            text-decoration: none;
            letter-spacing: -0.015em;
        }
        .auth-brand-logo-icon {
            width: 2.25rem;
            height: 2.25rem;
            background: rgba(255,255,255,0.2);
            border-radius: 0.625rem;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(8px);
        }

        .auth-brand-hero { margin-top: 2rem; }
        .auth-brand-tag {
            display: inline-block;
            padding: 0.25rem 0.625rem;
            background: rgba(255,255,255,0.15);
            border-radius: 9999px;
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 1rem;
            backdrop-filter: blur(8px);
        }
        .auth-brand-hero h1 {
            font-size: 1.625rem;
            font-weight: 800;
            line-height: 1.2;
            margin: 0 0 0.875rem;
            letter-spacing: -0.02em;
        }
        @media (min-width: 768px) { .auth-brand-hero h1 { font-size: 2rem; } }
        @media (min-width: 1280px) { .auth-brand-hero h1 { font-size: 2.25rem; } }
        .auth-brand-hero p {
            font-size: 0.9375rem;
            line-height: 1.6;
            opacity: 0.92;
            margin: 0;
            max-width: 28rem;
        }

        .auth-brand-features {
            display: none;
            margin-top: 2rem;
            list-style: none;
            padding: 0;
        }
        @media (min-width: 1024px) { .auth-brand-features { display: block; } }
        .auth-brand-features li {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            margin-bottom: 0.625rem;
            font-size: 0.9375rem;
        }
        .auth-brand-features li svg {
            width: 1.125rem;
            height: 1.125rem;
            background: rgba(255,255,255,0.2);
            border-radius: 9999px;
            padding: 0.25rem;
            flex-shrink: 0;
        }

        .auth-brand-foot {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 1.5rem;
        }
        @media (max-width: 1023px) { .auth-brand-foot { display: none; } }

        /* ─── Form panel (right) ─── */
        .auth-form-side {
            background: #0f172a;
            padding: 2rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @media (min-width: 1024px) { .auth-form-side { padding: 3rem 3rem; } }

        .auth-card {
            width: 100%;
            max-width: 26rem;
        }
        .auth-title {
            font-size: 1.625rem;
            font-weight: 800;
            margin: 0 0 0.375rem;
            color: white;
            letter-spacing: -0.02em;
        }
        .auth-subtitle {
            color: #9ca3af;
            font-size: 0.9375rem;
            margin: 0 0 1.75rem;
            line-height: 1.5;
        }

        /* ─── Form fields ─── */
        .auth-field { margin-bottom: 1rem; }
        .auth-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #d1d5db;
            margin-bottom: 0.375rem;
            letter-spacing: 0.01em;
        }
        .auth-input {
            width: 100%;
            padding: 0.75rem 0.875rem;
            border: 1.5px solid #334155;
            border-radius: 0.625rem;
            background: rgba(15, 23, 42, 0.6);
            color: #f3f4f6;
            font-size: 0.9375rem;
            font-family: inherit;
            transition: all 150ms;
        }
        .auth-input::placeholder { color: #6b7280; }
        .auth-input:focus {
            outline: none;
            border-color: #4154f1;
            background: rgba(15, 23, 42, 0.85);
            box-shadow: 0 0 0 4px rgba(65, 84, 241, 0.15);
        }
        .auth-input:hover:not(:focus) { border-color: #475569; }
        .auth-error {
            font-size: 0.8125rem;
            color: #fca5a5;
            margin-top: 0.375rem;
        }

        /* Inline row (e.g. remember + forgot) */
        .auth-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
        }
        .auth-check {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #9ca3af;
            cursor: pointer;
            user-select: none;
        }
        .auth-check input {
            width: 1rem;
            height: 1rem;
            accent-color: #4154f1;
            cursor: pointer;
        }
        .auth-link {
            color: #818cf8;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: color 120ms;
        }
        .auth-link:hover { color: #a5b4fc; }

        /* ─── Buttons ─── */
        .auth-btn-primary {
            width: 100%;
            background: linear-gradient(135deg, #4154f1 0%, #3743cc 100%);
            color: white;
            padding: 0.8125rem 1.25rem;
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

        /* ─── Role selector cards ─── */
        .role-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.625rem;
            margin-bottom: 1.25rem;
        }
        .role-card {
            display: block;
            padding: 0.875rem 1rem;
            border: 1.5px solid #334155;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 140ms;
            text-align: left;
            background: rgba(15, 23, 42, 0.6);
            position: relative;
        }
        .role-card:hover { border-color: #4f5b9a; transform: translateY(-1px); }
        .role-card input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .role-card:has(input:checked) {
            border-color: #4154f1;
            background: rgba(65, 84, 241, 0.12);
            box-shadow: 0 4px 14px rgba(65, 84, 241, 0.2);
        }
        .role-card-head {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
        }
        .role-card-icon {
            width: 1.75rem;
            height: 1.75rem;
            border-radius: 0.5rem;
            background: rgba(65, 84, 241, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #818cf8;
        }
        .role-card:has(input:checked) .role-card-icon {
            background: #4154f1;
            color: white;
        }
        .role-card-title {
            font-weight: 700;
            font-size: 0.875rem;
            color: white;
        }
        .role-card-desc {
            font-size: 0.75rem;
            color: #94a3b8;
            line-height: 1.4;
        }
        .role-card-tick {
            position: absolute;
            top: 0.625rem;
            right: 0.625rem;
            width: 1.125rem;
            height: 1.125rem;
            border-radius: 9999px;
            background: #4154f1;
            color: white;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .role-card:has(input:checked) .role-card-tick { display: flex; }

        /* ─── Footer link ─── */
        .auth-foot {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: #9ca3af;
        }

        /* ─── Session status / alerts ─── */
        .auth-status {
            background: rgba(16, 185, 129, 0.1);
            color: #34d399;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body>
    <div class="auth-wrap">
        {{-- Brand panel --}}
        <aside class="auth-brand">
            <div>
                <a href="/" class="auth-brand-logo">
                    <span class="auth-brand-logo-icon">
                        <svg style="width:1.25rem; height:1.25rem;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/>
                        </svg>
                    </span>
                    AI Quiz
                </a>
            </div>

            <div class="auth-brand-hero">
                <span class="auth-brand-tag">Smart learning platform</span>
                <h1>Learn smarter with Agent-powered quizzes</h1>
                <p>Upload any lecture in PDF, DOCX, or text. Get auto-generated multiple-choice questions and personalised feedback in seconds.</p>

                <ul class="auth-brand-features">
                    <li>
                        <svg fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        PDF / DOCX text extraction
                    </li>
                    <li>
                        <svg fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Agent-generated MCQs
                    </li>
                    <li>
                        <svg fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Per-student weak-area reports
                    </li>
                </ul>
            </div>

            <div class="auth-brand-foot">
                Final Year Project · The Islamia University of Bahawalpur
            </div>
        </aside>

        {{-- Form panel --}}
        <main class="auth-form-side">
            <div class="auth-card">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
