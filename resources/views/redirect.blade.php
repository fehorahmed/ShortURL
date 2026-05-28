@extends('layouts.app')
@section('title', 'Redirecting...')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 px-4 py-12">

    {{-- Ad above redirect box --}}
    @foreach($ads as $ad)
        <div class="w-full max-w-2xl mb-6 text-center">{!! $ad->ad_code !!}</div>
    @endforeach

    <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md w-full text-center">
        <div class="relative w-24 h-24 mx-auto mb-6">
            <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e5e7eb" stroke-width="2.5"/>
                <circle id="progress-ring" cx="18" cy="18" r="15.9" fill="none" stroke="#4f46e5"
                    stroke-width="2.5" stroke-linecap="round"
                    stroke-dasharray="100 100" stroke-dashoffset="0"
                    style="transition: stroke-dashoffset 1s linear;"/>
            </svg>
            <span id="timer-count" class="absolute inset-0 flex items-center justify-center text-3xl font-bold text-indigo-600">
                {{ $waitSeconds }}
            </span>
        </div>

        <p class="text-gray-500 text-sm mb-2">You are being redirected to</p>
        <p class="text-indigo-600 font-medium text-sm break-all mb-6 px-2">
            {{ Str::limit($link->original_url, 70) }}
        </p>

        <a href="{{ $link->original_url }}"
           class="inline-block bg-indigo-600 text-white px-8 py-2.5 rounded-lg hover:bg-indigo-700 font-medium text-sm">
            Go Now →
        </a>

        <p class="text-gray-400 text-xs mt-4">Automatic redirect in <span id="countdown-text">{{ $waitSeconds }}</span> seconds</p>
    </div>

    {{-- Ad below redirect box --}}
    @foreach($ads->skip(1) as $ad)
        <div class="w-full max-w-2xl mt-6 text-center">{!! $ad->ad_code !!}</div>
    @endforeach
</div>

@push('scripts')
<script>
    const totalSeconds = {{ $waitSeconds }};
    const targetUrl    = @json($link->original_url);
    const ring         = document.getElementById('progress-ring');
    const counter      = document.getElementById('timer-count');
    const countdownTxt = document.getElementById('countdown-text');
    let   seconds      = totalSeconds;

    const interval = setInterval(() => {
        seconds--;
        const progress = ((totalSeconds - seconds) / totalSeconds) * 100;
        ring.style.strokeDashoffset = progress;
        counter.textContent      = seconds;
        countdownTxt.textContent = seconds;

        if (seconds <= 0) {
            clearInterval(interval);
            window.location.href = targetUrl;
        }
    }, 1000);
</script>
@endpush
@endsection
