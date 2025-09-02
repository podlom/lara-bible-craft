<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bibliography extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'citation_style',
        'language',
    ];

    /**
     * Автоматичне приведення типів.
     */
    protected $casts = [
        'user_id' => 'integer',
    ];

    /**
     * Власник списку літератури.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Джерела, що належать до цього списку.
     */
    public function sources(): HasMany
    {
        return $this->hasMany(Source::class);
    }

    /**
     * Стиль оформлення (зв’язок через slug).
     */
    public function style(): BelongsTo
    {
        return $this->belongsTo(CitationStyle::class, 'citation_style', 'slug');
    }
}
