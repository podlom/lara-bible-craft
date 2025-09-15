<?php

namespace App\Services;

use App\Enums\SourceTypeEnum;
use App\Helpers\BibleHelper;

class BiblioParserService
{
    public function parse(string $raw): array
    {
        // 1) Normalize the raw line
        $entry = BibleHelper::normalizeEntry($raw);

        $data = [
            'authors' => [],
            'title' => null,
            'subtitle' => null,
            'responsibility' => null,
            'publisher' => null,
            'year' => null,
            'type' => SourceTypeEnum::BOOK->value, // sensible default
            'place' => null,
        ];

        // 2) Extract title between asterisks. Examples:
        // "ENISA. *Threat Landscape 2023.* European Union Agency for Cybersecurity, 2023."
        // "NIST. *Framework for Improving Critical Infrastructure Cybersecurity. Version 2.0*. — Gaithersburg, MD: NIST, 2024."
        // NOTE: we allow trailing punctuation right after closing asterisk.
        if (preg_match('/\*([^*]+?)\*\s*[\.:\-—,]?/u', $entry, $m)) {
            $title = trim($m[1]);

            // Strip trailing period inside the italics if present (common in formatted lists)
            $title = preg_replace('/\.\s*$/u', '', $title);

            $data['title'] = $title;

            // Optional: split title/subtitle on colon IF you store subtitle separately
            if (mb_strpos($title, ':') !== false) {
                [$main, $sub] = array_map('trim', explode(':', $title, 2));
                if ($main !== '') {
                    $data['title'] = $main;
                }
                if (! empty($sub)) {
                    $data['subtitle'] = $sub;
                }
            }
        }

        // 3) Extract year (prefer the last 4-digit year in the line)
        if (preg_match('/(19|20)\d{2}(?!.*(19|20)\d{2})/u', $entry, $ym)) {
            $data['year'] = (int) $ym[0];
        }

        // 4) Very light publisher/place hints (optional and safe)
        //    e.g., "— Gaithersburg, MD: NIST, 2024." or "Pearson, 2023."
        //    We'll grab "NIST" or "Pearson" if they appear as "Place: Publisher, YEAR"
        if (preg_match('/[:]\s*([^,:]+)\s*,\s*(19|20)\d{2}\.?/u', $entry, $pm)) {
            $pub = trim($pm[1]);
            // avoid super-short accidental matches
            if (mb_strlen($pub) >= 3) {
                $data['publisher'] = $pub;
            }
        } elseif (preg_match('/\*\)[^*]*\s+([^,]+)\s*,\s*(19|20)\d{2}\.?/u', $entry)) {
            // defensive: ignore—rare broken pattern we don't want to match by accident
        } elseif (! $data['publisher'] && preg_match('/\*\s*([^,]+)\s*,\s*(19|20)\d{2}\.?/u', $entry, $pm2)) {
            // After the closing * (title), if we see "Publisher, YEAR"
            $pub = trim($pm2[1]);
            if (mb_strlen($pub) >= 3) {
                $data['publisher'] = $pub;
            }
        }

        // 5) 🌐 Тип джерела - TYPE DETECTION (keep/enhance your original rules if you already had them)
        $lower = mb_strtolower($entry, 'UTF-8');

        if (preg_match('/\b(standard|iso|nist|en\s?\d|din|dstu|дсту|стандарт)\b/u', $lower)) {
            $data['type'] = SourceTypeEnum::STANDARD->value;
        } elseif (preg_match('/\b(report|звіт|brief|review|огляд)\b/u', $lower)) {
            $data['type'] = SourceTypeEnum::REPORT->value;
        } elseif (preg_match('/\b(thesis|dissertation|дисертац|магістерськ|кваліфікац)\b/u', $lower)) {
            $data['type'] = SourceTypeEnum::THESIS->value;
        } elseif (preg_match('/\b(law|regulation|reglament|закон|норматив|gdpr)\b/u', $lower)) {
            $data['type'] = SourceTypeEnum::LAW->value;
        } elseif (preg_match('/\b(journal|magazine|періодич|статт)\b/u', $lower)) {
            $data['type'] = SourceTypeEnum::ARTICLE->value;
        } else {
            $data['type'] = SourceTypeEnum::BOOK->value;
        }

        // 6) (Optional) Authors heuristic:
        // Take the segment before the title and treat it as "author/org." if it ends with a period and
        // is not just an index number. That will handle "ENISA.", "Symantec (Broadcom).", "NIST." etc.
        if ($data['title'] && preg_match('/^(.*?)\*\s*'.preg_quote($data['title'], '/').'\*/u', $entry, $am)) {
            $pre = trim(preg_replace('/^\s*\d+\s*[\.\)]\s*/u', '', $am[1])); // remove any leading index again
            $pre = rtrim($pre, '.'); // drop trailing period
            if ($pre !== '') {
                // For now, store as a single "organization" string in authors[0].
                // If your schema expects array of people, this still fits (later you can refine).
                $data['authors'] = [$pre];
            }
        }

        return $data;
    }
}
