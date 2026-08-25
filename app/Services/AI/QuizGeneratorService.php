<?php

namespace App\Services\AI;

use App\Models\AiUsageLog;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Services\AI\Contracts\AiProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuizGeneratorService
{
    public function __construct(private AiProvider $provider) {}

    /**
     * Generate questions for a Quiz, replacing any existing ones.
     * Returns the number of questions written.
     *
     * @throws \Throwable
     */
    public function generate(Quiz $quiz): int
    {
        $quiz->load('lecture');
        $lecture = $quiz->lecture;

        if (! $lecture || empty(trim($lecture->raw_text))) {
            throw new \RuntimeException('Lecture has no raw_text. Paste or upload content first, then regenerate.');
        }

        $start = microtime(true);

        $maxChars = (int) config('ai.max_context_chars', 60000);
        $text     = mb_substr($this->sanitize($lecture->raw_text), 0, $maxChars);

        try {
            $quiz->update(['ai_generation_status' => 'processing']);

            $result = $this->provider->generateQuiz(
                $text,
                $quiz->question_count,
                $quiz->difficulty,
            );

            $valid = $this->validate($result['questions']);

            DB::transaction(function () use ($quiz, $valid) {
                // Replace existing questions
                $quiz->questions()->delete();

                foreach ($valid as $i => $q) {
                    $question = Question::create([
                        'quiz_id'     => $quiz->id,
                        'type'        => 'mcq',
                        'text'        => $q['text'],
                        'explanation' => $q['explanation'] ?? null,
                        'points'      => 1,
                        'order'       => $i + 1,
                    ]);

                    foreach ($q['options'] as $optIndex => $optText) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'text'        => $optText,
                            'is_correct'  => $optIndex === $q['correct_index'],
                            'order'       => $optIndex,
                        ]);
                    }
                }
            });

            $quiz->update([
                'ai_generation_status' => 'ready',
                'ai_provider_used'     => $this->provider->name(),
                'ai_tokens_used'       => $result['tokens_used'] ?? 0,
                'ai_generated_at'      => now(),
            ]);

            AiUsageLog::create([
                'provider'        => $this->provider->name(),
                'model'           => $result['model'] ?? 'unknown',
                'input_tokens'    => 0,
                'output_tokens'   => $result['tokens_used'] ?? 0,
                'status'          => 'success',
                'user_id'         => auth()->id(),
                'related_quiz_id' => $quiz->id,
                'latency_ms'      => (int) ((microtime(true) - $start) * 1000),
            ]);

            return count($valid);
        } catch (\Throwable $e) {
            $quiz->update(['ai_generation_status' => 'failed']);

            AiUsageLog::create([
                'provider'        => $this->provider->name(),
                'model'           => 'unknown',
                'status'          => 'failed',
                'user_id'         => auth()->id(),
                'related_quiz_id' => $quiz->id,
                'error_message'   => $e->getMessage(),
                'latency_ms'      => (int) ((microtime(true) - $start) * 1000),
            ]);

            throw $e;
        }
    }

    private function sanitize(string $text): string
    {
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;
        return trim($text);
    }

    private function validate(array $questions): array
    {
        $validator = Validator::make(['questions' => $questions], [
            'questions'                   => 'required|array|min:1',
            'questions.*.text'            => 'required|string|min:5',
            'questions.*.options'         => 'required|array|size:4',
            'questions.*.options.*'       => 'required|string|min:1',
            'questions.*.correct_index'   => 'required|integer|min:0|max:3',
            'questions.*.explanation'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new \RuntimeException('Agent response failed validation: ' . $validator->errors()->first());
        }

        return $questions;
    }
}
