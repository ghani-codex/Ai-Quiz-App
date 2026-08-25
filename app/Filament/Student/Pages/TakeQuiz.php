<?php

namespace App\Filament\Student\Pages;

use App\Models\AttemptAnswer;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class TakeQuiz extends Page
{
    protected static string $view = 'filament.student.pages.take-quiz';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Take Quiz';

    public Quiz $quiz;

    public QuizAttempt $attempt;

    public array $questions = [];

    public int $currentIndex = 0;

    /** @var array<int, int> question_id => option_id */
    public array $answers = [];

    public int $secondsRemaining = 0;

    public bool $submitted = false;

    public static function getSlug(): string
    {
        return 'quizzes/{quiz}/attempt';
    }

    public function mount(Quiz $quiz): void
    {
        $quiz->load(['questions.options', 'lecture.course.enrollments']);

        // Must be enrolled in the parent course
        $isEnrolled = $quiz->lecture?->course?->enrollments
            ?->where('user_id', auth()->id())->isNotEmpty() ?? false;

        abort_unless($isEnrolled, 403, 'You must enrol in this course to take its quizzes.');
        abort_unless($quiz->is_published && $quiz->ai_generation_status === 'ready' && $quiz->questions->isNotEmpty(),
            404, 'Quiz is not available.');

        $this->quiz = $quiz;

        // Resume an in-progress attempt if one exists, else create a new one
        $existing = QuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->whereNull('submitted_at')
            ->latest('started_at')
            ->first();

        if ($existing) {
            $this->attempt = $existing;
            // Restore any previously selected answers
            foreach ($existing->answers as $ans) {
                if ($ans->selected_option_id) {
                    $this->answers[$ans->question_id] = $ans->selected_option_id;
                }
            }
        } else {
            $this->attempt = QuizAttempt::create([
                'user_id'    => auth()->id(),
                'quiz_id'    => $quiz->id,
                'started_at' => now(),
            ]);
        }

        // Shuffle question order once per attempt; persist the order via session keyed by attempt id
        $order = session("quiz_order.{$this->attempt->id}");
        if (! is_array($order)) {
            $order = $quiz->questions->pluck('id')->shuffle()->all();
            session(["quiz_order.{$this->attempt->id}" => $order]);
        }

        $byId = $quiz->questions->keyBy('id');
        $this->questions = collect($order)
            ->map(fn ($id) => $byId->get($id))
            ->filter()
            ->values()
            ->map(fn ($q) => [
                'id'      => $q->id,
                'text'    => $q->text,
                'options' => $q->options->map(fn ($o) => [
                    'id'   => $o->id,
                    'text' => $o->text,
                ])->values()->all(),
            ])->all();

        // Compute remaining seconds based on server time, not client clock
        $elapsed = (int) abs(now()->diffInSeconds($this->attempt->started_at, true));
        $limit   = $quiz->time_limit_minutes * 60;
        $this->secondsRemaining = max(0, $limit - $elapsed);

        if ($this->secondsRemaining === 0) {
            $this->submit();
        }
    }

    public function selectOption(int $questionId, int $optionId): void
    {
        if ($this->submitted) {
            return;
        }
        $this->answers[$questionId] = $optionId;
    }

    public function next(): void
    {
        if ($this->currentIndex < count($this->questions) - 1) {
            $this->currentIndex++;
        }
    }

    public function previous(): void
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
        }
    }

    public function jumpTo(int $index): void
    {
        if ($index >= 0 && $index < count($this->questions)) {
            $this->currentIndex = $index;
        }
    }

    public function submit(): void
    {
        if ($this->submitted) {
            return;
        }
        $this->submitted = true;

        $now           = now();
        $startedAt     = $this->attempt->started_at;
        $elapsedSecs   = (int) abs($now->diffInSeconds($startedAt, true));
        $limitSecs     = $this->quiz->time_limit_minutes * 60;
        $effectiveSecs = min($elapsedSecs, $limitSecs + 5); // grace +5s

        $correct = 0;
        $maxPts  = 0;
        $earned  = 0;

        DB::transaction(function () use (&$correct, &$maxPts, &$earned, $effectiveSecs, $now) {
            // Clear any prior answers from a resumed attempt
            AttemptAnswer::where('quiz_attempt_id', $this->attempt->id)->delete();

            $this->quiz->load('questions.options');
            foreach ($this->quiz->questions as $q) {
                $maxPts += $q->points;
                $selectedId  = $this->answers[$q->id] ?? null;
                $selectedOpt = $q->options->firstWhere('id', $selectedId);
                $isCorrect   = (bool) ($selectedOpt?->is_correct ?? false);

                if ($isCorrect) {
                    $correct++;
                    $earned += $q->points;
                }

                AttemptAnswer::create([
                    'quiz_attempt_id'    => $this->attempt->id,
                    'question_id'        => $q->id,
                    'selected_option_id' => $selectedId,
                    'is_correct'         => $isCorrect,
                    'points_awarded'     => $isCorrect ? $q->points : 0,
                ]);
            }

            $score = $maxPts > 0 ? round(($earned / $maxPts) * 100, 2) : 0;

            $this->attempt->update([
                'submitted_at'       => $now,
                'score'              => $score,
                'total_points'       => $maxPts,
                'time_taken_seconds' => $effectiveSecs,
            ]);
        });

        // Clear shuffled order from session
        session()->forget("quiz_order.{$this->attempt->id}");

        Notification::make()
            ->title('Quiz submitted')
            ->body("You answered {$correct} out of {$maxPts} correctly.")
            ->success()
            ->send();

        $this->redirect(\App\Filament\Student\Pages\QuizResults::getUrl(['attempt' => $this->attempt->id]));
    }
}
