<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Str;

class NlpService
{
    public function detectLanguage(string $text): string
    {
        // Simple heuristic; replace with external service if needed
        $hasNonAscii = (bool) preg_match('/[^\x00-\x7F]/', $text);
        if ($hasNonAscii) {
            return 'auto';
        }
        // Basic detection for Spanish/French keywords as example
        $lower = Str::lower($text);
        if (str_contains($lower, 'hola') || str_contains($lower, 'gracias')) return 'es';
        if (str_contains($lower, 'bonjour') || str_contains($lower, 'merci')) return 'fr';
        return 'en';
    }

    public function translate(string $text, string $targetLocale): string
    {
        // Placeholder: in production integrate with translation API
        return $text;
    }
}


