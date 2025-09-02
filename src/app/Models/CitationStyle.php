<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitationStyle extends Model
{
    protected $fillable = [
        'name',       // Наприклад: ДСТУ 8302:2015, APA, MLA
        'slug',       // Наприклад: dstu, apa, mla
        'config',     // JSON з шаблонами форматування
    ];

    protected $casts = [
        'config' => 'array',
    ];

    public function bibliographies()
    {
        return $this->hasMany(Bibliography::class, 'citation_style', 'slug');
    }
}
