<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortLink extends Model
{
    protected $fillable = [
        'user_id', 'original_url', 'short_code', 'custom_alias',
        'title', 'expires_at', 'is_active', 'password',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clicks()
    {
        return $this->hasMany(LinkClick::class, 'short_link_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function shortUrl(): string
    {
        $code = $this->custom_alias ?? $this->short_code;
        return config('app.url') . '/' . $code;
    }
}
