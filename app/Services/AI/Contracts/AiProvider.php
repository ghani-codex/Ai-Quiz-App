<?php

namespace App\Services\AI\Contracts;

interface AiProvider
{
    /**
     * Provider identifier — stored in ai_usage_logs.
     */
    public function name(): string;

    /**
     * Generate quiz questions from a lecture's raw text.
     *
     * Returns: [
     *   'questions'      => array of [text, options, correct_index, explanation],
     *   'tokens_used'    => int,
     *   'model'          => string,
     * ]
     */
    public function generateQuiz(string $lectureText, int $count, string $difficulty): array;
}
