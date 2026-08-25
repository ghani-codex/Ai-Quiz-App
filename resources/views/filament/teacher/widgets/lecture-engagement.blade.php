@php
    $rows = $this->getEngagementRows();
    $options = $this->getCourseOptions();
    $agg = $this->getAggregates($rows);

    // Ring SVG math (smaller rings now — side-by-side)
    $radius = 36;
    $circumference = 2 * M_PI * $radius;

    $ringEngagement = $circumference * (1 - $agg['engagement_rate'] / 100);
    $ringCoverage   = $circumference * (1 - $agg['engaged_lectures_pct'] / 100);
    $ringDownloads  = $circumference * (1 - $agg['download_rate'] / 100);

    $topLectures = $rows; // show all (up to 15), scroll vertically
@endphp

<div>
    <style>
        .le-card {
            background: white;
            border: 1px solid rgb(229 231 235);
            border-radius: 1rem;
            overflow: hidden;
        }
        .dark .le-card { background: rgb(17 24 39); border-color: rgb(31 41 55); }

        /* Header */
        .le-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgb(229 231 235);
            flex-wrap: wrap;
        }
        .dark .le-head { border-color: rgb(31 41 55); }
        .le-head-text { min-width: 0; flex: 1; }
        .le-title {
            font-size: 1.0625rem;
            font-weight: 700;
            color: rgb(17 24 39);
            margin: 0;
            letter-spacing: -0.01em;
        }
        .dark .le-title { color: rgb(243 244 246); }
        .le-sub {
            font-size: 0.8125rem;
            color: rgb(107 114 128);
            margin: 0.25rem 0 0;
        }
        /* Custom select dropdown — same look in both light and dark mode */
        .le-filter {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            min-width: 13rem;
            padding: 0.5rem 2.25rem 0.5rem 0.875rem;
            border: 1.5px solid rgb(229 231 235);
            border-radius: 0.5rem;
            background-color: white;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.75' d='m6 8 4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.625rem center;
            background-size: 1.125rem;
            color: rgb(31 41 55);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: border-color 120ms, box-shadow 120ms;
        }
        .dark .le-filter {
            background-color: rgb(30 41 59);
            border-color: rgb(51 65 85);
            color: rgb(226 232 240);
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.75' d='m6 8 4 4 4-4'/%3E%3C/svg%3E");
        }
        .le-filter:hover { border-color: rgba(65,84,241,0.4); }
        .le-filter:focus {
            outline: none;
            border-color: #4154f1;
            box-shadow: 0 0 0 3px rgba(65,84,241,0.18);
        }
        /* Style the dropdown OPTION list (limited browser support but Chromium honors it) */
        .le-filter option {
            background-color: white;
            color: rgb(31 41 55);
            padding: 0.5rem;
        }
        .dark .le-filter option {
            background-color: rgb(30 41 59);
            color: rgb(226 232 240);
        }
        /* Hide IE/Edge legacy expand arrow */
        .le-filter::-ms-expand { display: none; }

        /* ─── Two-column body ─── */
        .le-body {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
            padding: 1.25rem;
        }
        @media (min-width: 1024px) { .le-body { grid-template-columns: 5fr 6fr; gap: 1.5rem; } }

        /* ─── Left: stat cards with horizontal rings ─── */
        .le-stats {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .le-stat-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.125rem;
            border-radius: 0.875rem;
            position: relative;
        }
        .le-stat-card.indigo {
            background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(65,84,241,0.04));
            border: 1px solid rgba(99,102,241,0.22);
        }
        .dark .le-stat-card.indigo {
            background: linear-gradient(135deg, rgba(99,102,241,0.18), rgba(65,84,241,0.06));
            border-color: rgba(99,102,241,0.35);
        }
        .le-stat-card.emerald {
            background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(5,150,105,0.04));
            border: 1px solid rgba(16,185,129,0.22);
        }
        .dark .le-stat-card.emerald {
            background: linear-gradient(135deg, rgba(16,185,129,0.18), rgba(5,150,105,0.06));
            border-color: rgba(16,185,129,0.35);
        }
        .le-stat-card.amber {
            background: linear-gradient(135deg, rgba(245,158,11,0.1), rgba(217,119,6,0.04));
            border: 1px solid rgba(245,158,11,0.22);
        }
        .dark .le-stat-card.amber {
            background: linear-gradient(135deg, rgba(245,158,11,0.18), rgba(217,119,6,0.06));
            border-color: rgba(245,158,11,0.35);
        }

        /* Horizontal ring */
        .le-ring-wrap {
            position: relative;
            width: 90px;
            height: 90px;
            flex-shrink: 0;
        }
        .le-ring-svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }
        .le-ring-bg {
            stroke: rgba(0,0,0,0.08);
            fill: none;
        }
        .dark .le-ring-bg { stroke: rgba(255,255,255,0.08); }

        .le-ring-fg {
            fill: none;
            stroke-linecap: round;
        }
        .le-ring-fg.indigo { stroke: url(#leGradIndigo); }
        .le-ring-fg.emerald { stroke: url(#leGradEmerald); }
        .le-ring-fg.amber { stroke: url(#leGradAmber); }

        @keyframes leRingDrawIndigo { from { stroke-dashoffset: {{ $circumference }}; } to { stroke-dashoffset: {{ $ringEngagement }}; } }
        @keyframes leRingDrawEmerald { from { stroke-dashoffset: {{ $circumference }}; } to { stroke-dashoffset: {{ $ringCoverage }}; } }
        @keyframes leRingDrawAmber { from { stroke-dashoffset: {{ $circumference }}; } to { stroke-dashoffset: {{ $ringDownloads }}; } }
        .le-ring-fg.indigo { animation: leRingDrawIndigo 1.2s cubic-bezier(0.4, 0, 0.2, 1) both; }
        .le-ring-fg.emerald { animation: leRingDrawEmerald 1.2s cubic-bezier(0.4, 0, 0.2, 1) both 0.1s; }
        .le-ring-fg.amber { animation: leRingDrawAmber 1.2s cubic-bezier(0.4, 0, 0.2, 1) both 0.2s; }

        .le-ring-center {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .le-ring-num {
            font-size: 1.375rem;
            font-weight: 800;
            line-height: 1;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.02em;
        }
        .le-ring-num.indigo { color: #4154f1; }
        .le-ring-num.emerald { color: #059669; }
        .le-ring-num.amber { color: #d97706; }
        .dark .le-ring-num.indigo { color: #818cf8; }
        .dark .le-ring-num.emerald { color: #34d399; }
        .dark .le-ring-num.amber { color: #fbbf24; }
        .le-ring-unit {
            font-size: 0.625rem;
            font-weight: 600;
            opacity: 0.7;
        }

        .le-stat-text { min-width: 0; flex: 1; }
        .le-stat-label {
            font-size: 0.9375rem;
            font-weight: 700;
            color: rgb(17 24 39);
            margin: 0;
            letter-spacing: -0.005em;
        }
        .dark .le-stat-label { color: rgb(243 244 246); }
        .le-stat-desc {
            font-size: 0.75rem;
            color: rgb(107 114 128);
            margin: 0.25rem 0 0;
            line-height: 1.4;
        }

        /* ─── Right: top lectures list ─── */
        .le-list-card {
            background: rgb(249 250 251);
            border: 1px solid rgb(229 231 235);
            border-radius: 0.875rem;
            padding: 1rem;
        }
        .dark .le-list-card { background: rgba(255,255,255,0.02); border-color: rgb(31 41 55); }

        .le-list-head {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }
        .le-list-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: rgb(17 24 39);
            margin: 0;
        }
        .dark .le-list-title { color: rgb(243 244 246); }
        .le-list-sub {
            font-size: 0.75rem;
            color: rgb(107 114 128);
        }

        /* Scrollable list (vertical) */
        .le-list-wrap {
            max-height: 360px;
            overflow-y: auto;
            padding-right: 0.5rem;
            margin-right: -0.5rem;
            scrollbar-width: thin;
            scrollbar-color: rgba(99,102,241,0.4) transparent;
        }
        .le-list-wrap::-webkit-scrollbar { width: 6px; }
        .le-list-wrap::-webkit-scrollbar-track { background: transparent; }
        .le-list-wrap::-webkit-scrollbar-thumb {
            background: rgba(99,102,241,0.35);
            border-radius: 9999px;
        }
        .le-list-wrap::-webkit-scrollbar-thumb:hover { background: rgba(99,102,241,0.6); }

        .le-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .le-row {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.75rem;
            border-radius: 0.5rem;
            background: white;
            border: 1px solid rgb(243 244 246);
            transition: all 120ms;
        }
        .dark .le-row { background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.05); }
        .le-row:hover { border-color: rgba(65,84,241,0.3); transform: translateX(2px); }

        .le-rank {
            width: 1.625rem;
            height: 1.625rem;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.6875rem;
            background: rgb(229 231 235);
            color: rgb(75 85 99);
        }
        .dark .le-rank { background: rgb(51 65 85); color: rgb(203 213 225); }
        .le-rank.gold { background: linear-gradient(135deg,#fbbf24,#d97706); color: white; }
        .le-rank.silver { background: linear-gradient(135deg,#cbd5e1,#94a3b8); color: white; }
        .le-rank.bronze { background: linear-gradient(135deg,#d97706,#92400e); color: white; }

        .le-row-body { min-width: 0; }
        .le-row-title {
            font-size: 0.8125rem;
            font-weight: 600;
            color: rgb(17 24 39);
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .dark .le-row-title { color: rgb(243 244 246); }
        .le-row-bar {
            height: 0.3125rem;
            background: rgb(229 231 235);
            border-radius: 9999px;
            overflow: hidden;
            margin-top: 0.375rem;
        }
        .dark .le-row-bar { background: rgba(255,255,255,0.06); }
        .le-row-bar-fill {
            height: 100%;
            background: linear-gradient(to right, #6366f1, #4154f1);
            border-radius: 9999px;
        }
        .le-row-stat {
            font-size: 0.6875rem;
            font-variant-numeric: tabular-nums;
            color: rgb(107 114 128);
            white-space: nowrap;
            text-align: right;
        }
        .le-row-stat strong { color: rgb(17 24 39); font-weight: 700; font-size: 0.8125rem; }
        .dark .le-row-stat strong { color: rgb(243 244 246); }

        .le-empty {
            padding: 2.5rem 1rem;
            text-align: center;
            color: rgb(107 114 128);
            font-size: 0.8125rem;
        }
    </style>

    <div class="le-card">
        <div class="le-head">
            <div class="le-head-text">
                <h2 class="le-title">Lecture engagement</h2>
                <p class="le-sub">How your students are interacting with your lecture content.</p>
            </div>
            <select wire:model.live="courseFilter" class="le-filter">
                @foreach ($options as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        @if (count($rows) === 0)
            <div class="le-empty">No lectures for the selected filter.</div>
        @else
            <div class="le-body">
                {{-- ─── LEFT: 3 ring stat cards stacked ─── --}}
                <div class="le-stats">
                    <svg style="position:absolute; width:0; height:0;">
                        <defs>
                            <linearGradient id="leGradIndigo" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#818cf8"/>
                                <stop offset="100%" stop-color="#4154f1"/>
                            </linearGradient>
                            <linearGradient id="leGradEmerald" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#34d399"/>
                                <stop offset="100%" stop-color="#059669"/>
                            </linearGradient>
                            <linearGradient id="leGradAmber" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#fbbf24"/>
                                <stop offset="100%" stop-color="#d97706"/>
                            </linearGradient>
                        </defs>
                    </svg>

                    {{-- Ring 1: Engagement rate --}}
                    <div class="le-stat-card indigo">
                        <div class="le-ring-wrap">
                            <svg class="le-ring-svg" viewBox="0 0 90 90">
                                <circle class="le-ring-bg" cx="45" cy="45" r="{{ $radius }}" stroke-width="8"/>
                                <circle class="le-ring-fg indigo" cx="45" cy="45" r="{{ $radius }}" stroke-width="8"
                                        stroke-dasharray="{{ $circumference }}"
                                        stroke-dashoffset="{{ $ringEngagement }}"/>
                            </svg>
                            <div class="le-ring-center">
                                <span class="le-ring-num indigo">{{ $agg['engagement_rate'] }}<span class="le-ring-unit">%</span></span>
                            </div>
                        </div>
                        <div class="le-stat-text">
                            <p class="le-stat-label">Engagement rate</p>
                            <p class="le-stat-desc">{{ $agg['actual_opens'] }} opens out of {{ $agg['max_possible_opens'] ?: '—' }} possible (lecture × student pairs).</p>
                        </div>
                    </div>

                    {{-- Ring 2: Lecture coverage --}}
                    <div class="le-stat-card emerald">
                        <div class="le-ring-wrap">
                            <svg class="le-ring-svg" viewBox="0 0 90 90">
                                <circle class="le-ring-bg" cx="45" cy="45" r="{{ $radius }}" stroke-width="8"/>
                                <circle class="le-ring-fg emerald" cx="45" cy="45" r="{{ $radius }}" stroke-width="8"
                                        stroke-dasharray="{{ $circumference }}"
                                        stroke-dashoffset="{{ $ringCoverage }}"/>
                            </svg>
                            <div class="le-ring-center">
                                <span class="le-ring-num emerald">{{ $agg['engaged_lectures'] }}<span class="le-ring-unit">/{{ $agg['total_lectures'] }}</span></span>
                            </div>
                        </div>
                        <div class="le-stat-text">
                            <p class="le-stat-label">Lecture coverage</p>
                            <p class="le-stat-desc">{{ $agg['engaged_lectures_pct'] }}% of lectures have at least one student read.</p>
                        </div>
                    </div>

                    {{-- Ring 3: Downloads --}}
                    <div class="le-stat-card amber">
                        <div class="le-ring-wrap">
                            <svg class="le-ring-svg" viewBox="0 0 90 90">
                                <circle class="le-ring-bg" cx="45" cy="45" r="{{ $radius }}" stroke-width="8"/>
                                <circle class="le-ring-fg amber" cx="45" cy="45" r="{{ $radius }}" stroke-width="8"
                                        stroke-dasharray="{{ $circumference }}"
                                        stroke-dashoffset="{{ $ringDownloads }}"/>
                            </svg>
                            <div class="le-ring-center">
                                <span class="le-ring-num amber">{{ $agg['total_downloads'] }}</span>
                            </div>
                        </div>
                        <div class="le-stat-text">
                            <p class="le-stat-label">Downloads</p>
                            <p class="le-stat-desc">{{ $agg['download_rate'] }}% of opens converted to a file download.</p>
                        </div>
                    </div>
                </div>

                {{-- ─── RIGHT: top lectures list ─── --}}
                <div class="le-list-card">
                    <div class="le-list-head">
                        <h3 class="le-list-title">Top lectures by engagement</h3>
                        <span class="le-list-sub">{{ count($topLectures) }} lectures · scroll to see more</span>
                    </div>

                    <div class="le-list-wrap">
                    <div class="le-list">
                        @foreach ($topLectures as $i => $row)
                            @php
                                $rank = $i + 1;
                                $rankClass = match (true) {
                                    $rank === 1 && $row['has_any'] => 'gold',
                                    $rank === 2 && $row['has_any'] => 'silver',
                                    $rank === 3 && $row['has_any'] => 'bronze',
                                    default => '',
                                };
                            @endphp
                            <div class="le-row">
                                <div class="le-rank {{ $rankClass }}">#{{ $rank }}</div>
                                <div class="le-row-body">
                                    <div class="le-row-title">{{ $row['title'] }}</div>
                                    <div class="le-row-bar">
                                        <div class="le-row-bar-fill" style="width: {{ $row['opens_bar_pct'] }}%"></div>
                                    </div>
                                </div>
                                <div class="le-row-stat">
                                    <strong>{{ $row['students_opened'] }}</strong>/{{ $row['enrolled'] }}<br>
                                    <span style="opacity:0.6;">{{ $row['open_pct'] }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
