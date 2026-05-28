<?php

namespace App\Console\Commands;

use App\Models\ShortLink;
use Illuminate\Console\Command;

class CleanExpiredLinks extends Command
{
    protected $signature   = 'links:clean-expired';
    protected $description = 'Deactivate expired short links';

    public function handle(): void
    {
        $count = ShortLink::where('expires_at', '<=', now())
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $this->info("Deactivated {$count} expired links.");
    }
}
