{{-- resources/views/products/checkout.blade.php --}}
@extends('layouts.app')

@section('title', 'Checkout - ' . $product->name)

@section('content')
<div class="min-h-screen bg-gray-100 py-12>
    <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-2xl p-10 text-center">

        <h1 class="text-4xl font-black mb-6">Scan to Pay</h1>

        <div class="mb-8">
            <img src="data:image/png;base64,{{ $qrImage }}" alt="KHQR" class="mx-auto border-8 border-gray-300 rounded-2xl">
        </div>

        <div class="text-3xl font-bold text-green-600 mb-4">
            {{ number_format($amount) }} ៛
        </div>

        <p class="text-xl font-semibold mb-2">{{ $product->name }}</p>
        <p class="text-gray-600 mb-8">Open Bakong app → Scan QR code</p>

        <div class="text-sm text-gray-500 mb-6">
            <p>Payment expires in <span id="timer">15:00</span></p>
        </div>

        <button onclick="checkPayment()"
                class="px-8 py-4 bg-black text-white rounded-xl hover:bg-gray-800 mb-4">
            I Have Paid
        </button>

        <p class="text-xs text-gray-500">Page auto-checks every 8 seconds...</p>
    </div>
</div>

<script>
let countdown = 900; // 15 minutes
setInterval(() => {
    countdown--;
    let m = String(Math.floor(countdown / 60)).padStart(2, '0');
    let s = String(countdown % 60).padStart(2, '0');
    document.getElementById('timer').textContent = m + ':' + s;
    if (countdown <= 0) location.reload();
}, 1000);

function checkPayment() {
    fetch("{{ route('payment.check', $md5) }}")
        .then(r => r.json())
        .then(data => {
            if (data.paid) {
                alert("Payment Successful! Thank you!");
                window.location.href = "{{ route('home') }}";
            }
        });
}

// Check every 8 seconds
setInterval(checkPayment, 8000);
checkPayment(); // first check
</script>
@endsection
