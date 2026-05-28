<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Plan;

class HomeController extends Controller
{
    public function index()
    {
        $plans   = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $homeAds = Advertisement::forPlacement('homepage');

        return view('home', compact('plans', 'homeAds'));
    }
}
