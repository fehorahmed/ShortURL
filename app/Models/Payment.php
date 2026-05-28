<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'plan_id', 'subscription_id', 'amount', 'currency',
        'gateway', 'transaction_id', 'status', 'gateway_response',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'amount'           => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
