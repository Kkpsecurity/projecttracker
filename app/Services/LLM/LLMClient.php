<?php

namespace App\Services\LLM;

interface LLMClient
{
    /**
     * Generate a single assistant response.
     */
    public function chat(string $prompt, array $options = []): string;
}
