<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function shortLinks()
    {
        return $this->hasMany(ShortLink::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latestOfMany();
    }

    public function currentPlan(): ?Plan
    {
        $sub = $this->activeSubscription;
        if ($sub) {
            return $sub->plan;
        }
        return Plan::where('price', 0)->orderBy('sort_order')->first();
    }

    public function canCreateLink(): bool
    {
        $plan = $this->currentPlan();
        if (!$plan) return false;
        if ($plan->max_links === -1) return true;

        return $this->shortLinks()
            ->where('expires_at', '>', now())
            ->count() < $plan->max_links;
    }

    public function linkExpiryDays(): int
    {
        return $this->currentPlan()?->link_expiry_days ?? 30;
    }
}
