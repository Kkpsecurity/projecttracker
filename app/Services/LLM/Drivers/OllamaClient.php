<?php

namespace App\Services\LLM\Drivers;

use App\Services\LLM\LLMClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class OllamaClient implements LLMClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $model,
        private readonly int $timeoutSeconds,
    ) {
    }

    public function chat(string $prompt, array $options = []): string
    {
        $response = Http::timeout($this->timeoutSeconds)
            ->acceptJson()
            ->asJson()
            ->post(rtrim($this->baseUrl, '/').'/api/generate', [
                'model' => $options['model'] ?? $this->model,
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'temperature' => $options['temperature'] ?? 0.2,
                ],
            ]);

        if (! $response->ok()) {
            throw new RuntimeException('Ollama request failed: '.$response->status().' '.$response->body());
        }

        $text = data_get($response->json(), 'response');

        if (! is_string($text) || Str::of($text)->trim()->isEmpty()) {
            throw new RuntimeException('Ollama response missing text.');
        }

        return trim($text);
    }
}
