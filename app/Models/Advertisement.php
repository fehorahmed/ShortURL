<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = [
        'name', 'type', 'placement', 'ad_code',
        'image_url', 'link_url', 'is_active', 'sort_order',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public static function forPlacement(string $placement)
    {
        return static::where('placement', $placement)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}
