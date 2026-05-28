<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'description', 'price', 'currency', 'duration_days',
        'max_links', 'link_expiry_days', 'show_ads', 'custom_alias',
        'analytics', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'show_ads'     => 'boolean',
        'custom_alias' => 'boolean',
        'analytics'    => 'boolean',
        'is_active'    => 'boolean',
        'price'        => 'decimal:2',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function isFree(): bool
    {
        return $this->price == 0;
    }

    public function formattedPrice(): string
    {
        return $this->price == 0
            ? 'Free'
            : $this->currency . ' ' . number_format($this->price, 0);
    }
}
