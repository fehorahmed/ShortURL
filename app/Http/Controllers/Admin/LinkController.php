<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShortLink;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index(Request $request)
    {
        $links = ShortLink::with('user')
            ->when($request->search, fn($q, $s) => $q->where('short_code', 'like', "%$s%")->orWhere('original_url', 'like', "%$s%"))
            ->when($request->status === 'expired', fn($q) => $q->where('expires_at', '<=', now()))
            ->when($request->status === 'active',  fn($q) => $q->where('is_active', true)->where('expires_at', '>', now()))
            ->latest()
            ->paginate(20);

        return view('admin.links.index', compact('links'));
    }

    public function destroy(ShortLink $shortLink)
    {
        $shortLink->delete();
        return back()->with('success', 'Link deleted.');
    }
}
