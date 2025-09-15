<?php

namespace App\Services;

use App\Enums\SourceTypeEnum;

class BiblioParserService
{
    public function parse(string $raw): array
    {
        $data = [
            'authors' => [],
            'title' => null,
            'year' => null,
            'publisher' => null,
            'type' => 'book', // дефолт
        ];

        // 1) Прибрати нумерацію "16." на початку
        $normalized = preg_replace('/^\s*\d+\s*[\.\)]\s*/u', '', $raw);

        // 2) Детектор стандартів (розширений список абревіатур)
        if (preg_match('/^\s*(ISO(?:\/IEC)?|IEC|NIST|RFC|IETF|ETSI|EN|BSI?|ДСТУ|DSTU|ГОСТ)\b/iu', $normalized))
        {
            $data['type'] = 'standard';

            // --- 2a) ВАРІАНТ З КОДОМ (ISO/IEC 27001:2018. *Title* Publisher, 2018.)
            if (preg_match(
                '/^(?P<code>(?:ISO(?:\/IEC)?|IEC|EN|BSI?|ДСТУ|DSTU|ГОСТ)[^\.]+)\.\s*' .                  // code до крапки
                '(?P<titleItalics>\*.*?\*)' .                                                           // курсивний заголовок
                '(?:\.\s*(?P<version>(?:Version|Rev\.?|Ed\.?|Edition)\s*[\w\.\-]+))?' .                 // опційна версія
                '(?:,\s*(?P<publisher>[^,\.]+))?' .                                                     // опційний видавець
                '(?:,\s*(?P<year>\d{4}))?' .                                                            // опційний рік
                '\.?/u',
                $normalized,
                $m1
            )) {
                $code   = trim($m1['code']);
                $titleI = trim($m1['titleItalics']);
                $title  = rtrim($code, '.') . '. ' . rtrim($titleI, '.');

                if (!empty($m1['version'])) {
                    $title .= ' ' . trim($m1['version']);
                }

                $data['title'] = $title;
                if (!empty($m1['publisher'])) $data['publisher'] = trim($m1['publisher']);
                if (!empty($m1['year']))      $data['year']      = (int) $m1['year'];

                return $data;
            }

            // --- 2b) ВАРІАНТ БЕЗ КОДУ (NIST. *Title* Version 1.1, Publisher, 2020.)
            if (preg_match(
                '/^(?P<org>(?:NIST|RFC|IETF|ETSI|BSI?|EN|ДСТУ|DSTU|ГОСТ))\.\s*' .                        // організація + крапка
                '(?P<titleItalics>\*.*?\*)' .                                                           // курсивний заголовок
                '(?:\s*(?P<version>(?:Version|Rev\.?|Ed\.?|Edition)\s*[\w\.\-]+))?' .                   // опційна версія
                '(?:,\s*(?P<publisher>[^,\.]+))?' .                                                     // опційний видавець
                '(?:,\s*(?P<year>\d{4}))?' .                                                            // опційний рік
                '\.?/u',
                $normalized,
                $m2
            )) {
                $org    = trim($m2['org']);
                $titleI = trim($m2['titleItalics']);

                // Склеюємо саме так, як ти хочеш бачити у title:
                // "NIST. *Framework …* Version 1.1"
                $title = $org . '. ' . rtrim($titleI, '.');
                if (!empty($m2['version'])) {
                    $title .= ' ' . trim($m2['version']);
                }

                $data['title'] = $title;
                if (!empty($m2['publisher'])) $data['publisher'] = trim($m2['publisher']);
                if (!empty($m2['year']))      $data['year']      = (int) $m2['year'];

                return $data;
            }

            // --- 2c) Фолбек для екзотики: якщо не впізнали структуру, не ламай імпорт
            // спробуємо взяти все до останнього ", YYYY" як title
            if (preg_match('/^(?<beforeYear>.+?),\s*\d{4}\.?$/u', $normalized, $mx)) {
                $data['title'] = rtrim(trim($mx['beforeYear']), '.');
            } else {
                // або цілком рядок (без кінцевої крапки)
                $data['title'] = rtrim($normalized, '.');
            }

            return $data;
        }

        // Витяг року
        if (preg_match('/\((\d{4})\)/', $raw, $matches)) {
            $data['year'] = $matches[1];
        }

        // Витяг авторів
        if (preg_match('/^(.+?)\s*\(\d{4}\)/u', $raw, $matches)) {
            $authorBlock = trim($matches[1]);
            $authorParts = preg_split('/,\s*|&| і | та | and /u', $authorBlock);
            $authors = [];

            foreach ($authorParts as $part) {
                $part = trim($part);
                if ($part === '') {
                    continue;
                }

                if (preg_match('/^([A-Z][a-z]+)\s+([A-Z]\. ?[A-Z]?\.?)/u', $part, $m)) {
                    $authors[] = ['last_name' => $m[1], 'initials' => trim($m[2])];

                    continue;
                }

                if (preg_match('/^([А-ЯІЇЄҐ][а-яіїєґ’\'\-]+)\s+([А-ЯІЇЄҐ]\. ?[А-ЯІЇЄҐ]?\.?)/u', $part, $m)) {
                    $authors[] = ['last_name' => $m[1], 'initials' => trim($m[2])];

                    continue;
                }

                $authors[] = ['raw' => $part];
            }

            $data['authors'] = $authors;
        }

        // Назва
        if (preg_match('/\(\d{4}\)\.?\s*(.+)$/u', $raw, $matches)) {
            $data['title'] = trim($matches[1]);
        }

        // 🌐 Тип джерела (евристики)
        $lower = mb_strtolower($raw);

        if (preg_match('/(iso|iec|dstu|rfc|nist|gost|standard|стандарт)/i', $raw)) {
            $data['type'] = SourceTypeEnum::STANDARD->value; // 'standard';
        } elseif (preg_match('/(https?:\/\/|www\.|doi\.org)/i', $raw)) {
            $data['type'] = SourceTypeEnum::WEB->value; // 'web'; // website
        } elseif (preg_match('/(journal|журнал|том|випуск|volume|issue|no\.)/i', $raw)) {
            $data['type'] = SourceTypeEnum::ARTICLE->value; // 'article';
        } elseif (preg_match('/(report|звіт|brief|review|огляд)/i', $raw)) {
            $data['type'] = SourceTypeEnum::REPORT->value; // 'report';
        } elseif (preg_match('/(gdpr|law|regulation|reglament|закон|норматив)/i', $raw)) {
            $data['type'] = SourceTypeEnum::LAW->value; // 'law';
        } elseif (preg_match('/(thesis|dissertation|дисертація|магістерська|кваліфікаційна)/i', $raw)) {
            $data['type'] = SourceTypeEnum::THESIS->value; // 'thesis';
        } else {
            $data['type'] = SourceTypeEnum::BOOK->value; // 'book';
        }

        return $data;
    }
}
