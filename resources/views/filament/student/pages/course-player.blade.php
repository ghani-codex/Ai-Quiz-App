<x-filament-panels::page>
    @php
        $lec = $currentLecture;
        $hasFile = $lec && ! empty($lec->file_path);
        $hasText = $lec && ! empty(trim((string) $lec->raw_text));
        $hasOriginalSupported = $hasFile && in_array($lec->content_type, ['pdf'], true);
        $publishedQuiz = $lec?->quizzes->where('is_published', true)->first();
        $current = $this->currentIndex;
        $totalLectures = count($lectures);
    @endphp

    <style>
        /* ─── Player shell ─── */
        .cp-shell { display: grid; gap: 1.25rem; grid-template-columns: 1fr; }
        @media (min-width: 1024px) { .cp-shell { grid-template-columns: 280px 1fr; } }

        /* ─── Sidebar ─── */
        .cp-side {
            background: white;
            border: 1px solid rgb(229 231 235);
            border-radius: 0.875rem;
            padding: 0.875rem;
            position: sticky;
            top: 1rem;
            max-height: calc(100vh - 8rem);
            overflow-y: auto;
        }
        .dark .cp-side { background: rgb(17 24 39); border-color: rgb(31 41 55); }
        .cp-side-title {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            color: rgb(107 114 128);
            padding: 0.25rem 0.5rem 0.5rem;
            border-bottom: 1px solid rgb(229 231 235);
            margin-bottom: 0.5rem;
        }
        .dark .cp-side-title { border-color: rgb(31 41 55); }

        /* Progress block */
        .cp-progress-block {
            padding: 0.75rem 0.5rem 0.875rem;
            border-bottom: 1px solid rgb(229 231 235);
            margin-bottom: 0.5rem;
        }
        .dark .cp-progress-block { border-color: rgb(31 41 55); }
        .cp-progress-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.75rem;
            color: rgb(75 85 99);
            margin-bottom: 0.5rem;
        }
        .dark .cp-progress-head { color: rgb(156 163 175); }
        .cp-progress-head strong { color: rgb(17 24 39); font-weight: 700; font-variant-numeric: tabular-nums; }
        .dark .cp-progress-head strong { color: rgb(243 244 246); }
        .cp-progress-bar {
            height: 0.5rem;
            background: rgb(229 231 235);
            border-radius: 9999px;
            overflow: hidden;
        }
        .dark .cp-progress-bar { background: rgb(31 41 55); }
        .cp-progress-fill {
            height: 100%;
            background: linear-gradient(to right, #6366f1, #4f46e5);
            border-radius: 9999px;
            transition: width 360ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        .cp-progress-fill.complete {
            background: linear-gradient(to right, #10b981, #059669);
        }

        .cp-lec-btn {
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            width: 100%;
            padding: 0.625rem 0.75rem;
            margin-bottom: 0.25rem;
            border-radius: 0.5rem;
            text-align: left;
            background: transparent;
            border: 1px solid transparent;
            color: rgb(55 65 81);
            transition: background 120ms;
            cursor: pointer;
            font-size: 0.875rem;
        }
        .dark .cp-lec-btn { color: rgb(209 213 219); }
        .cp-lec-btn:hover { background: rgb(243 244 246); }
        .dark .cp-lec-btn:hover { background: rgba(255,255,255,0.05); }
        .cp-lec-btn.is-active {
            background: rgb(238 242 255);
            border-color: rgb(199 210 254);
            color: rgb(67 56 202);
            font-weight: 600;
        }
        .dark .cp-lec-btn.is-active {
            background: rgba(99,102,241,0.18);
            border-color: rgba(99,102,241,0.45);
            color: rgb(199 210 254);
        }

        .cp-lec-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
            background: rgb(229 231 235);
            color: rgb(75 85 99);
        }
        .dark .cp-lec-num { background: rgb(55 65 81); color: rgb(209 213 219); }
        .cp-lec-btn.is-active .cp-lec-num { background: rgb(99 102 241); color: white; }
        .cp-lec-btn.is-viewed .cp-lec-num {
            background: rgb(16 185 129);
            color: white;
        }
        .cp-lec-btn.is-viewed.is-active .cp-lec-num {
            background: rgb(99 102 241);
            box-shadow: 0 0 0 2px rgb(16 185 129);
        }

        .cp-lec-title { flex: 1; line-height: 1.35; min-width: 0; word-wrap: break-word; }

        .cp-lec-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.125rem 0.375rem;
            border-radius: 9999px;
            margin-top: 0.25rem;
        }
        .cp-lec-badge.pdf { background: rgb(254 226 226); color: rgb(153 27 27); }
        .cp-lec-badge.docx { background: rgb(219 234 254); color: rgb(30 64 175); }
        .cp-lec-badge.text { background: rgb(220 252 231); color: rgb(22 101 52); }
        .cp-lec-badge.transcript { background: rgb(254 243 199); color: rgb(180 83 9); }
        .cp-lec-quiz {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            background: rgb(254 243 199);
            color: rgb(146 64 14);
            padding: 0.125rem 0.375rem;
            border-radius: 9999px;
            font-size: 0.625rem;
            font-weight: 700;
            margin-left: 0.25rem;
        }
        .dark .cp-lec-quiz { background: rgba(245,158,11,0.18); color: rgb(252 211 77); }

        /* ─── Main reader ─── */
        .cp-main {
            background: white;
            border: 1px solid rgb(229 231 235);
            border-radius: 0.875rem;
            overflow: hidden;
            min-width: 0;
        }
        .dark .cp-main { background: rgb(17 24 39); border-color: rgb(31 41 55); }

        .cp-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgb(229 231 235);
            flex-wrap: wrap;
        }
        .dark .cp-toolbar { border-color: rgb(31 41 55); }
        .cp-crumb {
            font-size: 0.75rem;
            color: rgb(107 114 128);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 600;
        }
        .cp-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: rgb(17 24 39);
            margin-top: 0.25rem;
            line-height: 1.3;
        }
        .dark .cp-title { color: rgb(243 244 246); }
        @media (min-width: 768px) { .cp-title { font-size: 1.5rem; } }

        .cp-tools { display: flex; gap: 0.5rem; flex-wrap: wrap; }

        .cp-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.5rem 0.875rem;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 600;
            border: 1px solid rgb(229 231 235);
            background: white;
            color: rgb(31 41 55);
            cursor: pointer;
            transition: all 120ms;
            text-decoration: none;
        }
        .dark .cp-btn { background: rgb(30 41 59); border-color: rgb(51 65 85); color: rgb(226 232 240); }
        .cp-btn:hover { border-color: rgb(99 102 241); color: rgb(67 56 202); }
        .dark .cp-btn:hover { border-color: rgb(129 140 248); color: rgb(199 210 254); }
        .cp-btn.is-primary { background: rgb(99 102 241); color: white; border-color: rgb(99 102 241); }
        .cp-btn.is-primary:hover { background: rgb(79 70 229); color: white; }
        .cp-btn.is-success { background: rgb(16 185 129); color: white; border-color: rgb(16 185 129); }
        .cp-btn.is-success:hover { background: rgb(5 150 105); color: white; }

        /* Tab strip */
        .cp-tabs {
            display: flex;
            gap: 0.25rem;
            padding: 0.5rem 1.25rem 0;
            border-bottom: 1px solid rgb(229 231 235);
            background: rgb(249 250 251);
        }
        .dark .cp-tabs { background: rgb(15 23 42); border-color: rgb(31 41 55); }
        .cp-tab {
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: rgb(107 114 128);
            border-bottom: 2px solid transparent;
            cursor: pointer;
            background: transparent;
            border-left: none;
            border-right: none;
            border-top: none;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
        }
        .cp-tab:hover { color: rgb(55 65 81); }
        .dark .cp-tab:hover { color: rgb(226 232 240); }
        .cp-tab.is-active {
            color: rgb(67 56 202);
            border-bottom-color: rgb(99 102 241);
        }
        .dark .cp-tab.is-active { color: rgb(199 210 254); border-bottom-color: rgb(129 140 248); }

        /* ─── Reading view (docs-style typography) ─── */
        .cp-reading {
            padding: 2rem 1.5rem;
            max-width: 800px;
            margin: 0 auto;
        }
        @media (min-width: 768px) { .cp-reading { padding: 2.5rem 2rem; } }
        .cp-reading-body {
            font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
            font-size: 1rem;
            line-height: 1.75;
            color: rgb(31 41 55);
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .dark .cp-reading-body { color: rgb(226 232 240); }
        @media (min-width: 768px) { .cp-reading-body { font-size: 1.0625rem; } }

        .cp-reading-empty {
            text-align: center;
            padding: 4rem 1rem;
            color: rgb(107 114 128);
        }
        .cp-reading-empty-icon {
            width: 3rem;
            height: 3rem;
            margin: 0 auto 1rem;
            background: rgb(243 244 246);
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgb(107 114 128);
        }
        .dark .cp-reading-empty-icon { background: rgb(31 41 55); }

        /* ─── PDF embed ─── */
        .cp-pdf-frame {
            width: 100%;
            height: 80vh;
            border: 0;
            display: block;
            background: rgb(17 24 39);
        }
        .cp-no-original {
            padding: 3rem 1.5rem;
            text-align: center;
            color: rgb(107 114 128);
        }

        /* Footer (prev/next) */
        .cp-foot {
            border-top: 1px solid rgb(229 231 235);
            padding: 1rem 1.25rem;
            display: flex;
            justify-content: space-between;
            gap: 0.5rem;
            background: rgb(249 250 251);
        }
        .dark .cp-foot { background: rgb(15 23 42); border-color: rgb(31 41 55); }
        .cp-foot .cp-btn { min-width: 7rem; justify-content: center; }
    </style>

    <div class="cp-shell">
        {{-- ═══════════════ SIDEBAR ═══════════════ --}}
        <aside class="cp-side">
            <div class="cp-side-title">{{ $course->title }}</div>

            @if ($totalLectures > 0)
                <div class="cp-progress-block">
                    <div class="cp-progress-head">
                        <span>Your progress</span>
                        <strong>{{ $viewedCount }} / {{ $totalLectures }} · {{ $progressPct }}%</strong>
                    </div>
                    <div class="cp-progress-bar">
                        <div class="cp-progress-fill {{ $progressPct >= 100 ? 'complete' : '' }}" style="width: {{ $progressPct }}%"></div>
                    </div>
                </div>
            @endif

            @if (empty($lectures))
                <p style="padding: 1rem; color: rgb(107 114 128); font-size: 0.8125rem;">
                    No lectures yet.
                </p>
            @endif

            @foreach ($lectures as $i => $l)
                @php
                    $isActive = $currentLecture?->id === $l['id'];
                    $isViewed = isset($viewedLectureIds[$l['id']]);
                @endphp
                <button type="button"
                        wire:click="selectLecture({{ $l['id'] }})"
                        class="cp-lec-btn {{ $isActive ? 'is-active' : '' }} {{ $isViewed ? 'is-viewed' : '' }}">
                    <span class="cp-lec-num">
                        @if ($isViewed && ! $isActive)
                            <svg style="width:0.875rem; height:0.875rem;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @else
                            {{ $i + 1 }}
                        @endif
                    </span>
                    <span class="cp-lec-title">
                        {{ $l['title'] }}
                        @if ($l['quiz_count'] > 0)
                            <span class="cp-lec-quiz">⚡ Quiz</span>
                        @endif
                        <span class="cp-lec-badge {{ $l['content_type'] === 'video_transcript' ? 'transcript' : $l['content_type'] }}">
                            {{ strtoupper($l['content_type'] === 'video_transcript' ? 'Transcript' : $l['content_type']) }}
                        </span>
                    </span>
                </button>
            @endforeach
        </aside>

        {{-- ═══════════════ MAIN ═══════════════ --}}
        <main class="cp-main">
            @if ($lec)
                {{-- Toolbar --}}
                <div class="cp-toolbar">
                    <div style="min-width: 0; flex: 1;">
                        <div class="cp-crumb">Lecture {{ $current + 1 }} of {{ $totalLectures }} · {{ $course->category?->name }}</div>
                        <h1 class="cp-title">{{ $lec->title }}</h1>
                    </div>
                    <div class="cp-tools">
                        @if ($hasFile)
                            <a href="{{ route('student.lectures.download', $lec) }}"
                               class="cp-btn">
                                <svg style="width:1rem; height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                Download
                            </a>
                        @endif
                        @if ($publishedQuiz)
                            <a href="{{ \App\Filament\Student\Pages\TakeQuiz::getUrl(['quiz' => $publishedQuiz->id]) }}"
                               class="cp-btn is-success">
                                <svg style="width:1rem; height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 010 1.972l-11.54 6.347a1.125 1.125 0 01-1.667-.986V5.653z"/></svg>
                                Take Quiz
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Tabs (only show "Original" tab when there's a viewable file) --}}
                @if ($hasOriginalSupported || $hasText)
                    <div class="cp-tabs">
                        @if ($hasText)
                            <button type="button"
                                    wire:click="setTab('reading')"
                                    class="cp-tab {{ $tab === 'reading' ? 'is-active' : '' }}">
                                <svg style="width:1rem; height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                Reading view
                            </button>
                        @endif
                        @if ($hasOriginalSupported)
                            <button type="button"
                                    wire:click="setTab('original')"
                                    class="cp-tab {{ $tab === 'original' ? 'is-active' : '' }}">
                                <svg style="width:1rem; height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                Original file
                            </button>
                        @endif
                    </div>
                @endif

                {{-- Content body --}}
                @if ($tab === 'reading')
                    @if ($hasText)
                        <article class="cp-reading">
                            <div class="cp-reading-body">{{ $lec->raw_text }}</div>
                        </article>
                    @else
                        <div class="cp-reading-empty">
                            <div class="cp-reading-empty-icon">
                                <svg style="width:1.5rem; height:1.5rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            </div>
                            <p>No readable text for this lecture yet.</p>
                            @if ($hasFile)
                                <a href="{{ route('student.lectures.download', $lec) }}" class="cp-btn is-primary" style="margin-top:1rem;">Download the original file</a>
                            @endif
                        </div>
                    @endif
                @elseif ($tab === 'original')
                    @if ($hasOriginalSupported)
                        <iframe class="cp-pdf-frame"
                                src="{{ route('student.lectures.view', $lec) }}#toolbar=1&navpanes=0&view=FitH"
                                title="{{ $lec->title }}"></iframe>
                    @else
                        <div class="cp-no-original">
                            <p>This file format can't be previewed in-browser.</p>
                            @if ($hasFile)
                                <a href="{{ route('student.lectures.download', $lec) }}" class="cp-btn is-primary" style="margin-top:1rem;">Download to view</a>
                            @endif
                        </div>
                    @endif
                @endif

                {{-- Prev / Next footer --}}
                <div class="cp-foot">
                    <button type="button"
                            wire:click="previousLecture"
                            class="cp-btn"
                            @disabled($current === 0)
                            style="@if($current===0)opacity:0.4; cursor:not-allowed;@endif">
                        ← Previous lecture
                    </button>
                    <button type="button"
                            wire:click="nextLecture"
                            class="cp-btn is-primary"
                            @disabled($current >= $totalLectures - 1)
                            style="@if($current>=$totalLectures-1)opacity:0.4; cursor:not-allowed;@endif">
                        Next lecture →
                    </button>
                </div>
            @else
                <div class="cp-no-original">
                    <p>This course doesn't have any lectures yet.</p>
                </div>
            @endif
        </main>
    </div>
</x-filament-panels::page>
