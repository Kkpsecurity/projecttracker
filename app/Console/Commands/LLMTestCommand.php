<?php

namespace App\Console\Commands;

use App\Services\LLM\LLMClient;
use Illuminate\Console\Command;

class LLMTestCommand extends Command
{
    protected $signature = 'llm:test {prompt? : Prompt text} {--provider= : Override LLM_PROVIDER} {--model= : Override model}';

    protected $description = 'Send a test prompt to the configured LLM provider.';

    public function handle(LLMClient $llm): int
    {
        $prompt = (string) ($this->argument('prompt') ?? 'Reply with exactly: OK');

        $options = [];
        if ($this->option('model')) {
            $options['model'] = (string) $this->option('model');
        }

        if ($this->option('provider')) {
            config(['llm.provider' => (string) $this->option('provider')]);
            // Re-resolve LLM binding after config override.
            $llm = app(LLMClient::class);
        }

        $this->info('Provider: '.config('llm.provider'));
        $this->line('Prompt: '.$prompt);

        $text = $llm->chat($prompt, $options);

        $this->newLine();
        $this->line($text);

        return self::SUCCESS;
    }
}
