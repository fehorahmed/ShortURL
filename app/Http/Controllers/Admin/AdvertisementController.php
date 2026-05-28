<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    public function index()
    {
        $ads = Advertisement::orderBy('placement')->orderBy('sort_order')->get();
        return view('admin.ads.index', compact('ads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'type'      => 'required|in:adsterra,google_adsense,custom_html,image_banner',
            'placement' => 'required|in:redirect_page,dashboard_top,dashboard_side,homepage',
            'ad_code'   => 'nullable|string',
            'image_url' => 'nullable|url',
            'link_url'  => 'nullable|url',
            'sort_order'=> 'nullable|integer',
        ]);

        Advertisement::create([
            'name'       => $request->name,
            'type'       => $request->type,
            'placement'  => $request->placement,
            'ad_code'    => $request->ad_code,
            'image_url'  => $request->image_url,
            'link_url'   => $request->link_url,
            'sort_order' => $request->sort_order ?? 0,
            'is_active'  => true,
        ]);

        return redirect()->route('admin.ads.index')->with('success', 'Advertisement created.');
    }

    public function update(Request $request, Advertisement $advertisement)
    {
        $advertisement->update([
            'name'       => $request->name,
            'type'       => $request->type,
            'placement'  => $request->placement,
            'ad_code'    => $request->ad_code,
            'image_url'  => $request->image_url,
            'link_url'   => $request->link_url,
            'sort_order' => $request->sort_order ?? 0,
            'is_active'  => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Advertisement updated.');
    }

    public function toggle(Advertisement $advertisement)
    {
        $advertisement->update(['is_active' => !$advertisement->is_active]);
        return back()->with('success', 'Ad ' . ($advertisement->is_active ? 'enabled' : 'disabled') . '.');
    }

    public function destroy(Advertisement $advertisement)
    {
        $advertisement->delete();
        return redirect()->route('admin.ads.index')->with('success', 'Advertisement deleted.');
    }
}
