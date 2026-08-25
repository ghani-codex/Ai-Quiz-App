<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AiProvider;
use OpenAI;

class GeminiProvider implements AiProvider
{
    public function name(): string
    {
        return 'gemini';
    }

    public function generateQuiz(string $lectureText, int $count, string $difficulty): array
    {
        $key   = config('ai.providers.gemini.key');
        $base  = config('ai.providers.gemini.base_url');
        $model = config('ai.providers.gemini.model');

        if (empty($key)) {
            throw new \RuntimeException('GEMINI_API_KEY is not set in .env');
        }

        $client = OpenAI::factory()
            ->withApiKey($key)
            ->withBaseUri($base)
            ->make();

        $system = config('ai.prompts.quiz_system');
        $user   = strtr(config('ai.prompts.quiz_user_template'), [
            '{{difficulty}}'  => $difficulty,
            '{{count}}'       => (string) $count,
            '{{lectureText}}' => $lectureText,
        ]);

        $response = $client->chat()->create([
            'model'           => $model,
            'response_format' => ['type' => 'json_object'],
            'temperature'     => 0.4,
            'messages'        => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user',   'content' => $user],
            ],
        ]);

        $content = $response->choices[0]->message->content ?? '';
        $parsed  = json_decode($content, true);

        if (! is_array($parsed) || ! isset($parsed['questions']) || ! is_array($parsed['questions'])) {
            throw new \RuntimeException('Gemini returned malformed JSON: ' . mb_substr($content, 0, 200));
        }

        return [
            'questions'   => $parsed['questions'],
            'tokens_used' => $response->usage->totalTokens ?? 0,
            'model'       => $model,
        ];
    }
}
