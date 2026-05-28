@extends('layouts.app')
@section('title', 'Plans & Pricing')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-center mb-2">Plans & Pricing</h1>
    <p class="text-center text-gray-500 mb-10">Upgrade anytime. No hidden fees.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ min($plans->count(), 4) }} gap-6">
        @foreach($plans as $plan)
        <div class="border-2 {{ $currentPlan?->id === $plan->id ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 bg-white' }} rounded-2xl p-6 text-center relative">
            @if($currentPlan?->id === $plan->id)
                <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-xs px-3 py-1 rounded-full">Current Plan</span>
            @endif
            <h3 class="text-xl font-bold mb-1">{{ $plan->name }}</h3>
            @if($plan->description)
                <p class="text-gray-400 text-xs mb-2">{{ $plan->description }}</p>
            @endif
            <p class="text-3xl font-extrabold text-indigo-600 mb-1">{{ $plan->formattedPrice() }}</p>
            <p class="text-gray-400 text-sm mb-6">{{ $plan->isFree() ? 'Forever free' : 'per '.$plan->duration_days.' days' }}</p>

            <ul class="text-sm text-gray-700 space-y-2 mb-6 text-left">
                <li class="flex items-center gap-2">
                    <span>✅</span>
                    <span>{{ $plan->max_links == -1 ? 'Unlimited' : $plan->max_links }} links</span>
                </li>
                <li class="flex items-center gap-2">
                    <span>✅</span>
                    <span>{{ $plan->link_expiry_days }}-day link expiry</span>
                </li>
                <li class="flex items-center gap-2">
                    <span>{{ $plan->show_ads ? '❌' : '✅' }}</span>
                    <span>{{ $plan->show_ads ? 'Ads on redirect page' : 'No ads on redirects' }}</span>
                </li>
                <li class="flex items-center gap-2">
                    <span>{{ $plan->custom_alias ? '✅' : '❌' }}</span>
                    <span>Custom short aliases</span>
                </li>
                <li class="flex items-center gap-2">
                    <span>{{ $plan->analytics ? '✅' : '❌' }}</span>
                    <span>Click analytics</span>
                </li>
            </ul>

            @if($currentPlan?->id === $plan->id)
                <span class="block w-full bg-indigo-100 text-indigo-700 py-2.5 rounded-lg text-sm font-medium">✅ Active Plan</span>
            @elseif($plan->isFree())
                <span class="block w-full border border-gray-200 text-gray-400 py-2.5 rounded-lg text-sm">Free Forever</span>
            @else
                <form action="{{ route('user.plans.purchase', $plan) }}" method="POST">
                    @csrf
                    <button class="w-full bg-indigo-600 text-white py-2.5 rounded-lg hover:bg-indigo-700 font-medium text-sm">
                        Get {{ $plan->name }} — {{ $plan->formattedPrice() }}
                    </button>
                </form>
            @endif
        </div>
        @endforeach
    </div>

    @if($sub)
    <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800 text-center">
        Your <strong>{{ $currentPlan->name }}</strong> plan expires on
        <strong>{{ $sub->expires_at->format('d M Y') }}</strong>
        · {{ $sub->daysRemaining() }} days remaining.
    </div>
    @endif
</div>
@endsection
