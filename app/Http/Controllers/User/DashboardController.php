<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\ShortLink;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $plan  = $user->currentPlan();
        $sub   = $user->activeSubscription;
        $links = $user->shortLinks()->latest()->paginate(10);

        $stats = [
            'total_links'  => $user->shortLinks()->count(),
            'active_links' => $user->shortLinks()->where('is_active', true)->where('expires_at', '>', now())->count(),
            'total_clicks' => $user->shortLinks()->sum('click_count'),
            'clicks_today' => 0,
        ];

        $ads = Advertisement::forPlacement('dashboard_top');

        return view('user.dashboard.index', compact('user', 'plan', 'sub', 'links', 'stats', 'ads'));
    }

    public function links(Request $request)
    {
        $links = auth()->user()->shortLinks()
            ->when($request->search, fn($q, $s) => $q->where('original_url', 'like', "%$s%")->orWhere('short_code', 'like', "%$s%"))
            ->when($request->status === 'active',  fn($q) => $q->where('is_active', true)->where('expires_at', '>', now()))
            ->when($request->status === 'expired', fn($q) => $q->where('expires_at', '<=', now()))
            ->latest()
            ->paginate(15);

        return view('user.links.index', compact('links'));
    }

    public function deleteLink(ShortLink $shortLink)
    {
        if ($shortLink->user_id !== auth()->id()) {
            abort(403);
        }
        $shortLink->delete();
        return back()->with('success', 'Link deleted.');
    }
}
