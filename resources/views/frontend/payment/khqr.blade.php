{{-- resources/views/frontend/payment/khqr.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Pay with KHQR')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-2xl mx-auto px-6 text-center">

        @if($order->status === 'paid')
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full mb-6">
                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="text-4xl font-black mb-4">Payment Received!</h1>

                <a href="{{ route('order.success', $order->id) }}"
                   class="inline-block mt-6 px-8 py-4 bg-black text-white rounded-xl">
                    View Order Details
                </a>
            </div>

        @else
            <h1 class="text-4xl font-black mb-8">Scan to Pay with Bakong</h1>

            <div class="bg-white rounded-3xl shadow-xl p-10 mb-8">
                <img src="data:image/png;base64,{{ $qrImage }}"
                     alt="KHQR" class="mx-auto mb-8 max-w-xs">

                <div class="text-2xl font-bold mb-2">
                    {{ number_format($order->total) }} ៛
                </div>

                <p class="text-gray-600 mb-6">
                    Order #{{ $order->order_number ?? $order->id }}
                </p>

                <div class="text-sm text-gray-500 space-y-1">
                    <p>Expires in <span id="timer">15:00</span></p>
                    <p>Open your Bakong app and scan the QR code above</p>
                </div>
            </div>

            <button onclick="manualCheck()"
                    class="px-8 py-4 bg-black text-white rounded-xl hover:bg-gray-800">
                I Have Paid
            </button>

            <p class="mt-6 text-sm text-gray-500">
                Page will auto-check every 10 seconds
            </p>
        @endif

    </div>
</div>

@if($order->status !== 'paid')
<script>
// Countdown Timer
let countdown = 15 * 60;

setInterval(() => {
    countdown--;
    let m = String(Math.floor(countdown / 60)).padStart(2, '0');
    let s = String(countdown % 60).padStart(2, '0');
    document.getElementById('timer').textContent = `${m}:${s}`;
}, 1000);

// Single, clean checkPayment function
function checkPayment() {
    fetch("{{ route('payment.check', $order->id) }}")
        .then(r => r.json())
        .then(res => {
            if (res.paid === true) {
                location.reload();
            }
        })
        .catch(() => {}); // ignore network hiccups
}

// Manual button
function manualCheck() {
    checkPayment();
}

// Auto poll every 10 seconds
setInterval(checkPayment, 10000);

// Initial call
checkPayment();
</script>
@endif

@endsection
