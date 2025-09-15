<?php

namespace App\Models;

use App\Enums\SourceTypeEnum;
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
        'user_id',
    ];

    protected $casts = [
        'authors' => 'array',
        'accessed_at' => 'date',
        'type' => SourceTypeEnum::class,
    ];

    public function bibliography()
    {
        return $this->belongsTo(Bibliography::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // sources.user_id → users.id
    }
}
