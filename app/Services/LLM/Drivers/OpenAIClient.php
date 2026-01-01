<?php

namespace App\Services\LLM\Drivers;

use App\Services\LLM\LLMClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class OpenAIClient implements LLMClient
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUrl,
        private readonly string $model,
        private readonly int $timeoutSeconds,
    ) {
    }

    public function chat(string $prompt, array $options = []): string
    {
        if ($this->apiKey === '') {
            throw new RuntimeException('OPENAI_API_KEY is not set.');
        }

        $temperature = $options['temperature'] ?? 0.2;

        $response = Http::timeout($this->timeoutSeconds)
            ->withToken($this->apiKey)
            ->acceptJson()
            ->asJson()
            ->post(rtrim($this->baseUrl, '/').'/chat/completions', [
                'model' => $options['model'] ?? $this->model,
                'temperature' => $temperature,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if (! $response->ok()) {
            throw new RuntimeException('OpenAI request failed: '.$response->status().' '.$response->body());
        }

        $content = data_get($response->json(), 'choices.0.message.content');

        if (! is_string($content) || Str::of($content)->trim()->isEmpty()) {
            throw new RuntimeException('OpenAI response missing content.');
        }

        return trim($content);
    }
}
