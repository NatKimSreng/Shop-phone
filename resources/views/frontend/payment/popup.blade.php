<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pay with KHQR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black bg-opacity-70 min-h-screen flex items-center justify-center p-4">

<div class="bg-white rounded-3xl shadow-2xl p-8 max-w-sm w-full text-center">

    <h1 class="text-4xl font-black mb-8">Scan to Pay</h1>

    <!-- QR ALWAYS APPEARS NOW -->
    <div class="mb-8 flex items-center justify-center">
        <div class="w-80 h-80 mx-auto border-8 border-gray-300 rounded-3xl shadow-lg overflow-hidden">
            {!! $qrSvg !!}
        </div>
    </div>

    <div class="text-4xl font-black text-green-600 mb-4">
        {{ number_format($amount) }} ៛
    </div>
    <p class="text-xl font-bold mb-6">{{ $product->name }}</p>

    <div class="flex justify-center mb-4">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-black"></div>
    </div>

    <p class="text-sm text-gray-600">Open Bakong app and scan... <span id="timer">15:00</span></p>
</div>

<script>
const md5 = "{{ $md5 }}";
let seconds = 900;

setInterval(() => {
    seconds--;
    const m = String(Math.floor(seconds / 60)).padStart(2, '0');
    const s = String(seconds % 60).padStart(2, '0');
    document.getElementById('timer').textContent = m + ':' + s;
    if (seconds <= 0) window.close();
}, 1000);

function checkPayment() {
    fetch(`/payment/check/${md5}`)
        .then(r => r.json())
        .then(data => {
            if (data.paid) {
                document.body.innerHTML = `
                    <div class="flex flex-col items-center justify-center min-h-screen bg-green-600 text-white p-8">
                        <svg class="w-24 h-24 mb-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <h1 class="text-5xl font-black mb-6">Payment Success!</h1>
                        <p class="text-xl mb-8">Thank you for your purchase!</p>
                        <button onclick="window.close()" class="bg-white text-green-600 px-12 py-4 rounded-2xl font-bold text-lg hover:bg-gray-100">
                            Close & Continue Shopping
                        </button>
                    </div>
                `;
            }
        })
        .catch(err => console.log('Check error:', err));
}

// Auto-check every 5 seconds
setInterval(checkPayment, 5000);
checkPayment(); // First check
</script>

</body>
</html>
