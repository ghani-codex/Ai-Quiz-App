<x-filament-panels::page>
    @php
        $score = (float) ($attempt->score ?? 0);
        $color = $this->getScoreColor();
        $verdict = $this->getScoreVerdict();
        $correctCount = $attempt->answers->where('is_correct', true)->count();
        $totalQuestions = $attempt->quiz->questions->count();
        $incorrectCount = $totalQuestions - $correctCount;
        $minutes = floor(($attempt->time_taken_seconds ?? 0) / 60);
        $seconds = ($attempt->time_taken_seconds ?? 0) % 60;
        $passed = $score >= 50;

        // Ring math
        $radius = 70;
        $circumference = 2 * M_PI * $radius;
        $dashOffset = $circumference * (1 - $score / 100);

        $accent = match ($color) {
            'success' => ['#10b981', '#059669', 'rgb(220 252 231)', 'rgb(22 101 52)'],
            'warning' => ['#f59e0b', '#d97706', 'rgb(254 243 199)', 'rgb(180 83 9)'],
            'danger'  => ['#ef4444', '#dc2626', 'rgb(254 226 226)', 'rgb(153 27 27)'],
            default   => ['#6366f1', '#4f46e5', 'rgb(238 242 255)', 'rgb(67 56 202)'],
        };
        $ringFrom = $accent[0];
        $ringTo   = $accent[1];
        $pillBg   = $accent[2];
        $pillFg   = $accent[3];
    @endphp

    <style>
        :root {
            --results-ring-from: {{ $ringFrom }};
            --results-ring-to: {{ $ringTo }};
        }
        [x-cloak] { display: none !important; }

        /* ─── Hero ─── */
        .results-hero {
            position: relative;
            border-radius: 1.25rem;
            padding: 1.75rem;
            margin-bottom: 1.25rem;
            background: linear-gradient(135deg, {{ $ringFrom }} 0%, {{ $ringTo }} 100%);
            color: white;
            overflow: hidden;
            box-shadow: 0 12px 32px rgba(0,0,0,0.18);
        }
        .results-hero::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -10%;
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, transparent 70%);
            pointer-events: none;
        }
        @media (min-width: 768px) { .results-hero { padding: 2.25rem; } }

        .results-hero-grid {
            position: relative;
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.75rem;
            align-items: center;
        }
        @media (min-width: 768px) {
            .results-hero-grid { grid-template-columns: auto 1fr; gap: 2.5rem; }
        }

        /* Circular score ring */
        .score-ring-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .score-ring {
            transform: rotate(-90deg);
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.2));
        }
        .score-ring-bg { stroke: rgba(255,255,255,0.18); }
        .score-ring-fg {
            stroke: white;
            stroke-linecap: round;
            transition: stroke-dashoffset 1s cubic-bezier(0.4, 0, 0.2, 1);
            animation: ringDraw 1.4s cubic-bezier(0.4, 0, 0.2, 1) both;
        }
        @keyframes ringDraw {
            from { stroke-dashoffset: {{ $circumference }}; }
            to   { stroke-dashoffset: {{ $dashOffset }}; }
        }
        .score-ring-text {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .score-ring-num {
            font-size: 2.75rem;
            font-weight: 800;
            line-height: 1;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.025em;
        }
        .score-ring-of {
            font-size: 0.8125rem;
            opacity: 0.85;
            margin-top: 0.25rem;
            letter-spacing: 0.05em;
        }

        /* Hero text side */
        .results-hero-body { min-width: 0; }
        .results-breadcrumb {
            font-size: 0.75rem;
            opacity: 0.85;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 600;
        }
        .results-title {
            font-size: 1.375rem;
            font-weight: 800;
            margin: 0.25rem 0 0.875rem;
            line-height: 1.2;
        }
        @media (min-width: 768px) { .results-title { font-size: 1.75rem; } }

        .results-stat-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
            margin-top: 1rem;
        }
        @media (min-width: 768px) { .results-stat-grid { grid-template-columns: repeat(3, 1fr); } }

        .results-stat {
            background: rgba(255,255,255,0.14);
            backdrop-filter: blur(8px);
            border-radius: 0.625rem;
            padding: 0.625rem 0.875rem;
            border: 1px solid rgba(255,255,255,0.08);
        }
        .results-stat-label {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            opacity: 0.85;
            font-weight: 600;
        }
        .results-stat-value {
            font-size: 1.125rem;
            font-weight: 700;
            margin-top: 0.125rem;
            font-variant-numeric: tabular-nums;
        }
        .results-verdict {
            margin-top: 1.125rem;
            font-size: 0.9375rem;
            line-height: 1.5;
            opacity: 0.95;
        }
        .results-verdict strong { font-weight: 700; }

        .results-actions { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 1.25rem; }
        .results-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.625rem 1.125rem;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.22);
            color: white;
            border-radius: 0.625rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 150ms;
            backdrop-filter: blur(8px);
        }
        .results-btn:hover { background: rgba(255,255,255,0.28); color: white; }
        .results-btn-primary { background: white; color: {{ $ringTo }}; }
        .results-btn-primary:hover { background: rgba(255,255,255,0.92); color: {{ $ringTo }}; }

        /* ─── Summary stripe ─── */
        .summary-stripe {
            background: white;
            border: 1px solid rgb(229 231 235);
            border-radius: 0.875rem;
            padding: 0.875rem 1rem;
            margin-bottom: 1.25rem;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }
        @media (min-width: 768px) { .summary-stripe { grid-template-columns: repeat(4, 1fr); } }
        .dark .summary-stripe { background: rgb(17 24 39); border-color: rgb(31 41 55); }
        .summary-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.375rem 0.5rem;
        }
        .summary-icon {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .summary-icon.green { background: rgb(220 252 231); color: rgb(22 101 52); }
        .dark .summary-icon.green { background: rgba(16,185,129,0.18); color: rgb(110 231 183); }
        .summary-icon.red { background: rgb(254 226 226); color: rgb(153 27 27); }
        .dark .summary-icon.red { background: rgba(239,68,68,0.18); color: rgb(252 165 165); }
        .summary-icon.blue { background: rgb(219 234 254); color: rgb(30 64 175); }
        .dark .summary-icon.blue { background: rgba(59,130,246,0.18); color: rgb(147 197 253); }
        .summary-icon.amber { background: rgb(254 243 199); color: rgb(180 83 9); }
        .dark .summary-icon.amber { background: rgba(245,158,11,0.18); color: rgb(252 211 77); }
        .summary-text { line-height: 1.2; min-width: 0; }
        .summary-num { font-size: 1.125rem; font-weight: 700; color: rgb(17 24 39); font-variant-numeric: tabular-nums; }
        .dark .summary-num { color: rgb(243 244 246); }
        .summary-lbl { font-size: 0.75rem; color: rgb(107 114 128); margin-top: 0.125rem; }

        /* ─── Question breakdown ─── */
        .breakdown-card {
            background: white;
            border: 1px solid rgb(229 231 235);
            border-radius: 0.875rem;
            padding: 1.5rem;
        }
        .dark .breakdown-card { background: rgb(17 24 39); border-color: rgb(31 41 55); }

        .breakdown-header { margin-bottom: 1.25rem; }
        .breakdown-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: rgb(17 24 39);
        }
        .dark .breakdown-title { color: rgb(243 244 246); }
        .breakdown-sub { font-size: 0.875rem; color: rgb(107 114 128); margin-top: 0.25rem; }

        /* Per-question item */
        .q-item {
            border: 1.5px solid rgb(229 231 235);
            border-radius: 0.875rem;
            padding: 1.25rem;
            margin-bottom: 1rem;
            background: white;
            transition: border-color 150ms;
        }
        .dark .q-item { background: rgb(15 23 42); border-color: rgb(31 41 55); }
        .q-item.correct { border-color: rgb(167 243 208); background: linear-gradient(to bottom, rgb(240 253 244), white); }
        .dark .q-item.correct { background: linear-gradient(to bottom, rgba(16,185,129,0.08), rgb(15 23 42)); border-color: rgba(16,185,129,0.35); }
        .q-item.incorrect { border-color: rgb(254 202 202); background: linear-gradient(to bottom, rgb(254 242 242), white); }
        .dark .q-item.incorrect { background: linear-gradient(to bottom, rgba(239,68,68,0.08), rgb(15 23 42)); border-color: rgba(239,68,68,0.35); }
        .q-item.unanswered { border-color: rgb(229 231 235); }

        .q-head {
            display: flex;
            align-items: flex-start;
            gap: 0.875rem;
            margin-bottom: 1rem;
        }
        .q-status {
            flex-shrink: 0;
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }
        .q-status.green { background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 10px rgba(16,185,129,0.25); }
        .q-status.red { background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 10px rgba(239,68,68,0.25); }
        .q-status.gray { background: linear-gradient(135deg, #9ca3af, #6b7280); }

        .q-meta {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: rgb(107 114 128);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            margin-bottom: 0.375rem;
        }
        .q-meta-pill {
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.6875rem;
        }
        .q-meta-pill.green { background: rgb(220 252 231); color: rgb(22 101 52); }
        .dark .q-meta-pill.green { background: rgba(16,185,129,0.2); color: rgb(110 231 183); }
        .q-meta-pill.red { background: rgb(254 226 226); color: rgb(153 27 27); }
        .dark .q-meta-pill.red { background: rgba(239,68,68,0.2); color: rgb(252 165 165); }
        .q-meta-pill.gray { background: rgb(243 244 246); color: rgb(75 85 99); }
        .dark .q-meta-pill.gray { background: rgb(55 65 81); color: rgb(209 213 219); }

        .q-text {
            font-size: 1rem;
            font-weight: 600;
            color: rgb(17 24 39);
            line-height: 1.5;
        }
        .dark .q-text { color: rgb(243 244 246); }
        @media (min-width: 768px) { .q-text { font-size: 1.0625rem; } }

        /* Options inside breakdown */
        .q-opts { display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem; }
        .q-opt {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.75rem 0.875rem;
            border-radius: 0.625rem;
            background: rgb(249 250 251);
            border: 1px solid rgb(229 231 235);
            font-size: 0.875rem;
            color: rgb(75 85 99);
        }
        .dark .q-opt { background: rgb(30 41 59); border-color: rgb(51 65 85); color: rgb(203 213 225); }
        .q-opt.correct {
            background: linear-gradient(to right, rgb(220 252 231), rgb(240 253 244));
            border-color: rgb(134 239 172);
            color: rgb(22 101 52);
            font-weight: 600;
        }
        .dark .q-opt.correct {
            background: linear-gradient(to right, rgba(16,185,129,0.18), rgba(16,185,129,0.05));
            border-color: rgba(16,185,129,0.45);
            color: rgb(167 243 208);
        }
        .q-opt.your-wrong {
            background: linear-gradient(to right, rgb(254 226 226), rgb(254 242 242));
            border-color: rgb(252 165 165);
            color: rgb(153 27 27);
            font-weight: 600;
        }
        .dark .q-opt.your-wrong {
            background: linear-gradient(to right, rgba(239,68,68,0.18), rgba(239,68,68,0.05));
            border-color: rgba(239,68,68,0.45);
            color: rgb(252 165 165);
        }
        .q-opt-icon {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .q-opt.correct .q-opt-icon { background: rgb(16 185 129); color: white; }
        .q-opt.your-wrong .q-opt-icon { background: rgb(239 68 68); color: white; }
        .q-opt:not(.correct):not(.your-wrong) .q-opt-icon {
            background: rgb(229 231 235); color: rgb(107 114 128);
        }
        .dark .q-opt:not(.correct):not(.your-wrong) .q-opt-icon {
            background: rgb(51 65 85); color: rgb(148 163 184);
        }
        .q-opt-text { flex: 1; }
        .q-opt-tag {
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            white-space: nowrap;
        }
        .q-opt.correct .q-opt-tag { background: rgba(16,185,129,0.15); color: rgb(6 78 59); }
        .dark .q-opt.correct .q-opt-tag { background: rgba(255,255,255,0.12); color: rgb(167 243 208); }
        .q-opt.your-wrong .q-opt-tag { background: rgba(239,68,68,0.15); color: rgb(127 29 29); }
        .dark .q-opt.your-wrong .q-opt-tag { background: rgba(255,255,255,0.12); color: rgb(252 165 165); }

        /* Explanation box */
        .q-explanation {
            margin-top: 0.875rem;
            padding: 0.875rem 1rem;
            background: linear-gradient(to right, rgb(239 246 255), rgb(238 242 255));
            border: 1px solid rgb(191 219 254);
            border-radius: 0.625rem;
            display: flex;
            gap: 0.75rem;
        }
        .dark .q-explanation {
            background: linear-gradient(to right, rgba(59,130,246,0.1), rgba(99,102,241,0.1));
            border-color: rgba(59,130,246,0.3);
        }
        .q-explanation-icon {
            width: 1.75rem;
            height: 1.75rem;
            border-radius: 0.5rem;
            background: rgb(59 130 246);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .q-explanation-content { flex: 1; min-width: 0; }
        .q-explanation-label {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 700;
            color: rgb(30 64 175);
            margin-bottom: 0.25rem;
        }
        .dark .q-explanation-label { color: rgb(147 197 253); }
        .q-explanation-text {
            font-size: 0.875rem;
            line-height: 1.55;
            color: rgb(30 58 138);
        }
        .dark .q-explanation-text { color: rgb(191 219 254); }
    </style>

    {{-- ═══════════════ HERO ═══════════════ --}}
    <div class="results-hero">
        <div class="results-hero-grid">
            {{-- Circular score ring --}}
            <div class="score-ring-wrap">
                <svg class="score-ring" width="170" height="170" viewBox="0 0 160 160">
                    <circle class="score-ring-bg" cx="80" cy="80" r="{{ $radius }}" stroke-width="12" fill="none"/>
                    <circle class="score-ring-fg" cx="80" cy="80" r="{{ $radius }}" stroke-width="12" fill="none"
                            stroke-dasharray="{{ $circumference }}"
                            stroke-dashoffset="{{ $dashOffset }}"/>
                </svg>
                <div class="score-ring-text">
                    <div class="score-ring-num">{{ number_format($score, $score == (int) $score ? 0 : 1) }}<span style="font-size:1.25rem; opacity:0.7;">%</span></div>
                    <div class="score-ring-of">{{ $passed ? 'PASSED' : 'KEEP TRYING' }}</div>
                </div>
            </div>

            {{-- Right side: title + stats --}}
            <div class="results-hero-body">
                <p class="results-breadcrumb">{{ $attempt->quiz->lecture?->course?->title }} · {{ $attempt->quiz->lecture?->title }}</p>
                <h2 class="results-title">{{ $attempt->quiz->title }}</h2>

                <div class="results-stat-grid">
                    <div class="results-stat">
                        <div class="results-stat-label">Correct</div>
                        <div class="results-stat-value">{{ $correctCount }} <span style="opacity:0.7; font-size:0.875rem;">/ {{ $totalQuestions }}</span></div>
                    </div>
                    <div class="results-stat">
                        <div class="results-stat-label">Time taken</div>
                        <div class="results-stat-value">{{ $minutes }}:{{ str_pad($seconds, 2, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="results-stat">
                        <div class="results-stat-label">Difficulty</div>
                        <div class="results-stat-value" style="text-transform: capitalize;">{{ $attempt->quiz->difficulty }}</div>
                    </div>
                </div>

                <p class="results-verdict"><strong>{{ $verdict }}.</strong></p>

                <div class="results-actions">
                    <a href="{{ \App\Filament\Student\Pages\TakeQuiz::getUrl(['quiz' => $attempt->quiz_id]) }}" class="results-btn results-btn-primary">
                        <svg style="width:1rem; height:1rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582M20 20v-5h-.581M5.064 9A8.001 8.001 0 0119.418 9M19.418 15A8.001 8.001 0 014.582 15"/></svg>
                        Retake quiz
                    </a>
                    <a href="{{ \App\Filament\Student\Resources\AvailableQuizResource::getUrl() }}" class="results-btn">
                        Browse more quizzes →
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════ SUMMARY STRIPE ═══════════════ --}}
    <div class="summary-stripe">
        <div class="summary-cell">
            <div class="summary-icon green">
                <svg style="width:1.25rem; height:1.25rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="summary-text">
                <div class="summary-num">{{ $correctCount }}</div>
                <div class="summary-lbl">Correct</div>
            </div>
        </div>
        <div class="summary-cell">
            <div class="summary-icon red">
                <svg style="width:1.25rem; height:1.25rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div class="summary-text">
                <div class="summary-num">{{ $incorrectCount }}</div>
                <div class="summary-lbl">Incorrect</div>
            </div>
        </div>
        <div class="summary-cell">
            <div class="summary-icon blue">
                <svg style="width:1.25rem; height:1.25rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"/></svg>
            </div>
            <div class="summary-text">
                <div class="summary-num">{{ $minutes }}:{{ str_pad($seconds, 2, '0', STR_PAD_LEFT) }}</div>
                <div class="summary-lbl">Time taken</div>
            </div>
        </div>
        <div class="summary-cell">
            <div class="summary-icon amber">
                <svg style="width:1.25rem; height:1.25rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L2.05 10.1c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.673z"/></svg>
            </div>
            <div class="summary-text">
                <div class="summary-num">{{ number_format($score, 1) }}<span style="font-size:0.875rem; color:rgb(107 114 128);">/100</span></div>
                <div class="summary-lbl">Final score</div>
            </div>
        </div>
    </div>

    {{-- ═══════════════ QUESTION BREAKDOWN ═══════════════ --}}
    <div class="breakdown-card">
        <div class="breakdown-header">
            <h3 class="breakdown-title">Question breakdown</h3>
            <p class="breakdown-sub">Review each answer and learn from the explanations below.</p>
        </div>

        @foreach ($attempt->quiz->questions as $i => $question)
            @php
                $answer = $attempt->answers->firstWhere('question_id', $question->id);
                $selectedId = $answer?->selected_option_id;
                $isCorrect = (bool) ($answer?->is_correct ?? false);
                $unanswered = $selectedId === null;
                $itemClass = $isCorrect ? 'correct' : ($unanswered ? 'unanswered' : 'incorrect');
                $statusColor = $isCorrect ? 'green' : ($unanswered ? 'gray' : 'red');
                $statusLabel = $isCorrect ? 'Correct' : ($unanswered ? 'Not answered' : 'Incorrect');
            @endphp

            <div class="q-item {{ $itemClass }}">
                <div class="q-head">
                    <div class="q-status {{ $statusColor }}">
                        @if ($isCorrect)
                            <svg style="width:1.25rem; height:1.25rem;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @elseif ($unanswered)
                            <span>?</span>
                        @else
                            <svg style="width:1.25rem; height:1.25rem;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        @endif
                    </div>

                    <div style="flex:1; min-width:0;">
                        <div class="q-meta">
                            <span>Question {{ $i + 1 }} of {{ $totalQuestions }}</span>
                            <span class="q-meta-pill {{ $statusColor }}">{{ $statusLabel }}</span>
                            @if (! $unanswered)
                                <span style="color: rgb(107 114 128); text-transform: none; font-weight: 500;">
                                    +{{ $answer->points_awarded }}/{{ $question->points }} pt{{ $question->points > 1 ? 's' : '' }}
                                </span>
                            @endif
                        </div>
                        <p class="q-text">{{ $question->text }}</p>
                    </div>
                </div>

                <div class="q-opts">
                    @foreach ($question->options as $option)
                        @php
                            $isUserPick = $selectedId === $option->id;
                            $isAnswerKey = $option->is_correct;
                            $optClass = $isAnswerKey ? 'correct' : ($isUserPick && ! $isAnswerKey ? 'your-wrong' : '');
                        @endphp
                        <div class="q-opt {{ $optClass }}">
                            <div class="q-opt-icon">
                                @if ($isAnswerKey)
                                    <svg style="width:0.875rem; height:0.875rem;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                @elseif ($isUserPick)
                                    <svg style="width:0.875rem; height:0.875rem;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                @else
                                    {{ chr(65 + $loop->index) }}
                                @endif
                            </div>
                            <span class="q-opt-text">{{ $option->text }}</span>
                            @if ($isAnswerKey)
                                <span class="q-opt-tag">Correct answer</span>
                            @elseif ($isUserPick)
                                <span class="q-opt-tag">Your answer</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if ($question->explanation)
                    <div class="q-explanation">
                        <div class="q-explanation-icon">
                            <svg style="width:1rem; height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <div class="q-explanation-content">
                            <div class="q-explanation-label">Why this answer?</div>
                            <p class="q-explanation-text">{{ $question->explanation }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
