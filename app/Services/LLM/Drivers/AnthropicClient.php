<?php

namespace App\Services\LLM\Drivers;

use App\Services\LLM\LLMClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class AnthropicClient implements LLMClient
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
            throw new RuntimeException('ANTHROPIC_API_KEY is not set.');
        }

        $response = Http::timeout($this->timeoutSeconds)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
            ])
            ->acceptJson()
            ->asJson()
            ->post(rtrim($this->baseUrl, '/').'/v1/messages', [
                'model' => $options['model'] ?? $this->model,
                'max_tokens' => (int) ($options['max_tokens'] ?? 800),
                'temperature' => $options['temperature'] ?? 0.2,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if (! $response->ok()) {
            throw new RuntimeException('Anthropic request failed: '.$response->status().' '.$response->body());
        }

        $content = data_get($response->json(), 'content.0.text');

        if (! is_string($content) || Str::of($content)->trim()->isEmpty()) {
            throw new RuntimeException('Anthropic response missing content.');
        }

        return trim($content);
    }
}
