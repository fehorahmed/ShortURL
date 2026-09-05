@extends('layouts.app')
@section('title', 'ShortURL — Shorten, Share & Track Links')

@section('content')

    {{-- Hero --}}
    <section class="bg-gradient-to-br from-indigo-600 to-purple-700 text-white py-20 px-4">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Shorten. Share. Track.</h1>
            <p class="text-indigo-100 text-lg mb-10">Create short links instantly — free for everyone, no account needed.</p>

            <div class="bg-white rounded-2xl p-6 shadow-2xl">
                @if (session('short_url'))
                    <div
                        class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between gap-3">
                        <a href="{{ session('short_url') }}" target="_blank"
                            class="text-green-800 font-medium text-sm truncate">{{ session('short_url') }}</a>
                        <button
                            onclick="navigator.clipboard.writeText('{{ session('short_url') }}').then(() => this.textContent = 'Copied!')"
                            class="shrink-0 text-xs bg-green-600 text-white px-3 py-1.5 rounded-md hover:bg-green-700">Copy</button>
                    </div>
                @endif

                <form action="{{ route('links.shorten') }}" method="POST">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="url" name="url" placeholder="Paste your long URL here..." required
                            value="{{ old('url') }}"
                            class="flex-1 border border-gray-300 rounded-lg px-4 py-3 text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="submit"
                            class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-semibold text-sm whitespace-nowrap">
                            Shorten URL
                        </button>
                    </div>

                    @auth
                        @if (auth()->user()->currentPlan()?->custom_alias)
                            <div class="mt-3 flex items-center gap-2">
                                <span class="text-gray-400 text-sm shrink-0">{{ config('app.url') }}/</span>
                                <input type="text" name="custom_alias" placeholder="custom-alias (optional)"
                                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 focus:ring-2 focus:ring-indigo-400 focus:outline-none text-gray-900">
                            </div>
                        @endif
                    @endauth

                    @if ($errors->any())
                        <p class="text-red-500 text-sm mt-2 text-left">{{ $errors->first() }}</p>
                    @endif

                    @guest
                        <p class="text-gray-400 text-xs mt-3">
                            No account needed. Links expire in 30 days.
                            <a href="{{ route('register') }}" class="text-indigo-600 hover:underline">Sign up for longer links
                                →</a>
                        </p>
                    @endguest
                </form>
            </div>
        </div>
    </section>

    {{-- Homepage ads --}}
    {{-- @foreach ($homeAds as $ad)
    <div class="text-center py-4 bg-gray-100">{!! $ad->ad_code !!}</div>
@endforeach --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-3">
        @foreach ($homeAds as $ad)
            <div>
                {!! $ad->ad_code !!}
            </div>
        @endforeach
    </div>

    {{-- Features --}}
    <section class="py-16 px-4 bg-white">
        <div class="max-w-5xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-8 text-center">
            @foreach ([['icon' => '⚡', 'title' => 'Instant Shortening', 'desc' => 'Create short links in seconds, no sign-up required.'], ['icon' => '📊', 'title' => 'Click Analytics', 'desc' => 'Track clicks, devices, and referrers with Pro plans.'], ['icon' => '🔒', 'title' => 'Secure & Reliable', 'desc' => 'Links are served fast and expire only when you want.']] as $f)
                <div class="p-6">
                    <div class="text-4xl mb-3">{{ $f['icon'] }}</div>
                    <h3 class="font-bold text-lg mb-2">{{ $f['title'] }}</h3>
                    <p class="text-gray-500 text-sm">{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Plans --}}
    <section class="py-16 px-4 bg-gray-50">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-2">Simple Pricing</h2>
            <p class="text-center text-gray-500 mb-10">Upgrade to unlock more links, longer expiry, and remove ads.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ min($plans->count(), 4) }} gap-6">
                @foreach ($plans as $plan)
                    <div
                        class="border-2 {{ !$plan->isFree() && $loop->index === 2 ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 bg-white' }} rounded-2xl p-6 text-center relative">
                        @if (!$plan->isFree() && $loop->index === 2)
                            <span
                                class="absolute -top-3 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-xs px-3 py-1 rounded-full">Most
                                Popular</span>
                        @endif
                        <h3 class="text-xl font-bold mb-1">{{ $plan->name }}</h3>
                        <p class="text-3xl font-extrabold text-indigo-600 mb-1">{{ $plan->formattedPrice() }}</p>
                        <p class="text-gray-400 text-sm mb-5">
                            {{ $plan->isFree() ? 'Forever free' : 'per ' . $plan->duration_days . ' days' }}</p>
                        <ul class="text-sm text-gray-700 space-y-2 mb-6 text-left">
                            <li>✅ {{ $plan->max_links == -1 ? 'Unlimited' : $plan->max_links }} links</li>
                            <li>✅ {{ $plan->link_expiry_days }}-day link expiry</li>
                            <li>{{ $plan->show_ads ? '❌ Ads on redirect' : '✅ No ads' }}</li>
                            <li>{{ $plan->custom_alias ? '✅' : '❌' }} Custom aliases</li>
                            <li>{{ $plan->analytics ? '✅' : '❌' }} Click analytics</li>
                        </ul>
                        @if ($plan->isFree())
                            <a href="{{ route('register') }}"
                                class="block border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 text-sm">Get
                                Started Free</a>
                        @else
                            @auth
                                <form action="{{ route('user.plans.purchase', $plan) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-indigo-600 text-white py-2.5 rounded-lg hover:bg-indigo-700 font-medium text-sm">
                                        Get {{ $plan->name }} — {{ $plan->formattedPrice() }}
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('register') }}"
                                    class="block bg-indigo-600 text-white py-2.5 rounded-lg hover:bg-indigo-700 font-medium text-sm text-center">
                                    Sign Up & Buy
                                </a>
                            @endauth
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

@endsection
