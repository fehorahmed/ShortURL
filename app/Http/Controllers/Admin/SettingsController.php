<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'             => 'required|string|max:100',
            'site_description'      => 'nullable|string|max:255',
            'default_link_expiry'   => 'required|integer|min:1',
            'redirect_wait_seconds' => 'required|integer|min:0|max:30',
        ]);

        foreach ($request->except(['_token']) as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Settings saved.');
    }
}
