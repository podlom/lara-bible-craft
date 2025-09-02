<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $fillable = [
        'bibliography_id',
        'type',
        'authors',
        'title',
        'subtitle',
        'responsibility',
        'type_note',
        'publisher_city',
        'publisher_name',
        'year',
        'pages',
        'url',
        'accessed_at',
        'formatted_entry',
        'order_in_list',
        'global_index',
        'chapter_index',
        'chapter_name',
    ];

    protected $casts = [
        'authors' => 'array',
        'accessed_at' => 'date',
    ];

    public function bibliography()
    {
        return $this->belongsTo(Bibliography::class);
    }
}
