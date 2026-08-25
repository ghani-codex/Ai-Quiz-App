@php
    $attempts = $this->getAttempts();
    $stats = $this->getStats($attempts);

    // Stat ring math
    $statRadius = 32;
    $statCircumference = 2 * M_PI * $statRadius;
    $ringAvg   = $statCircumference * (1 - ($stats['avg_score'] / 100));
    $ringPass  = $statCircumference * (1 - ($stats['pass_rate'] / 100));
    $ringTop   = $statCircumference * (1 - ($stats['top_score'] / 100));

    // Mini score-ring math
    $miniRadius = 24;
    $miniCircumference = 2 * M_PI * $miniRadius;
@endphp

<div>
    <style>
        .ra-card {
            background: white;
            border: 1px solid rgb(229 231 235);
            border-radius: 1rem;
            overflow: hidden;
        }
        .dark .ra-card { background: rgb(17 24 39); border-color: rgb(31 41 55); }

        .ra-head {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgb(229 231 235);
        }
        .dark .ra-head { border-color: rgb(31 41 55); }
        .ra-title {
            font-size: 1.0625rem;
            font-weight: 700;
            color: rgb(17 24 39);
            margin: 0;
            letter-spacing: -0.01em;
        }
        .dark .ra-title { color: rgb(243 244 246); }
        .ra-sub {
            font-size: 0.8125rem;
            color: rgb(107 114 128);
            margin: 0.25rem 0 0;
        }

        /* ─── Top stats row (3 ring stat cards) ─── */
        .ra-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgb(229 231 235);
        }
        .dark .ra-stats { border-color: rgb(31 41 55); }
        @media (max-width: 640px) { .ra-stats { grid-template-columns: 1fr; } }

        .ra-stat {
            display: flex;
            align-items: center;
            gap: 1.125rem;
            padding: 1.25rem 1.5rem;
            border-radius: 0.875rem;
            min-height: 110px;
        }
        .ra-stat.indigo { background: rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.22); }
        .dark .ra-stat.indigo { background: rgba(99,102,241,0.15); border-color: rgba(99,102,241,0.35); }
        .ra-stat.emerald { background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.22); }
        .dark .ra-stat.emerald { background: rgba(16,185,129,0.15); border-color: rgba(16,185,129,0.35); }
        .ra-stat.amber { background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.22); }
        .dark .ra-stat.amber { background: rgba(245,158,11,0.15); border-color: rgba(245,158,11,0.35); }

        .ra-stat-ring {
            position: relative;
            width: 72px;
            height: 72px;
            flex-shrink: 0;
        }
        .ra-stat-svg { width: 100%; height: 100%; transform: rotate(-90deg); }
        .ra-ring-bg { stroke: rgba(0,0,0,0.08); fill: none; }
        .dark .ra-ring-bg { stroke: rgba(255,255,255,0.08); }
        .ra-ring-fg { fill: none; stroke-linecap: round; }
        .ra-ring-fg.indigo { stroke: url(#raGradIndigo); }
        .ra-ring-fg.emerald { stroke: url(#raGradEmerald); }
        .ra-ring-fg.amber { stroke: url(#raGradAmber); }

        @keyframes raRingAvg { from { stroke-dashoffset: {{ $statCircumference }}; } to { stroke-dashoffset: {{ $ringAvg }}; } }
        @keyframes raRingPass { from { stroke-dashoffset: {{ $statCircumference }}; } to { stroke-dashoffset: {{ $ringPass }}; } }
        @keyframes raRingTop { from { stroke-dashoffset: {{ $statCircumference }}; } to { stroke-dashoffset: {{ $ringTop }}; } }

        .ra-ring-fg.r-avg { animation: raRingAvg 1.1s cubic-bezier(0.4,0,0.2,1) both; }
        .ra-ring-fg.r-pass { animation: raRingPass 1.1s cubic-bezier(0.4,0,0.2,1) both 0.1s; }
        .ra-ring-fg.r-top { animation: raRingTop 1.1s cubic-bezier(0.4,0,0.2,1) both 0.2s; }

        .ra-ring-center {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .ra-ring-num {
            font-size: 1rem;
            font-weight: 800;
            line-height: 1;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.02em;
        }
        .ra-ring-num.indigo { color: #4154f1; }
        .ra-ring-num.emerald { color: #059669; }
        .ra-ring-num.amber { color: #d97706; }
        .dark .ra-ring-num.indigo { color: #818cf8; }
        .dark .ra-ring-num.emerald { color: #34d399; }
        .dark .ra-ring-num.amber { color: #fbbf24; }
        .ra-ring-unit { font-size: 0.5625rem; opacity: 0.7; }

        .ra-stat-text { min-width: 0; flex: 1; }
        .ra-stat-label {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            color: rgb(75 85 99);
            margin: 0 0 0.375rem;
        }
        .dark .ra-stat-label { color: rgb(156 163 175); }
        .ra-stat-value {
            font-size: 1.625rem;
            font-weight: 800;
            color: rgb(17 24 39);
            margin: 0;
            line-height: 1;
            letter-spacing: -0.02em;
            font-variant-numeric: tabular-nums;
        }
        .dark .ra-stat-value { color: rgb(243 244 246); }
        .ra-stat-value .unit { font-size: 0.875rem; opacity: 0.55; font-weight: 600; margin-left: 0.125rem; }
        .ra-stat-desc {
            font-size: 0.75rem;
            color: rgb(107 114 128);
            margin: 0.5rem 0 0;
            line-height: 1.4;
        }
        .dark .ra-stat-desc { color: rgb(148 163 184); }

        /* ─── Attempt cards grid ─── */
        .ra-list-head {
            padding: 1rem 1.5rem 0.5rem;
            display: flex;
            align-items: baseline;
            justify-content: space-between;
        }
        .ra-list-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: rgb(17 24 39);
            margin: 0;
        }
        .dark .ra-list-title { color: rgb(243 244 246); }
        .ra-list-sub { font-size: 0.75rem; color: rgb(107 114 128); }

        /* Horizontal carousel — pure CSS scroll-snap */
        .ra-grid-wrap { position: relative; }

        .ra-grid {
            display: flex;
            gap: 0.75rem;
            padding: 0.5rem 1.5rem 1.5rem;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scroll-padding-left: 1.5rem;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: rgba(99,102,241,0.35) transparent;
        }
        .ra-grid::-webkit-scrollbar { height: 6px; }
        .ra-grid::-webkit-scrollbar-track { background: transparent; }
        .ra-grid::-webkit-scrollbar-thumb {
            background: rgba(99,102,241,0.35);
            border-radius: 9999px;
        }
        .ra-grid::-webkit-scrollbar-thumb:hover { background: rgba(99,102,241,0.6); }

        /* Each card: fixed width, snap-aligned */
        .ra-attempt {
            flex: 0 0 175px;
            scroll-snap-align: start;
        }

        /* Carousel navigation buttons */
        .ra-nav {
            display: inline-flex;
            gap: 0.25rem;
        }
        .ra-nav-btn {
            width: 1.875rem;
            height: 1.875rem;
            border-radius: 9999px;
            background: white;
            border: 1px solid rgb(229 231 235);
            color: rgb(75 85 99);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 120ms;
        }
        .dark .ra-nav-btn { background: rgb(30 41 59); border-color: rgb(51 65 85); color: rgb(203 213 225); }
        .ra-nav-btn:hover {
            border-color: #4154f1;
            color: #4154f1;
            transform: scale(1.05);
        }
        .ra-nav-btn:disabled {
            opacity: 0.35;
            cursor: not-allowed;
            transform: none;
        }
        .ra-nav-btn:disabled:hover { border-color: rgb(229 231 235); color: rgb(75 85 99); }
        .dark .ra-nav-btn:disabled:hover { border-color: rgb(51 65 85); color: rgb(203 213 225); }

        .ra-attempt {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0.875rem 0.625rem;
            border-radius: 0.75rem;
            background: rgb(249 250 251);
            border: 1px solid rgb(243 244 246);
            transition: all 150ms;
        }
        .dark .ra-attempt { background: rgba(255,255,255,0.025); border-color: rgba(255,255,255,0.05); }
        .ra-attempt:hover {
            transform: translateY(-2px);
            border-color: rgba(65,84,241,0.35);
            box-shadow: 0 8px 20px rgba(65,84,241,0.08);
        }

        .ra-mini-ring {
            position: relative;
            width: 60px;
            height: 60px;
            margin-bottom: 0.5rem;
        }
        .ra-mini-svg { width: 100%; height: 100%; transform: rotate(-90deg); }
        .ra-mini-bg { stroke: rgba(0,0,0,0.08); fill: none; }
        .dark .ra-mini-bg { stroke: rgba(255,255,255,0.08); }
        .ra-mini-fg {
            fill: none;
            stroke-linecap: round;
            transition: stroke-dashoffset 800ms cubic-bezier(0.4,0,0.2,1);
        }
        .ra-mini-fg.green { stroke: #10b981; }
        .ra-mini-fg.amber { stroke: #f59e0b; }
        .ra-mini-fg.red   { stroke: #ef4444; }

        .ra-mini-center {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .ra-mini-num {
            font-size: 0.9375rem;
            font-weight: 800;
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }
        .ra-mini-num.green { color: #059669; }
        .ra-mini-num.amber { color: #d97706; }
        .ra-mini-num.red   { color: #dc2626; }
        .dark .ra-mini-num.green { color: #34d399; }
        .dark .ra-mini-num.amber { color: #fbbf24; }
        .dark .ra-mini-num.red   { color: #f87171; }

        .ra-attempt-name {
            font-size: 0.75rem;
            font-weight: 700;
            color: rgb(17 24 39);
            line-height: 1.2;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
            margin-bottom: 0.25rem;
        }
        .dark .ra-attempt-name { color: rgb(243 244 246); }
        .ra-attempt-quiz {
            font-size: 0.6875rem;
            color: rgb(107 114 128);
            line-height: 1.2;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }
        .ra-attempt-time {
            font-size: 0.625rem;
            color: rgb(156 163 175);
            margin-top: 0.375rem;
        }

        /* Empty state */
        .ra-empty {
            padding: 3rem 1.5rem;
            text-align: center;
            color: rgb(107 114 128);
        }
        .ra-empty-icon {
            width: 3rem;
            height: 3rem;
            background: rgb(243 244 246);
            border-radius: 9999px;
            margin: 0 auto 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgb(156 163 175);
        }
        .dark .ra-empty-icon { background: rgba(255,255,255,0.06); }
    </style>

    <svg style="position:absolute; width:0; height:0;">
        <defs>
            <linearGradient id="raGradIndigo" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#818cf8"/>
                <stop offset="100%" stop-color="#4154f1"/>
            </linearGradient>
            <linearGradient id="raGradEmerald" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#34d399"/>
                <stop offset="100%" stop-color="#059669"/>
            </linearGradient>
            <linearGradient id="raGradAmber" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#fbbf24"/>
                <stop offset="100%" stop-color="#d97706"/>
            </linearGradient>
        </defs>
    </svg>

    <div class="ra-card">
        <div class="ra-head">
            <h2 class="ra-title">Recent attempts on my quizzes</h2>
            <p class="ra-sub">Latest student submissions across your courses.</p>
        </div>

        @if ($stats['count'] === 0)
            <div class="ra-empty">
                <div class="ra-empty-icon">
                    <svg style="width:1.5rem; height:1.5rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <p>No attempts yet. Once students take your quizzes, their results will appear here.</p>
            </div>
        @else
            {{-- 3 stat rings --}}
            <div class="ra-stats">
                <div class="ra-stat indigo">
                    <div class="ra-stat-ring">
                        <svg class="ra-stat-svg" viewBox="0 0 72 72">
                            <circle class="ra-ring-bg" cx="36" cy="36" r="{{ $statRadius }}" stroke-width="6"/>
                            <circle class="ra-ring-fg indigo r-avg" cx="36" cy="36" r="{{ $statRadius }}" stroke-width="6"
                                    stroke-dasharray="{{ $statCircumference }}"
                                    stroke-dashoffset="{{ $ringAvg }}"/>
                        </svg>
                        <div class="ra-ring-center">
                            <span class="ra-ring-num indigo">{{ number_format($stats['avg_score'], 0) }}<span class="ra-ring-unit">%</span></span>
                        </div>
                    </div>
                    <div class="ra-stat-text">
                        <p class="ra-stat-label">Average score</p>
                        <p class="ra-stat-value">{{ number_format($stats['avg_score'], 1) }}<span class="unit">/100</span></p>
                        <p class="ra-stat-desc">Across {{ $stats['count'] }} recent attempts</p>
                    </div>
                </div>

                <div class="ra-stat emerald">
                    <div class="ra-stat-ring">
                        <svg class="ra-stat-svg" viewBox="0 0 72 72">
                            <circle class="ra-ring-bg" cx="36" cy="36" r="{{ $statRadius }}" stroke-width="6"/>
                            <circle class="ra-ring-fg emerald r-pass" cx="36" cy="36" r="{{ $statRadius }}" stroke-width="6"
                                    stroke-dasharray="{{ $statCircumference }}"
                                    stroke-dashoffset="{{ $ringPass }}"/>
                        </svg>
                        <div class="ra-ring-center">
                            <span class="ra-ring-num emerald">{{ $stats['pass_rate'] }}<span class="ra-ring-unit">%</span></span>
                        </div>
                    </div>
                    <div class="ra-stat-text">
                        <p class="ra-stat-label">Pass rate</p>
                        <p class="ra-stat-value">{{ $stats['pass_rate'] }}<span class="unit">%</span></p>
                        <p class="ra-stat-desc">Scored at least 50/100</p>
                    </div>
                </div>

                <div class="ra-stat amber">
                    <div class="ra-stat-ring">
                        <svg class="ra-stat-svg" viewBox="0 0 72 72">
                            <circle class="ra-ring-bg" cx="36" cy="36" r="{{ $statRadius }}" stroke-width="6"/>
                            <circle class="ra-ring-fg amber r-top" cx="36" cy="36" r="{{ $statRadius }}" stroke-width="6"
                                    stroke-dasharray="{{ $statCircumference }}"
                                    stroke-dashoffset="{{ $ringTop }}"/>
                        </svg>
                        <div class="ra-ring-center">
                            <span class="ra-ring-num amber">{{ number_format($stats['top_score'], 0) }}<span class="ra-ring-unit">%</span></span>
                        </div>
                    </div>
                    <div class="ra-stat-text">
                        <p class="ra-stat-label">Top score</p>
                        <p class="ra-stat-value">{{ number_format($stats['top_score'], 1) }}<span class="unit">/100</span></p>
                        <p class="ra-stat-desc">Best performer this period</p>
                    </div>
                </div>
            </div>

            {{-- Attempt cards (horizontal carousel) --}}
            <div x-data="{
                scroller: null,
                canPrev: false,
                canNext: true,
                init() {
                    this.scroller = this.$refs.scroller;
                    this.updateState();
                    this.scroller.addEventListener('scroll', () => this.updateState(), { passive: true });
                    window.addEventListener('resize', () => this.updateState());
                },
                updateState() {
                    if (! this.scroller) return;
                    this.canPrev = this.scroller.scrollLeft > 4;
                    this.canNext = this.scroller.scrollLeft < (this.scroller.scrollWidth - this.scroller.clientWidth - 4);
                },
                slide(dir) {
                    this.scroller.scrollBy({ left: dir * (this.scroller.clientWidth * 0.7), behavior: 'smooth' });
                },
            }">
                <div class="ra-list-head">
                    <div>
                        <h3 class="ra-list-title">Last {{ count($attempts) }} submissions</h3>
                        <span class="ra-list-sub">Swipe sideways or use the arrows to browse</span>
                    </div>
                    <div class="ra-nav">
                        <button type="button" class="ra-nav-btn" @click="slide(-1)" :disabled="!canPrev" aria-label="Previous">
                            <svg style="width:1rem; height:1rem;" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                        </button>
                        <button type="button" class="ra-nav-btn" @click="slide(1)" :disabled="!canNext" aria-label="Next">
                            <svg style="width:1rem; height:1rem;" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                        </button>
                    </div>
                </div>

                <div class="ra-grid-wrap">
                <div x-ref="scroller" class="ra-grid">
                @foreach ($attempts as $a)
                    @php
                        $scorePct = max(0, min(100, $a['score']));
                        $offset = $miniCircumference * (1 - $scorePct / 100);
                        $colorClass = $scorePct >= 70 ? 'green' : ($scorePct >= 50 ? 'amber' : 'red');
                    @endphp
                    <div class="ra-attempt" title="{{ $a['student'] }} · {{ $a['quiz'] }} · {{ $a['course'] }}">
                        <div class="ra-mini-ring">
                            <svg class="ra-mini-svg" viewBox="0 0 60 60">
                                <circle class="ra-mini-bg" cx="30" cy="30" r="{{ $miniRadius }}" stroke-width="5"/>
                                <circle class="ra-mini-fg {{ $colorClass }}" cx="30" cy="30" r="{{ $miniRadius }}" stroke-width="5"
                                        stroke-dasharray="{{ $miniCircumference }}"
                                        stroke-dashoffset="{{ $offset }}"/>
                            </svg>
                            <div class="ra-mini-center">
                                <span class="ra-mini-num {{ $colorClass }}">{{ number_format($a['score'], 0) }}</span>
                            </div>
                        </div>
                        <div class="ra-attempt-name">{{ $a['student'] }}</div>
                        <div class="ra-attempt-quiz">{{ $a['quiz'] }}</div>
                        <div class="ra-attempt-time">{{ $a['submitted_at']?->diffForHumans() }}</div>
                    </div>
                @endforeach
                </div>
                </div>
            </div>
        @endif
    </div>
</div>
