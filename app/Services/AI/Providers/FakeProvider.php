<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AiProvider;

/**
 * Demo provider — produces deterministic synthetic questions from the lecture
 * text without any API call. Used when GEMINI_API_KEY is not set, so the user
 * can still try the end-to-end flow locally before signing up for Gemini.
 */
class FakeProvider implements AiProvider
{
    public function name(): string
    {
        return 'fake';
    }

    public function generateQuiz(string $lectureText, int $count, string $difficulty): array
    {
        // Pick N pseudo-sentences from the lecture text to seed the questions.
        $sentences = preg_split('/(?<=[.!?])\s+/', strip_tags($lectureText));
        $sentences = array_values(array_filter(array_map('trim', $sentences ?: []), fn ($s) => mb_strlen($s) > 20));

        $questions = [];
        for ($i = 0; $i < $count; $i++) {
            $seed = $sentences[$i % max(count($sentences), 1)] ?? 'the lecture content';
            $excerpt = mb_substr($seed, 0, 120);
            $correctIndex = $i % 4;

            $options = [
                'Option A — closely matches the lecture',
                'Option B — partly correct but misses the point',
                'Option C — common misconception',
                'Option D — unrelated to the lecture',
            ];
            // Move "matches the lecture" to correct_index
            [$options[0], $options[$correctIndex]] = [$options[$correctIndex], $options[0]];

            $questions[] = [
                'text'          => "[{$difficulty}] Based on the lecture, which statement is most accurate regarding: \"{$excerpt}…\"?",
                'options'       => $options,
                'correct_index' => $correctIndex,
                'explanation'   => "The lecture explicitly discusses this. (Demo question generated locally — set GEMINI_API_KEY in .env for real Agent output.)",
            ];
        }

        return [
            'questions'   => $questions,
            'tokens_used' => 0,
            'model'       => 'fake-local-v1',
        ];
    }
}
