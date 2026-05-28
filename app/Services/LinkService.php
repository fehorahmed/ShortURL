<?php

namespace App\Services;

use App\Models\LinkClick;
use App\Models\ShortLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LinkService
{
    public function generateCode(int $length = 6): string
    {
        do {
            $code = Str::random($length);
        } while (ShortLink::where('short_code', $code)->exists());

        return $code;
    }

    public function recordClick(ShortLink $link, Request $request): void
    {
        $link->increment('click_count');

        $userAgent = $request->userAgent() ?? '';
        $device    = $this->detectDevice($userAgent);
        $browser   = $this->detectBrowser($userAgent);
        $os        = $this->detectOs($userAgent);

        LinkClick::create([
            'short_link_id' => $link->id,
            'ip_address'    => $request->ip(),
            'country'       => null,
            'device'        => $device,
            'browser'       => $browser,
            'os'            => $os,
            'referrer'      => $request->headers->get('referer'),
            'clicked_at'    => now(),
        ]);
    }

    private function detectDevice(string $ua): string
    {
        if (preg_match('/Mobile|Android|iPhone|iPad/i', $ua)) {
            return preg_match('/iPad/i', $ua) ? 'tablet' : 'mobile';
        }
        return 'desktop';
    }

    private function detectBrowser(string $ua): string
    {
        if (str_contains($ua, 'Chrome'))  return 'Chrome';
        if (str_contains($ua, 'Firefox')) return 'Firefox';
        if (str_contains($ua, 'Safari'))  return 'Safari';
        if (str_contains($ua, 'Edge'))    return 'Edge';
        return 'Other';
    }

    private function detectOs(string $ua): string
    {
        if (str_contains($ua, 'Windows')) return 'Windows';
        if (str_contains($ua, 'Mac'))     return 'macOS';
        if (str_contains($ua, 'Linux'))   return 'Linux';
        if (str_contains($ua, 'Android')) return 'Android';
        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) return 'iOS';
        return 'Other';
    }
}
