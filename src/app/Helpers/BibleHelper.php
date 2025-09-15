<?php

namespace App\Helpers;

class BibleHelper
{
    public static function normalizeEntry(string $s): string
    {
        // remove leading "17.", "17) ", "17.\t", etc.
        $s = preg_replace('/^\s*\d+\s*[\.\)]\s*/u', '', $s);
        // collapse multiple spaces
        $s = preg_replace('/\s+/u', ' ', $s);

        // trim weird dashes or spaces
        return trim($s, " \t\n\r\0\x0B");
    }
}
