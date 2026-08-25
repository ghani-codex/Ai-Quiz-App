<?php

namespace Database\Seeders;

use App\Models\AttemptAnswer;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lecture;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('email', 'teacher@quiz.test')->first();
        $student = User::where('email', 'student@quiz.test')->first();

        // Spawn 15 extra students across the last 30 days for chart data
        $students = collect([$student]);
        for ($i = 1; $i <= 15; $i++) {
            $u = User::firstOrCreate(
                ['email' => "student{$i}@quiz.test"],
                [
                    'name'        => "Student {$i}",
                    'password'    => Hash::make('password'),
                    'role'        => User::ROLE_STUDENT,
                    'is_approved' => true,
                    'created_at'  => now()->subDays(rand(1, 28)),
                ],
            );
            $u->assignRole('student');
            $students->push($u);
        }

        $categories = Category::all()->keyBy('slug');

        $coursesData = [
            ['title' => 'Introduction to Python Programming', 'category' => 'programming', 'level' => 'beginner', 'featured' => true],
            ['title' => 'Data Structures and Algorithms',     'category' => 'programming', 'level' => 'intermediate', 'featured' => true],
            ['title' => 'Linear Algebra Essentials',          'category' => 'mathematics', 'level' => 'beginner', 'featured' => true],
            ['title' => 'Statistics for Data Science',        'category' => 'data-science', 'level' => 'intermediate', 'featured' => true],
            ['title' => 'Business Communication',             'category' => 'business',    'level' => 'beginner', 'featured' => false],
            ['title' => 'UI/UX Design Fundamentals',          'category' => 'design',      'level' => 'beginner', 'featured' => false],
        ];

        foreach ($coursesData as $i => $cd) {
            $cat = $categories->get($cd['category']) ?? $categories->first();

            $course = Course::firstOrCreate(
                ['slug' => Str::slug($cd['title'])],
                [
                    'teacher_id'   => $teacher->id,
                    'category_id'  => $cat->id,
                    'title'        => $cd['title'],
                    'description'  => "Hands-on {$cd['title']} for {$cd['level']} students. Covers the foundational concepts with AI-generated quizzes after every lecture.",
                    'level'        => $cd['level'],
                    'price'        => 0,
                    'is_published' => true,
                    'is_featured'  => $cd['featured'],
                    'created_at'   => now()->subDays(rand(5, 25)),
                ],
            );

            // 3 lectures per course
            foreach (range(1, 3) as $n) {
                $lecture = Lecture::firstOrCreate(
                    ['course_id' => $course->id, 'order' => $n],
                    [
                        'title'        => "Lecture {$n}: " . fake()->sentence(4),
                        'content_type' => 'text',
                        'raw_text'     => fake()->paragraphs(rand(3, 6), true),
                    ],
                );

                // 1 published quiz per lecture
                $quiz = Quiz::firstOrCreate(
                    ['lecture_id' => $lecture->id],
                    [
                        'title'                => "Quiz {$n} — " . Str::words($lecture->title, 3, ''),
                        'difficulty'           => collect(['easy', 'medium', 'hard'])->random(),
                        'question_count'       => 5,
                        'time_limit_minutes'   => 10,
                        'is_published'         => true,
                        'ai_generation_status' => 'ready',
                        'ai_provider_used'     => 'seed',
                        'ai_tokens_used'       => rand(800, 2000),
                        'ai_generated_at'      => now()->subDays(rand(1, 10)),
                    ],
                );

                if ($quiz->questions()->count() === 0) {
                    foreach (range(1, 5) as $qn) {
                        $question = Question::create([
                            'quiz_id'     => $quiz->id,
                            'type'        => 'mcq',
                            'text'        => "Sample question {$qn}: " . fake()->sentence() . '?',
                            'explanation' => fake()->sentence(),
                            'points'      => 1,
                            'order'       => $qn,
                        ]);
                        $correct = rand(0, 3);
                        foreach (range(0, 3) as $oi) {
                            QuestionOption::create([
                                'question_id' => $question->id,
                                'text'        => fake()->words(3, true),
                                'is_correct'  => $oi === $correct,
                                'order'       => $oi,
                            ]);
                        }
                    }
                }
            }
        }

        // Random enrollments + attempts
        $allCourses = Course::all();
        foreach ($students as $s) {
            $picks = $allCourses->random(rand(2, 4));
            foreach ($picks as $course) {
                $enrolledAt = now()->subDays(rand(1, 28));
                Enrollment::firstOrCreate(
                    ['user_id' => $s->id, 'course_id' => $course->id],
                    [
                        'enrolled_at'  => $enrolledAt,
                        'progress_pct' => rand(0, 100),
                    ],
                );

                // For each enrolled course's first quiz, ~70% chance to attempt
                $firstQuiz = $course->lectures()->with('quizzes')->first()?->quizzes->first();
                if ($firstQuiz && rand(1, 100) <= 70) {
                    $started = $enrolledAt->copy()->addHours(rand(1, 48));
                    $submitted = $started->copy()->addMinutes(rand(3, 9));
                    $score = rand(30, 100);
                    $attempt = QuizAttempt::firstOrCreate(
                        ['user_id' => $s->id, 'quiz_id' => $firstQuiz->id],
                        [
                            'started_at'         => $started,
                            'submitted_at'       => $submitted,
                            'score'              => $score,
                            'total_points'       => 100,
                            'time_taken_seconds' => abs($submitted->diffInSeconds($started)),
                            'feedback_text'      => 'Solid attempt — review the explanations you got wrong.',
                            'weak_areas'         => ['Topic A', 'Topic B'],
                        ],
                    );

                    foreach ($firstQuiz->questions as $q) {
                        $opts = $q->options;
                        $picked = $opts->random();
                        AttemptAnswer::firstOrCreate(
                            ['quiz_attempt_id' => $attempt->id, 'question_id' => $q->id],
                            [
                                'selected_option_id' => $picked->id,
                                'is_correct'         => $picked->is_correct,
                                'points_awarded'     => $picked->is_correct ? 1 : 0,
                            ],
                        );
                    }
                }
            }
        }
    }
}
