<x-filament-panels::page>
    @php
        $total = count($questions);
        $current = $questions[$currentIndex] ?? null;
        $progress = $total > 0 ? (($currentIndex + 1) / $total) * 100 : 0;
        $answered = count($answers);
    @endphp

    <style>
        /* Scoped quiz styles — bypass Tailwind purge issues for non-bundled variants */
        .quiz-shell { --quiz-accent: #4154f1; }
        .dark .quiz-shell { --quiz-accent: #818cf8; }

        /* ─── Sticky header ─── */
        .quiz-header {
            position: sticky;
            top: 0;
            z-index: 30;
            margin: -1.5rem -1rem 1.25rem -1rem;
            padding: 0.875rem 1.25rem 1rem;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgb(229 231 235);
        }
        .dark .quiz-header {
            background: rgba(17, 24, 39, 0.92);
            border-bottom-color: rgb(31 41 55);
        }
        .quiz-header-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
        }
        .quiz-breadcrumb {
            font-size: 0.7rem;
            color: rgb(107 114 128);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 500;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .quiz-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: rgb(17 24 39);
            margin-top: 0.125rem;
        }
        .dark .quiz-title { color: rgb(243 244 246); }
        @media (min-width: 768px) { .quiz-title { font-size: 1.375rem; } }
        .quiz-counter {
            font-size: 0.75rem;
            color: rgb(107 114 128);
            margin-top: 0.25rem;
        }
        .quiz-timer-block { text-align: right; flex-shrink: 0; }
        .quiz-timer-label {
            font-size: 0.7rem;
            color: rgb(107 114 128);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 500;
        }
        .quiz-timer-value {
            font-size: 1.5rem;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            color: var(--quiz-accent);
            line-height: 1.1;
        }
        @media (min-width: 768px) { .quiz-timer-value { font-size: 1.875rem; } }
        .quiz-timer-value.danger { color: rgb(220 38 38); animation: timerPulse 1s infinite; }
        .quiz-timer-value.warning { color: rgb(217 119 6); }
        @keyframes timerPulse {
            0%, 100% { opacity: 1; }
            50%      { opacity: 0.6; }
        }
        .quiz-progress {
            margin-top: 0.75rem;
            height: 0.375rem;
            background: rgb(229 231 235);
            border-radius: 9999px;
            overflow: hidden;
        }
        .dark .quiz-progress { background: rgb(31 41 55); }
        .quiz-progress-fill {
            height: 100%;
            background: var(--quiz-accent);
            transition: width 0.3s ease;
        }

        /* ─── Question card ─── */
        .quiz-card {
            background: white;
            border: 1px solid rgb(229 231 235);
            border-radius: 0.875rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .dark .quiz-card {
            background: rgb(17 24 39);
            border-color: rgb(31 41 55);
        }
        @media (min-width: 768px) { .quiz-card { padding: 2rem; } }

        .quiz-q-meta {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgb(238 242 255);
            color: var(--quiz-accent);
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.025em;
            margin-bottom: 1rem;
        }
        .dark .quiz-q-meta {
            background: rgba(99, 102, 241, 0.18);
            color: rgb(165 180 252);
        }
        .quiz-q-text {
            font-size: 1.125rem;
            font-weight: 600;
            line-height: 1.6;
            color: rgb(17 24 39);
            margin-bottom: 1.5rem;
        }
        .dark .quiz-q-text { color: rgb(243 244 246); }
        @media (min-width: 768px) { .quiz-q-text { font-size: 1.25rem; } }

        /* ─── Option buttons ─── */
        .quiz-options { display: flex; flex-direction: column; gap: 0.75rem; }
        .quiz-option {
            width: 100%;
            text-align: left;
            padding: 1rem 1.125rem;
            border-radius: 0.75rem;
            border: 2px solid rgb(229 231 235);
            background: white;
            color: rgb(17 24 39);
            cursor: pointer;
            transition: border-color 150ms, background-color 150ms, transform 80ms;
            display: flex;
            align-items: center;
            gap: 0.875rem;
            font: inherit;
        }
        .dark .quiz-option {
            border-color: rgb(55 65 81);
            background: rgb(17 24 39);
            color: rgb(229 231 235);
        }
        .quiz-option:hover {
            border-color: rgb(165 180 252);
            background: rgb(248 250 252);
            transform: translateY(-1px);
        }
        .dark .quiz-option:hover {
            border-color: rgb(99 102 241);
            background: rgb(30 41 59);
        }
        .quiz-option:active { transform: translateY(0); }
        .quiz-option.is-selected {
            border-color: var(--quiz-accent);
            background: rgb(238 242 255);
            box-shadow: 0 4px 14px rgba(65, 84, 241, 0.18);
        }
        .dark .quiz-option.is-selected {
            background: rgba(99, 102, 241, 0.14);
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.28);
        }
        .quiz-letter {
            flex-shrink: 0;
            width: 2rem;
            height: 2rem;
            border-radius: 9999px;
            border: 2px solid rgb(209 213 219);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 700;
            color: rgb(107 114 128);
            transition: all 150ms;
        }
        .dark .quiz-letter {
            border-color: rgb(75 85 99);
            color: rgb(156 163 175);
        }
        .quiz-option.is-selected .quiz-letter {
            background: var(--quiz-accent);
            border-color: var(--quiz-accent);
            color: white;
        }
        .quiz-option-text {
            font-size: 0.95rem;
            line-height: 1.5;
            flex: 1;
        }
        @media (min-width: 768px) { .quiz-option-text { font-size: 1rem; } }

        /* ─── Dot navigator ─── */
        .quiz-dots-card {
            background: white;
            border: 1px solid rgb(229 231 235);
            border-radius: 0.875rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
        }
        .dark .quiz-dots-card {
            background: rgb(17 24 39);
            border-color: rgb(31 41 55);
        }
        .quiz-dots-label {
            font-size: 0.7rem;
            color: rgb(107 114 128);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            margin-bottom: 0.625rem;
        }
        .quiz-dots { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .quiz-dot {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 0.5rem;
            background: rgb(243 244 246);
            color: rgb(107 114 128);
            border: 1px solid transparent;
            font-size: 0.875rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 120ms;
        }
        .dark .quiz-dot { background: rgb(31 41 55); color: rgb(156 163 175); }
        .quiz-dot:hover { background: rgb(229 231 235); }
        .dark .quiz-dot:hover { background: rgb(55 65 81); }
        .quiz-dot.answered {
            background: rgb(220 252 231);
            color: rgb(22 101 52);
        }
        .dark .quiz-dot.answered {
            background: rgba(16, 185, 129, 0.18);
            color: rgb(110 231 183);
        }
        .quiz-dot.current {
            background: var(--quiz-accent);
            color: white;
            box-shadow: 0 0 0 3px rgba(65, 84, 241, 0.25);
        }

        /* ─── Bottom action bar ─── */
        .quiz-actions {
            position: sticky;
            bottom: 0;
            z-index: 20;
            margin: 1rem -1rem -1.5rem;
            padding: 0.875rem 1.25rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgb(229 231 235);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }
        .dark .quiz-actions {
            background: rgba(17, 24, 39, 0.95);
            border-top-color: rgb(31 41 55);
        }
        .quiz-btn {
            padding: 0.625rem 1.25rem;
            border-radius: 0.625rem;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 120ms;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
        }
        .quiz-btn-secondary {
            background: white;
            border-color: rgb(209 213 219);
            color: rgb(55 65 81);
        }
        .dark .quiz-btn-secondary {
            background: rgb(31 41 55);
            border-color: rgb(55 65 81);
            color: rgb(229 231 235);
        }
        .quiz-btn-secondary:hover { background: rgb(249 250 251); }
        .dark .quiz-btn-secondary:hover { background: rgb(55 65 81); }
        .quiz-btn-secondary:disabled { opacity: 0.4; cursor: not-allowed; }
        .quiz-btn-primary {
            background: var(--quiz-accent);
            color: white;
            box-shadow: 0 2px 8px rgba(65, 84, 241, 0.25);
        }
        .quiz-btn-primary:hover { filter: brightness(1.07); }
        .quiz-btn-submit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 2px 12px rgba(16, 185, 129, 0.3);
        }
        .quiz-btn-submit:hover { filter: brightness(1.07); }
        .quiz-btn-ghost {
            background: rgb(243 244 246);
            color: rgb(55 65 81);
        }
        .dark .quiz-btn-ghost {
            background: rgb(31 41 55);
            color: rgb(229 231 235);
        }
        .quiz-btn-ghost:hover { background: rgb(229 231 235); }
        .dark .quiz-btn-ghost:hover { background: rgb(55 65 81); }

        /* ─── Confirm modal ─── */
        .quiz-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(4px);
            z-index: 60;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .quiz-modal {
            background: white;
            border-radius: 1rem;
            max-width: 28rem;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            transform: scale(1);
        }
        .dark .quiz-modal { background: rgb(17 24 39); border: 1px solid rgb(31 41 55); }
        .quiz-modal-body { padding: 1.75rem 1.75rem 1.25rem; }
        .quiz-modal-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 9999px;
            background: rgb(254 243 199);
            color: rgb(180 83 9);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .dark .quiz-modal-icon { background: rgba(180, 83, 9, 0.18); color: rgb(252 211 77); }
        .quiz-modal-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: rgb(17 24 39);
            margin-bottom: 0.375rem;
        }
        .dark .quiz-modal-title { color: rgb(243 244 246); }
        .quiz-modal-desc {
            font-size: 0.875rem;
            color: rgb(75 85 99);
            line-height: 1.5;
        }
        .dark .quiz-modal-desc { color: rgb(156 163 175); }
        .quiz-modal-stats {
            margin-top: 1rem;
            padding: 0.875rem 1rem;
            background: rgb(248 250 252);
            border-radius: 0.625rem;
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            font-size: 0.8125rem;
        }
        .dark .quiz-modal-stats { background: rgba(255,255,255,0.04); }
        .quiz-modal-stat { color: rgb(75 85 99); }
        .dark .quiz-modal-stat { color: rgb(156 163 175); }
        .quiz-modal-stat strong { color: rgb(17 24 39); font-weight: 700; }
        .dark .quiz-modal-stat strong { color: rgb(243 244 246); }
        .quiz-modal-warn {
            color: rgb(180 83 9) !important;
        }
        .dark .quiz-modal-warn { color: rgb(252 211 77) !important; }
        .quiz-modal-footer {
            padding: 1rem 1.75rem 1.5rem;
            display: flex;
            gap: 0.625rem;
            justify-content: flex-end;
        }
        @media (max-width: 480px) {
            .quiz-modal-footer { flex-direction: column-reverse; }
            .quiz-modal-footer .quiz-btn { width: 100%; justify-content: center; }
        }

        /* Modal animation */
        [x-cloak] { display: none !important; }
        .quiz-modal-enter { animation: modalIn 180ms ease-out both; }
        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.94) translateY(8px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
    </style>

    <div class="quiz-shell"
         x-data="{ confirmOpen: false }"
         @keydown.escape.window="confirmOpen = false">
        {{-- Sticky header --}}
        <div class="quiz-header">
            <div class="quiz-header-row">
                <div style="min-width:0; flex:1;">
                    <p class="quiz-breadcrumb">
                        {{ $quiz->lecture?->course?->title }} · {{ $quiz->lecture?->title }}
                    </p>
                    <h2 class="quiz-title">{{ $quiz->title }}</h2>
                    <p class="quiz-counter">
                        Question {{ $currentIndex + 1 }} of {{ $total }} · {{ $answered }} answered
                    </p>
                </div>

                <div class="quiz-timer-block" x-data="{
                    remaining: @js($secondsRemaining),
                    fmt() {
                        const m = Math.floor(this.remaining / 60);
                        const s = String(this.remaining % 60).padStart(2, '0');
                        return `${m}:${s}`;
                    },
                    cls() {
                        if (this.remaining < 60) return 'quiz-timer-value danger';
                        if (this.remaining < 180) return 'quiz-timer-value warning';
                        return 'quiz-timer-value';
                    },
                    init() {
                        this.timer = setInterval(() => {
                            if (this.remaining > 0) this.remaining--;
                            else { clearInterval(this.timer); $wire.submit(); }
                        }, 1000);
                    }
                }">
                    <p class="quiz-timer-label">Time left</p>
                    <p :class="cls()" x-text="fmt()"></p>
                </div>
            </div>

            <div class="quiz-progress">
                <div class="quiz-progress-fill" style="width: {{ $progress }}%"></div>
            </div>
        </div>

        @if ($current)
            {{-- Question card --}}
            <div class="quiz-card">
                <div class="quiz-q-meta">
                    <svg style="width:0.875rem; height:0.875rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4M12 16h.01"/></svg>
                    Question {{ $currentIndex + 1 }} of {{ $total }}
                </div>

                <h3 class="quiz-q-text">{{ $current['text'] }}</h3>

                <div class="quiz-options">
                    @foreach ($current['options'] as $i => $option)
                        @php $isSelected = ($answers[$current['id']] ?? null) === $option['id']; @endphp
                        <button
                            type="button"
                            wire:click="selectOption({{ $current['id'] }}, {{ $option['id'] }})"
                            class="quiz-option {{ $isSelected ? 'is-selected' : '' }}"
                        >
                            <span class="quiz-letter">
                                @if ($isSelected)
                                    <svg style="width:1rem; height:1rem;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    {{ chr(65 + $i) }}
                                @endif
                            </span>
                            <span class="quiz-option-text">{{ $option['text'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Jump-to dots --}}
            <div class="quiz-dots-card">
                <p class="quiz-dots-label">Jump to question</p>
                <div class="quiz-dots">
                    @foreach ($questions as $idx => $q)
                        @php
                            $cls = ['quiz-dot'];
                            if ($idx === $currentIndex) $cls[] = 'current';
                            elseif (isset($answers[$q['id']])) $cls[] = 'answered';
                        @endphp
                        <button
                            type="button"
                            wire:click="jumpTo({{ $idx }})"
                            class="{{ implode(' ', $cls) }}"
                        >{{ $idx + 1 }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Sticky action bar --}}
            <div class="quiz-actions">
                <button
                    type="button"
                    wire:click="previous"
                    @disabled($currentIndex === 0)
                    class="quiz-btn quiz-btn-secondary"
                >← Previous</button>

                @if ($currentIndex < $total - 1)
                    <button
                        type="button"
                        wire:click="next"
                        class="quiz-btn quiz-btn-ghost"
                        style="flex:1; justify-content:center;"
                    >Next →</button>
                @endif

                <button
                    type="button"
                    @click="confirmOpen = true"
                    class="quiz-btn {{ $currentIndex === $total - 1 ? 'quiz-btn-submit' : 'quiz-btn-primary' }}"
                    @if ($currentIndex === $total - 1) style="flex:1; justify-content:center;" @endif
                >
                    {{ $currentIndex === $total - 1 ? 'Submit Quiz ✓' : 'Submit' }}
                </button>
            </div>

            {{-- Custom submit-confirmation modal --}}
            <div x-show="confirmOpen"
                 x-cloak
                 x-transition.opacity.duration.150ms
                 class="quiz-modal-backdrop"
                 @click.self="confirmOpen = false">
                <div class="quiz-modal quiz-modal-enter" @click.stop>
                    <div class="quiz-modal-body">
                        <div class="quiz-modal-icon">
                            <svg style="width:1.5rem; height:1.5rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                        </div>
                        <h3 class="quiz-modal-title">Submit quiz now?</h3>
                        <p class="quiz-modal-desc">
                            Once submitted, your answers are locked and your score is calculated immediately.
                            You can still retake the quiz later for a fresh attempt.
                        </p>

                        <div class="quiz-modal-stats">
                            <span class="quiz-modal-stat">
                                Answered <strong>{{ $answered }} / {{ $total }}</strong>
                            </span>
                            @if ($answered < $total)
                                <span class="quiz-modal-stat quiz-modal-warn">
                                    ⚠ {{ $total - $answered }} unanswered
                                </span>
                            @else
                                <span class="quiz-modal-stat" style="color: rgb(22 101 52);">
                                    ✓ All answered
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="quiz-modal-footer">
                        <button type="button"
                                @click="confirmOpen = false"
                                class="quiz-btn quiz-btn-secondary">
                            Keep reviewing
                        </button>
                        <button type="button"
                                @click="confirmOpen = false; $wire.submit()"
                                class="quiz-btn quiz-btn-submit">
                            Yes, submit my quiz
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
