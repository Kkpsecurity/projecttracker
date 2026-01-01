<?php

namespace App\Providers;

use App\Services\LLM\Drivers\AnthropicClient;
use App\Services\LLM\Drivers\OllamaClient;
use App\Services\LLM\Drivers\OpenAIClient;
use App\Services\LLM\LLMClient;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class LLMServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LLMClient::class, function () {
            $provider = config('llm.provider');

            return match ($provider) {
                'openai' => new OpenAIClient(
                    apiKey: (string) config('llm.openai.api_key', ''),
                    baseUrl: (string) config('llm.openai.base_url', 'https://api.openai.com/v1'),
                    model: (string) config('llm.openai.model', 'gpt-4o-mini'),
                    timeoutSeconds: (int) config('llm.openai.timeout', 60),
                ),

                'ollama' => new OllamaClient(
                    baseUrl: (string) config('llm.ollama.base_url', 'http://127.0.0.1:11434'),
                    model: (string) config('llm.ollama.model', 'llama3.1'),
                    timeoutSeconds: (int) config('llm.ollama.timeout', 120),
                ),

                'anthropic' => new AnthropicClient(
                    apiKey: (string) config('llm.anthropic.api_key', ''),
                    baseUrl: (string) config('llm.anthropic.base_url', 'https://api.anthropic.com'),
                    model: (string) config('llm.anthropic.model', 'claude-3-5-sonnet-20241022'),
                    timeoutSeconds: (int) config('llm.anthropic.timeout', 60),
                ),

                default => throw new InvalidArgumentException('Unsupported LLM_PROVIDER: '.$provider),
            };
        });
    }
}
