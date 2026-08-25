<?php

namespace App\Filament\Student\Pages;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lecture;
use App\Models\LectureView;
use Filament\Pages\Page;

class CoursePlayer extends Page
{
    protected static string $view = 'filament.student.pages.course-player';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Course';

    public Course $course;

    public array $lectures = [];

    public ?Lecture $currentLecture = null;

    public string $tab = 'reading'; // 'reading' | 'original'

    /** @var array<int, true> Lecture IDs the student has viewed */
    public array $viewedLectureIds = [];

    public int $totalLectures = 0;

    public int $viewedCount = 0;

    public int $progressPct = 0;

    public static function getSlug(): string
    {
        return 'courses/{course}/learn';
    }

    public function mount(Course $course): void
    {
        // Enrolment check
        $enrolled = $course->enrollments()->where('user_id', auth()->id())->exists();
        abort_unless($enrolled || auth()->user()->isAdmin(), 403, 'Enrol in this course to start learning.');

        $course->load(['lectures' => fn ($q) => $q->orderBy('order'), 'lectures.quizzes' => fn ($q) => $q->where('is_published', true), 'teacher:id,name', 'category:id,name']);
        $this->course = $course;

        $this->lectures = $course->lectures->map(fn (Lecture $l) => [
            'id'              => $l->id,
            'title'           => $l->title,
            'content_type'    => $l->content_type,
            'has_file'        => ! empty($l->file_path),
            'quiz_count'      => $l->quizzes->count(),
            'order'           => $l->order,
        ])->all();

        // Resolve current lecture from ?lecture= query (or first by default)
        $requested = (int) request()->query('lecture', 0);
        $this->currentLecture = $course->lectures->firstWhere('id', $requested)
            ?? $course->lectures->first();

        // Default to "original" tab if file is a PDF and the teacher gave us one;
        // otherwise reading view (which has the extracted text).
        $this->tab = ($this->currentLecture && $this->currentLecture->content_type === 'pdf' && $this->currentLecture->file_path)
            ? 'original'
            : 'reading';

        $this->refreshProgress();

        if ($this->currentLecture) {
            $this->trackView($this->currentLecture->id);
        }
    }

    /**
     * Record a lecture view and refresh sidebar checkmarks + progress bar.
     */
    protected function trackView(int $lectureId): void
    {
        if (! auth()->user()->isStudent()) {
            return; // admins/teachers previewing don't pollute analytics
        }

        LectureView::record(auth()->id(), $lectureId);

        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $this->course->id)
            ->first();
        $enrollment?->syncProgress();

        $this->refreshProgress();
    }

    protected function refreshProgress(): void
    {
        $lectureIds = collect($this->lectures)->pluck('id');
        $this->totalLectures = $lectureIds->count();

        $this->viewedLectureIds = LectureView::where('user_id', auth()->id())
            ->whereIn('lecture_id', $lectureIds)
            ->pluck('lecture_id')
            ->mapWithKeys(fn ($id) => [$id => true])
            ->all();

        $this->viewedCount = count($this->viewedLectureIds);
        $this->progressPct = $this->totalLectures > 0
            ? (int) round(($this->viewedCount / $this->totalLectures) * 100)
            : 0;
    }

    public function selectLecture(int $lectureId): void
    {
        $found = collect($this->lectures)->firstWhere('id', $lectureId);
        if (! $found) return;

        $this->currentLecture = Lecture::with('quizzes')->find($lectureId);

        $this->tab = ($this->currentLecture->content_type === 'pdf' && $this->currentLecture->file_path)
            ? 'original'
            : 'reading';

        $this->trackView($lectureId);
    }

    public function setTab(string $tab): void
    {
        if (! in_array($tab, ['reading', 'original'], true)) return;
        $this->tab = $tab;
    }

    public function getCurrentIndexProperty(): int
    {
        foreach ($this->lectures as $i => $l) {
            if ($l['id'] === ($this->currentLecture?->id)) return $i;
        }
        return 0;
    }

    public function previousLecture(): void
    {
        $i = $this->currentIndex;
        if ($i > 0) {
            $this->selectLecture($this->lectures[$i - 1]['id']);
        }
    }

    public function nextLecture(): void
    {
        $i = $this->currentIndex;
        if ($i < count($this->lectures) - 1) {
            $this->selectLecture($this->lectures[$i + 1]['id']);
        }
    }
}
