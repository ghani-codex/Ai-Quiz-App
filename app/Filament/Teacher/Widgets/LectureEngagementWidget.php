<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Course;
use App\Models\Lecture;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class LectureEngagementWidget extends Widget
{
    protected static string $view = 'filament.teacher.widgets.lecture-engagement';

    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    /** Livewire-bound filter — 'all' or a course id. */
    public string $courseFilter = 'all';

    /**
     * Course options for the dropdown.
     * @return array<int|string, string>
     */
    public function getCourseOptions(): array
    {
        $teacherCourses = Course::where('teacher_id', auth()->id())
            ->orderBy('title')
            ->pluck('title', 'id')
            ->all();

        return ['all' => 'All my courses'] + $teacherCourses;
    }

    /**
     * Build the engagement rows.
     * @return array<int, array<string, mixed>>
     */
    public function getEngagementRows(): array
    {
        $teacherId = auth()->id();

        $base = Lecture::query()
            ->select(
                'lectures.id',
                'lectures.title',
                'lectures.course_id',
                'courses.title as course_title',
                DB::raw('COUNT(DISTINCT lecture_views.user_id) as students_opened'),
                DB::raw('COALESCE(SUM(lecture_views.view_count), 0) as total_reads'),
                DB::raw('COALESCE(SUM(lecture_views.download_count), 0) as total_downloads')
            )
            ->join('courses', 'courses.id', '=', 'lectures.course_id')
            ->leftJoin('lecture_views', 'lecture_views.lecture_id', '=', 'lectures.id')
            ->where('courses.teacher_id', $teacherId);

        if ($this->courseFilter !== 'all' && is_numeric($this->courseFilter)) {
            $base->where('lectures.course_id', (int) $this->courseFilter);
        }

        $rows = $base
            ->groupBy('lectures.id', 'lectures.title', 'lectures.course_id', 'courses.title')
            ->orderByDesc('students_opened')
            ->orderByDesc('total_reads')
            ->limit(15)
            ->get();

        // Total enrolment per course (for "X / Y opened" denominator)
        $courseEnrolments = DB::table('enrollments')
            ->select('course_id', DB::raw('COUNT(*) as cnt'))
            ->whereIn('course_id', $rows->pluck('course_id')->unique())
            ->groupBy('course_id')
            ->pluck('cnt', 'course_id');

        // Find the max value across all metrics for relative bar scaling
        $maxOpens     = max(1, $rows->max('students_opened') ?? 1);
        $maxReads     = max(1, $rows->max('total_reads') ?? 1);
        $maxDownloads = max(1, $rows->max('total_downloads') ?? 1);

        return $rows->map(function ($r) use ($courseEnrolments, $maxOpens, $maxReads, $maxDownloads) {
            $enrolled    = (int) ($courseEnrolments[$r->course_id] ?? 0);
            $openPct     = $enrolled > 0 ? (int) round(($r->students_opened / $enrolled) * 100) : 0;

            return [
                'id'              => $r->id,
                'title'           => $r->title,
                'course_title'    => $r->course_title,
                'students_opened' => (int) $r->students_opened,
                'total_reads'     => (int) $r->total_reads,
                'total_downloads' => (int) $r->total_downloads,
                'enrolled'        => $enrolled,
                'open_pct'        => $openPct,
                'opens_bar_pct'   => (int) (($r->students_opened / $maxOpens) * 100),
                'reads_bar_pct'   => (int) (($r->total_reads / $maxReads) * 100),
                'downloads_bar_pct' => (int) (($r->total_downloads / $maxDownloads) * 100),
                'has_any'         => $r->students_opened > 0 || $r->total_reads > 0 || $r->total_downloads > 0,
            ];
        })->all();
    }

    /**
     * Headline aggregates for the three ring charts.
     * @return array<string, mixed>
     */
    public function getAggregates(array $rows): array
    {
        $totalLectures = count($rows);
        $engaged       = collect($rows)->where('has_any', true)->count();
        $totalReads    = (int) collect($rows)->sum('total_reads');
        $totalDownloads = (int) collect($rows)->sum('total_downloads');
        $totalStudents  = (int) collect($rows)->sum('students_opened');
        $totalEnrolled  = collect($rows)->pluck('enrolled')->max() ?? 0; // max across courses (denominator for openPct)

        // Engagement-rate %: across all lectures, what fraction of (lecture × enrolled student) pairs got opened?
        $maxPossibleOpens = collect($rows)->sum(fn ($r) => $r['enrolled']);
        $actualOpens      = collect($rows)->sum('students_opened');
        $engagementRate   = $maxPossibleOpens > 0 ? (int) round(($actualOpens / $maxPossibleOpens) * 100) : 0;

        // Average reads-per-lecture (rounded)
        $avgReads = $totalLectures > 0 ? round($totalReads / $totalLectures, 1) : 0;

        // Download conversion: of those who opened, what % also downloaded?
        $downloadRate = $actualOpens > 0 ? (int) round(($totalDownloads / max(1, $actualOpens)) * 100) : 0;

        return [
            'total_lectures'        => $totalLectures,
            'engaged_lectures'      => $engaged,
            'engaged_lectures_pct'  => $totalLectures > 0 ? (int) round(($engaged / $totalLectures) * 100) : 0,
            'engagement_rate'       => $engagementRate,
            'actual_opens'          => $actualOpens,
            'max_possible_opens'    => $maxPossibleOpens,
            'total_reads'           => $totalReads,
            'avg_reads'             => $avgReads,
            'total_downloads'       => $totalDownloads,
            'download_rate'         => $downloadRate,
        ];
    }
}
