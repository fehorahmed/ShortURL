<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkClick extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'short_link_id', 'ip_address', 'country', 'city',
        'device', 'browser', 'os', 'referrer', 'clicked_at',
    ];

    protected $casts = ['clicked_at' => 'datetime'];

    public function shortLink()
    {
        return $this->belongsTo(ShortLink::class);
    }
}
