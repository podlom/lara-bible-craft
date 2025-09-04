<?php

namespace App\Services;

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
            $data['type'] = 'standard';
        } elseif (preg_match('/(https?:\/\/|www\.|doi\.org)/i', $raw)) {
            $data['type'] = 'website';
        } elseif (preg_match('/(journal|журнал|том|випуск|volume|issue|no\.)/i', $raw)) {
            $data['type'] = 'article';
        } elseif (preg_match('/(report|звіт|brief|review|огляд)/i', $raw)) {
            $data['type'] = 'report';
        } elseif (preg_match('/(gdpr|law|regulation|reglament|закон|норматив)/i', $raw)) {
            $data['type'] = 'law';
        } elseif (preg_match('/(thesis|dissertation|дисертація|магістерська|кваліфікаційна)/i', $raw)) {
            $data['type'] = 'thesis';
        } else {
            $data['type'] = 'book';
        }

        return $data;
    }
}
