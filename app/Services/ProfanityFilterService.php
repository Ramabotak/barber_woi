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
        'jembut', 'silit', 'kadal', 'babi', 'setan',
    ];

    /**
     * Sensor kata kasar dalam teks: huruf pertama & terakhir dipertahankan,
     * huruf di tengah diganti bintang. Contoh: "anjing" -> "a****g".
     * Pencarian pakai word boundary agar tidak menyensor kata yang cuma
     * mengandung substring kata terlarang (mis. "kelas" tidak kena walau
     * ada kombinasi huruf mirip).
     */
    public function censor(?string $text): ?string
    {
        if (!$text) {
            return $text;
        }

        foreach ($this->bannedWords as $word) {
            $pattern = '/\b(' . preg_quote($word, '/') . ')\b/iu';

            $text = preg_replace_callback($pattern, function ($matches) {
                $found = $matches[1];
                $length = mb_strlen($found);

                if ($length <= 2) {
                    return str_repeat('*', $length);
                }

                return mb_substr($found, 0, 1) . str_repeat('*', $length - 2) . mb_substr($found, -1);
            }, $text);
        }

        return $text;
    }

    public function containsProfanity(?string $text): bool
    {
        if (!$text) {
            return false;
        }

        foreach ($this->bannedWords as $word) {
            if (preg_match('/\b' . preg_quote($word, '/') . '\b/iu', $text)) {
                return true;
            }
        }

        return false;
    }
}
