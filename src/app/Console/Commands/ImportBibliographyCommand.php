<?php

namespace App\Console\Commands;

use App\Models\Bibliography;
use App\Models\Source;
use App\Services\BiblioParserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportBibliographyCommand extends Command
{
    protected $signature = 'biblio:import {file} {--bibliography=1}';

    protected $description = 'Імпортувати бібліографічний список з текстового файлу';

    public function handle(): int
    {
        $path = $this->argument('file');
        $bibliographyId = (int) $this->option('bibliography');

        // 🔒 Перевірка існування файлу
        if (! File::exists($path)) {
            $this->error("❌ Файл не знайдено: $path");

            return 1;
        }

        // 🔒 Перевірка існування бібліографії
        $biblio = Bibliography::find($bibliographyId);
        if (! $biblio) {
            $this->error("❌ Бібліографія з ID={$bibliographyId} не знайдена.");

            return 1;
        }

        $contents = File::get($path);

        // 📚 Витяг записів з файлу
        preg_match_all('/^\d+\.\s+(.+?)(?=\n\d+\.|\z)/sm', $contents, $matches);
        $entries = $matches[1];

        $this->info('📖 Знайдено записів: '.count($entries));

        $parser = new BiblioParserService;

        $imported = 0;
        $failed = 0;

        foreach ($entries as $i => $entry) {
            $entry = trim($entry);
            if ($entry === '') {
                $this->warn('⚠️ Пропущено порожній запис під номером '.($i + 1));

                continue;
            }

            $parsed = $parser->parse($entry);

            try {
                if (empty($data['title'])) {
                    // Normalize: remove leading list index like "15." or "15)\t"
                    $normalized = preg_replace('/^\s*\d+\s*[\.\)]\s*/u', '', $entry);

                    // fallback for standards: use the whole line as title
                    if (($data['type'] ?? null) === 'standard') {
                        $data['title'] = trim($normalized, " \t\n\r\0\x0B.");
                    } else {
                        // or default to 'other' / skip with a warning
                        $data['title'] = 'Untitled';
                    }
                }

                Source::create([
                    'bibliography_id' => $bibliographyId,
                    'type' => $parsed['type'] ?? 'book',
                    'authors' => json_encode($parsed['authors']),
                    'title' => $parsed['title'] ?? null,
                    'year' => $parsed['year'] ?? null,
                    'formatted_entry' => $entry,
                    'order_in_list' => $i + 1,
                    'global_index' => $i + 1,
                ]);
                $imported++;
            } catch (\Throwable $e) {
                $this->warn('⚠️ Не вдалося імпортувати запис #'.($i + 1));
                $this->line("    > {$entry}");
                $this->line('    → Помилка: '.$e->getMessage());
                $failed++;
            }
        }

        $this->info('✅ Імпорт завершено.');
        $this->info("   ✔ Успішно: $imported");
        $this->info("   ❌ Пропущено: $failed");

        return 0;
    }
}
