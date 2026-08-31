<?php

namespace App\Services;

class ProfanityFilterService
{
    // Daftar kata kasar/tidak pantas bahasa Indonesia yang umum.
    // Bisa ditambah sewaktu-waktu sesuai kebutuhan.
    protected array $bannedWords = [
        'anjing', 'anjay', 'bangsat', 'bajingan', 'kontol', 'memek', 'ngentot',
        'goblok', 'tolol', 'idiot', 'tai', 'taik', 'sialan', 'brengsek',
        'jancok', 'jancuk', 'asu', 'kampret', 'bego', 'pantek', 'pukimak',
        'keparat', 'biadab', 'kunyuk', 'pepek', 'peler', 'kimak', 'lonte',
        'pelacur', 'bangke', 'cok', 'diancuk', 'kntl', 'ngewe',
        'jembut', 'silit', 'kadal', 'babi', 'setan','anj', 'bodoh', 'bitch', 'fuck', 'shit', 'asshole', 'bastard', 'dick', 'pussy', 'slut',
    ];

    protected ?string $censorPattern = null;
    protected ?string $detectPattern = null;

    public function censor(?string $text): ?string
    {
        if (!$text) {
            return $text;
        }

        $pattern = $this->getCensorPattern();

        return preg_replace_callback($pattern, function ($matches) {
            $found = $matches[1];
            $length = mb_strlen($found);

            if ($length <= 2) {
                return str_repeat('*', $length);
            }

            return mb_substr($found, 0, 1) . str_repeat('*', $length - 2) . mb_substr($found, -1);
        }, $text);
    }

    public function containsProfanity(?string $text): bool
    {
        if (!$text) {
            return false;
        }

        $pattern = $this->getDetectPattern();
        return (bool) preg_match($pattern, $text);
    }

    /**
     * Build single regex pattern untuk censor (dengan capture group).
     * Dibangun sekali dan di-cache untuk performance.
     */
    protected function getCensorPattern(): string
    {
        if ($this->censorPattern === null) {
            $escaped = array_map(fn($word) => preg_quote($word, '/'), $this->bannedWords);
            $this->censorPattern = '/\b(' . implode('|', $escaped) . ')\b/iu';
        }

        return $this->censorPattern;
    }

    /**
     * Build single regex pattern untuk detect.
     * Lebih efficient daripada loop dengan preg_match satu-satu.
     */
    protected function getDetectPattern(): string
    {
        if ($this->detectPattern === null) {
            $escaped = array_map(fn($word) => preg_quote($word, '/'), $this->bannedWords);
            $this->detectPattern = '/\b(?:' . implode('|', $escaped) . ')\b/iu';
        }

        return $this->detectPattern;
    }
}
