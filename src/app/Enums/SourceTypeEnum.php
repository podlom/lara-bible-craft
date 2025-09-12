<?php

namespace App\Enums;

enum SourceTypeEnum: string
{
    case BOOK = 'book';
    case ARTICLE = 'article';
    case REPORT = 'report';
    case LAW = 'law';
    case THESIS = 'thesis';
    case WEB = 'web';
    case STANDARD = 'standard';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::BOOK => 'Book',
            self::ARTICLE => 'Article',
            self::REPORT => 'Report',
            self::LAW => 'Law',
            self::THESIS => 'Thesis',
            self::WEB => 'Web',
            self::STANDARD => 'Standard',
            self::OTHER => 'Other',
        };
    }

    public static function values(): array
    {
        return array_column(SourceTypeEnum::cases(), 'value');
    }
}
