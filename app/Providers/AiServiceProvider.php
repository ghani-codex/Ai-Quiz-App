<?php

namespace App\Providers;

use App\Services\AI\Contracts\AiProvider;
use App\Services\AI\Providers\FakeProvider;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\GroqProvider;
use Illuminate\Support\ServiceProvider;

class AiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AiProvider::class, function () {
            $provider = config('ai.provider', 'gemini');

            // Auto-fallback to FakeProvider when no key is configured
            // (lets the user demo the UI without signing up for Gemini).
            $key = config("ai.providers.{$provider}.key");
            if (empty($key)) {
                return new FakeProvider();
            }

            return match ($provider) {
                'gemini' => new GeminiProvider(),
                'groq'   => new GroqProvider(),
                default  => new FakeProvider(),
            };
        });
    }
}
