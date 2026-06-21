<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with(['activeSubscription.plan'])
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%"))
            ->when($request->role, fn($q, $r) => $q->where('role', $r))
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['shortLinks' => fn($q) => $q->latest()->take(10), 'subscriptions.plan', 'payments.plan']);
        $plans = Plan::where('is_active', true)->get();
        return view('admin.users.show', compact('user', 'plans'));
    }

    public function toggleActive(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot suspend an admin account.');
        }
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'User status updated.');
    }

    public function assignPlan(Request $request, User $user)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'days'    => 'required|integer|min:1',
        ]);
        // dd($request);

        $user->subscriptions()->where('status', 'active')->update(['status' => 'cancelled']);

        Subscription::create([
            'user_id'    => $user->id,
            'plan_id'    => $request->plan_id,
            'starts_at'  => now(),
            'expires_at' => now()->addDays($request->integer('days')),
            'status'     => 'active',
        ]);

        return back()->with('success', 'Plan assigned successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot delete admin.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
