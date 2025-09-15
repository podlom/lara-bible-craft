<?php

namespace App\Console\Commands;

use App\Models\Bibliography;
use App\Models\Source;
use App\Services\BiblioParserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportBibliographyCommand extends Command
{
    private const IMPORT_USER_ID = 1;

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
                $data = $parsed;

                // Defensive fallback if title is still empty:
                if (empty($data['title'])) {
                    // Try again directly on the raw/normalized string
                    $normalized = preg_replace('/^\s*\d+\s*[\.\)]\s*/u', '', $entryNormalized ?? $entry);

                    if (preg_match('/\*([^*]+?)\*\s*[\.:\-—,]?/u', $normalized, $m)) {
                        $title = trim($m[1]);
                        $title = preg_replace('/\.\s*$/u', '', $title);
                        $data['title'] = $title;

                        if (mb_strpos($title, ':') !== false && empty($data['subtitle'])) {
                            [$main, $sub] = array_map('trim', explode(':', $title, 2));
                            if ($main !== '') {
                                $data['title'] = $main;
                            }
                            if (! empty($sub)) {
                                $data['subtitle'] = $sub;
                            }
                        }
                    }
                }

                // If STILL empty, last resort: use the full line (avoids DB failure, and you can fix later in UI)
                if (empty($data['title'])) {
                    $data['title'] = trim($normalized ?? $entry);
                }

                Source::create([
                    'bibliography_id' => $bibliographyId,
                    'type' => $parsed['type'] ?? 'book',
                    'authors' => json_encode($parsed['authors']),
                    'title' => $data['title'],
                    'subtitle' => $data['subtitle'] ?? null,
                    'year' => $parsed['year'] ?? date('Y'),
                    'formatted_entry' => $entry,
                    'order_in_list' => $i + 1,
                    'global_index' => $i + 1,
                    'user_id' => self::IMPORT_USER_ID,
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
