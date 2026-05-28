<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\ShortLink;
use App\Services\LinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShortLinkController extends Controller
{
    public function __construct(private LinkService $linkService) {}

    public function store(Request $request)
    {
        $request->validate([
            'url'          => 'required|url|max:2048',
            'custom_alias' => 'nullable|alpha_dash|min:3|max:30|unique:short_links,custom_alias|unique:short_links,short_code',
        ]);

        $user = auth()->user();

        if ($user && !$user->canCreateLink()) {
            return back()->with('error', 'You have reached your plan\'s link limit. Please upgrade.');
        }

        $expiryDays = $user ? $user->linkExpiryDays() : 30;
        $plan       = $user?->currentPlan();

        $alias = null;
        if ($request->filled('custom_alias') && $plan?->custom_alias) {
            $alias = $request->custom_alias;
        }

        $link = ShortLink::create([
            'user_id'      => $user?->id,
            'original_url' => $request->url,
            'short_code'   => $this->linkService->generateCode(),
            'custom_alias' => $alias,
            'title'        => $request->title,
            'expires_at'   => now()->addDays($expiryDays),
        ]);

        return back()->with([
            'success'   => 'Link created successfully!',
            'short_url' => $link->shortUrl(),
        ]);
    }

    public function redirect(Request $request, string $code)
    {
        $link = ShortLink::where(function ($q) use ($code) {
            $q->where('short_code', $code)->orWhere('custom_alias', $code);
        })->where('is_active', true)->first();

        if (!$link) {
            abort(404, 'This link does not exist.');
        }

        if ($link->isExpired()) {
            abort(410, 'This link has expired.');
        }

        $this->linkService->recordClick($link, $request);

        $plan = $link->user?->currentPlan();

        if (!$plan || $plan->show_ads) {
            $ads           = Advertisement::forPlacement('redirect_page');
            $waitSeconds   = 5;
            return view('redirect', compact('link', 'ads', 'waitSeconds'));
        }

        return redirect()->away($link->original_url);
    }
}
