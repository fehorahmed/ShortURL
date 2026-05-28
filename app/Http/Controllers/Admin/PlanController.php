<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('sort_order')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'description'      => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'currency'         => 'required|string|max:10',
            'duration_days'    => 'required|integer|min:1',
            'max_links'        => 'required|integer|min:-1',
            'link_expiry_days' => 'required|integer|min:1',
            'sort_order'       => 'nullable|integer',
        ]);

        $data['show_ads']     = $request->boolean('show_ads');
        $data['custom_alias'] = $request->boolean('custom_alias');
        $data['analytics']    = $request->boolean('analytics');
        $data['is_active']    = true;

        Plan::create($data);
        return redirect()->route('admin.plans.index')->with('success', 'Plan created.');
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'description'      => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'currency'         => 'required|string|max:10',
            'duration_days'    => 'required|integer|min:1',
            'max_links'        => 'required|integer|min:-1',
            'link_expiry_days' => 'required|integer|min:1',
            'sort_order'       => 'nullable|integer',
        ]);

        $data['show_ads']     = $request->boolean('show_ads');
        $data['custom_alias'] = $request->boolean('custom_alias');
        $data['analytics']    = $request->boolean('analytics');
        $data['is_active']    = $request->boolean('is_active');

        $plan->update($data);
        return back()->with('success', 'Plan updated.');
    }

    public function destroy(Plan $plan)
    {
        if ($plan->subscriptions()->where('status', 'active')->exists()) {
            return back()->with('error', 'Cannot delete a plan with active subscribers.');
        }
        $plan->delete();
        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted.');
    }
}
