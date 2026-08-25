<?php

/*
|--------------------------------------------------------------------------
| AI Provider Configuration
|--------------------------------------------------------------------------
|
| Primary: Google Gemini 2.5 Flash via the OpenAI-compatible endpoint.
| Fallback: Groq Llama 3.3 70B Versatile (also OpenAI-compatible).
| Fake:    Hard-coded MCQs — works without any API key, for local demo.
|
| If GEMINI_API_KEY is empty, FakeProvider is used automatically.
|
*/

return [
    'provider' => env('AI_PROVIDER', 'gemini'),

    'providers' => [
        'gemini' => [
            'key'      => env('GEMINI_API_KEY'),
            'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta/openai'),
            'model'    => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        ],
        'groq' => [
            'key'      => env('GROQ_API_KEY'),
            'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
            'model'    => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        ],
    ],

    'limits' => [
        'daily_per_user' => (int) env('AI_DAILY_LIMIT_PER_USER', 20),
        'per_minute'     => (int) env('AI_PER_MINUTE_LIMIT', 3),
    ],

    // Maximum lecture text to send to the LLM (hard cap before sending).
    'max_context_chars' => 60000,

    'prompts' => [

        'quiz_system' => <<<PROMPT
You are an expert educator preparing assessments for university students.

Read the supplied LECTURE TEXT carefully and produce exactly the requested
number of multiple-choice questions.

Hard rules:
1. Each question MUST be answerable from the lecture text alone.
2. Each question MUST have exactly 4 options.
3. Exactly ONE option is correct; the other 3 are plausible distractors.
4. `correct_index` is the zero-based index (0, 1, 2, or 3) of the correct option.
5. Each question MUST include a one-or-two-sentence `explanation` of WHY the answer is correct, referencing the lecture.
6. Do not repeat the same question twice in any form.
7. Match the requested DIFFICULTY:
   - easy: direct recall of explicit facts stated in the lecture.
   - medium: light inference, combining two facts from the lecture.
   - hard: multi-step reasoning, application, or comparison.

Return STRICTLY valid JSON matching this shape — no markdown, no commentary:
{
  "questions": [
    {
      "text": "string",
      "options": ["string", "string", "string", "string"],
      "correct_index": 0,
      "explanation": "string"
    }
  ]
}
PROMPT,

        'quiz_user_template' => <<<TPL
DIFFICULTY: {{difficulty}}
NUMBER OF QUESTIONS: {{count}}

LECTURE TEXT (verbatim — ignore formatting noise):
\"\"\"
{{lectureText}}
\"\"\"

Generate exactly {{count}} questions of {{difficulty}} difficulty. Return JSON ONLY.
TPL,

    ],
];
