{{-- resources/views/frontend/checkout/index.blade.php --}}
@extends('layouts.front')

@section('title', 'Checkout')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="mb-8 text-sm">
            <ol class="flex items-center space-x-2 text-gray-600">
                <li><a href="{{ route('home') }}" class="hover:text-black transition">Home</a></li>
                <li>/</li>
                <li><a href="{{ route('cart.index') }}" class="hover:text-black transition">Cart</a></li>
                <li>/</li>
                <li class="text-black font-medium">Checkout</li>
            </ol>
        </nav>

        <h1 class="text-4xl md:text-5xl font-black mb-8 text-center">Checkout</h1>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl">
                <h3 class="font-bold mb-2">Please fix the following errors:</h3>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-8">

            {{-- Order Summary --}}
            <div class="lg:col-span-1 order-2 lg:order-1">
                <div class="bg-white rounded-3xl shadow-lg p-6 sticky top-6">
                    <h2 class="text-2xl font-black mb-6">Order Summary</h2>

                    <div class="space-y-4 mb-6">
                        @foreach($cart as $productId => $item)
                            <div class="flex gap-4 pb-4 border-b border-gray-200">
                                @if($item['image'] ?? null)
                                    <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="w-20 h-20 object-cover rounded-lg">
                                @else
                                    <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <span class="text-xs text-gray-400">No Image</span>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h3 class="font-bold text-sm mb-1">{{ $item['name'] }}</h3>
                                    <p class="text-sm text-gray-500">Qty: {{ $item['qty'] }}</p>
                                    <p class="text-sm font-semibold mt-1">${{ number_format($item['price'] * $item['qty'], 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-3 pt-4 border-t border-gray-200">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>${{ number_format($subtotal ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="text-green-600">Free</span>
                        </div>
                        <div class="flex justify-between text-2xl font-black pt-4 border-t border-gray-200">
                            <span>Total</span>
                            <span>${{ number_format($subtotal ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Checkout Form --}}
            <div class="lg:col-span-2 order-1 lg:order-2">
                <div class="bg-white rounded-3xl shadow-lg p-8">
                    <h2 class="text-2xl font-black mb-6">Billing Information</h2>

                    <form id="checkoutForm" action="{{ route('checkout.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- All your input fields (name, email, phone, address, etc.) remain exactly the same -->
                        <!-- ... (keep all inputs unchanged) ... -->

                        <div>
                            <label for="name" class="block text-sm font-bold mb-2">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-black @error('name') border-red-500 @enderror" placeholder="John Doe" required>
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-bold mb-2">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-black @error('email') border-red-500 @enderror" placeholder="john@example.com" required>
                            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-bold mb-2">Phone Number <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-black @error('phone') border-red-500 @enderror" placeholder="+855 12 345 678" required>
                            @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-bold mb-2">Location (Delivery Address) <span class="text-red-500">*</span></label>

                            <!-- Address field that will be populated by map selection -->
                            <textarea name="address" id="address" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-black @error('address') border-red-500 @enderror" placeholder="Select location on map or enter address manually" required aria-required="true">{{ old('address') }}</textarea>

                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Please select your delivery location on the map below or enter it manually</p>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="city" class="block text-sm font-bold mb-2">City</label>
                                <input type="text" name="city" id="city" value="{{ old('city') }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl" placeholder="Phnom Penh">
                            </div>
                            <div>
                                <label for="postal_code" class="block text-sm font-bold mb-2">Postal Code</label>
                                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl" placeholder="12000">
                            </div>
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-bold mb-2">Country</label>
                            <input type="text" name="country" id="country" value="{{ old('country', 'Cambodia') }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl" placeholder="Cambodia">
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-bold mb-2">Order Notes (Optional)</label>
                            <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl" placeholder="Special delivery instructions...">{{ old('notes') }}</textarea>
                        </div>

                        <div>
                            <label for="payment_method" class="block text-sm font-bold mb-2">Payment Method <span class="text-red-500">*</span></label>
                            <select name="payment_method" id="payment_method" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-black" required>
                                <option value="cod">Cash on Delivery</option>
                                <option value="khqr">KHQR (Bakong)</option>
                            </select>
                        </div>

                        <button type="submit" id="placeOrderBtn"
                                class="w-full bg-black text-white font-bold py-4 rounded-xl hover:bg-gray-800 transition shadow-lg hover:shadow-2xl text-lg">
                            Place Order
                        </button>

                        <p class="text-sm text-gray-500 text-center">
                            By placing your order, you agree to our Terms of Service and Privacy Policy.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- KHQR Modal - Beautiful Card Design --}}
<div id="khqrModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full overflow-hidden relative">
        <button type="button" id="closeModal" class="absolute top-3 right-4 text-2xl text-white hover:text-gray-200 z-10">×</button>

        {{-- Red Header with KHQR Logo --}}
        <div class="bg-red-600 text-white py-6 px-6 relative">
            <div class="text-center">
                <div class="text-3xl font-black tracking-wider">KHQR</div>
            </div>
            <div class="absolute top-0 right-0 w-0 h-0 border-l-[30px] border-l-transparent border-t-[30px] border-t-white"></div>
        </div>

        {{-- Recipient Name --}}
        <div class="px-6 pt-4 pb-2">
            <p id="recipientName" class="text-lg font-semibold text-gray-800">Loading...</p>
        </div>

        {{-- Amount --}}
        <div class="px-6 pb-4">
            <div class="flex items-baseline gap-2">
                <span id="modalAmount" class="text-4xl font-black text-gray-900">0</span>
                <span class="text-xl font-medium text-gray-700">KHR</span>
            </div>
        </div>

        {{-- Dashed Separator --}}
        <div class="px-6 pb-4">
            <div class="border-t-2 border-dashed border-gray-300"></div>
        </div>

        {{-- QR Code Container --}}
        <div class="px-6 pb-6 flex justify-center">
            <div id="qrContainer" class="relative">
                <div class="w-64 h-64 bg-gray-100 animate-pulse rounded-lg flex items-center justify-center">
                    <span class="text-gray-400">Loading QR Code...</span>
                </div>
            </div>
        </div>

        {{-- Status and Timer --}}
        <div class="px-6 pb-6">
            <div class="flex items-center justify-center gap-2 mb-2">
                <div id="statusDot" class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></div>
                <span id="statusText" class="text-sm font-medium text-gray-700">Waiting for payment...</span>
            </div>
            <p class="text-xs text-gray-500 text-center">Expires in <span id="timer" class="font-bold">15:00</span></p>
        </div>
    </div>
</div>

<script>
// Listen for location confirmation from map picker
document.addEventListener('DOMContentLoaded', function() {
    const addressField = document.getElementById('address');

    // Function to populate address field from map selection
    // This function can be called by the map script when location is selected
    window.setSelectedAddress = function(addressText) {
        if (addressField && addressText) {
            addressField.value = addressText;
            addressField.classList.remove('border-red-500');
            addressField.classList.add('border-green-500');

            // Show visual confirmation
            setTimeout(() => {
                addressField.classList.remove('border-green-500');
            }, 2000);
        }
    };

    // Listen for "Confirm This Location" button click
    // Look for button containing "Confirm" text
    document.addEventListener('click', function(e) {
        const target = e.target;
        const targetText = target.textContent || '';

        if (targetText.includes('Confirm') || targetText.includes('confirm')) {
            // Find the location text - look for the address text in nearby elements
            let locationText = null;

            // Method 1: Look for element with id containing "location"
            locationText = document.getElementById('locationText') ||
                          document.querySelector('[id*="location"]') ||
                          document.querySelector('[class*="location"]');

            // Method 2: If not found, look in parent container
            if (!locationText || !locationText.textContent || locationText.textContent.length < 20) {
                const parent = target.closest('div');
                if (parent) {
                    // Look for text that looks like an address (contains comma and is long enough)
                    const allTexts = parent.querySelectorAll('p, div, span');
                    for (let el of allTexts) {
                        const text = el.textContent.trim();
                        // Check if it looks like an address (has commas, not just labels)
                        if (text && text.includes(',') && text.length > 20 &&
                            !text.includes('Selected Location:') &&
                            !text.includes('Coordinates:') &&
                            !text.includes('Use My Location') &&
                            !text.includes('Center on')) {
                            locationText = el;
                            break;
                        }
                    }
                }
            }

            // Populate address field
            if (locationText && locationText.textContent) {
                let addressText = locationText.textContent.trim();

                // Clean up the text (remove labels if present)
                addressText = addressText.replace(/^Selected Location:\s*/i, '');
                addressText = addressText.replace(/\s*Coordinates:.*$/i, '');

                if (addressText && addressText.length > 10) {
                    addressField.value = addressText;
                    addressField.classList.remove('border-red-500');
                    addressField.classList.add('border-green-500');

                    // Update button appearance
                    if (target.tagName === 'BUTTON' || target.closest('button')) {
                        const btn = target.tagName === 'BUTTON' ? target : target.closest('button');
                        btn.classList.add('bg-green-600');
                        btn.textContent = '✓ Location Confirmed';
                        btn.disabled = true;
                    }

                    // Remove green border after a moment
                    setTimeout(() => {
                        addressField.classList.remove('border-green-500');
                    }, 2000);

                    console.log('Address populated:', addressText);
                }
            }
        }
    });

    // Monitor for changes to location display elements
    const observer = new MutationObserver(function(mutations) {
        const locationText = document.getElementById('locationText');
        if (locationText && locationText.textContent && locationText.textContent.length > 10) {
            const text = locationText.textContent.trim();
            if (text && !text.includes('Selected Location:') && !text.includes('Coordinates:')) {
                if (addressField && (!addressField.value || addressField.value.trim().length < 10)) {
                    addressField.value = text;
                }
            }
        }
    });

    // Observe the document for changes
    observer.observe(document.body, {
        childList: true,
        subtree: true,
        characterData: true
    });
});

document.getElementById('checkoutForm').addEventListener('submit', async function (e) {
    const paymentMethod = document.getElementById('payment_method').value;

    // Validate required fields before submission
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const address = document.getElementById('address').value.trim();

    if (!name || !email || !phone || !address) {
        e.preventDefault();
        alert('Please fill in all required fields (Name, Email, Phone, and Address).\n\nIf you selected a location on the map, please click "Confirm This Location" button.');

        // Highlight address field if empty
        if (!address) {
            const addressField = document.getElementById('address');
            const locationText = document.getElementById('locationText');
            if (locationText && locationText.parentElement) {
                locationText.parentElement.classList.add('border-red-500');
            }
        }

        return false;
    }

    if (paymentMethod === 'cod') {
        // For COD, let the form submit naturally
        // Don't prevent default, don't disable button
        return true; // let normal submit happen
    }

    // For KHQR, prevent default and handle via AJAX
    e.preventDefault();

    const form = document.getElementById('checkoutForm');
    const formData = new FormData(form);

    // Double-check address is included in FormData
    if (!formData.get('address') || formData.get('address').trim() === '') {
        alert('Address is required. Please enter your delivery address.');
        return;
    }

    const btn = document.getElementById('placeOrderBtn');
    const originalText = btn.textContent;

    // Disable button and show processing state
    btn.disabled = true;
    btn.textContent = 'Processing...';

    try {
        // Force HTTPS - use current page protocol (which is HTTPS)
        let checkoutUrl = "{{ route('checkout.store') }}";
        // If it's a relative URL, make it absolute with current protocol
        if (checkoutUrl.startsWith('/')) {
            checkoutUrl = window.location.origin + checkoutUrl;
        }
        // Force HTTPS if somehow HTTP
        checkoutUrl = checkoutUrl.replace(/^http:/, 'https:');

        const response = await fetch(checkoutUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Non-JSON response:', text);
            alert('Server error: Received invalid response. Please try again.');
            btn.disabled = false;
            btn.textContent = originalText;
            return;
        }

        const data = await response.json();

        if (!response.ok || data.error || !data.qrCode) {
            const errorMsg = data.message || data.error || 'Something went wrong. Please try again.';
            if (data.errors) {
                const errorList = Object.values(data.errors).flat().join('\n');
                alert(errorMsg + '\n\n' + errorList);
                // Scroll to top to show error message area
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                alert(errorMsg);
            }
            btn.disabled = false;
            btn.textContent = originalText;
            return;
        }

        // Show Modal
        const modal = document.getElementById('khqrModal');
        modal.classList.remove('hidden');

        // Set recipient name from form
        const recipientName = document.getElementById('name').value || 'Customer';
        document.getElementById('recipientName').textContent = recipientName;

        // Display QR Code
        document.getElementById('qrContainer').innerHTML =
            `<img src="data:image/svg+xml;base64,${data.qrCode}" alt="KHQR Code" class="w-64 h-64 rounded-lg">`;

        // Display amount in KHR (formatted with commas)
        const amountKHR = data.amount || (data.amountUSD * 4100);
        document.getElementById('modalAmount').textContent = amountKHR.toLocaleString();

        let secondsLeft = 15 * 60; // 15 minutes
        const timerEl = document.getElementById('timer');
        const statusText = document.getElementById('statusText');
        const statusDot = document.getElementById('statusDot');

        let alreadyPaid = false;

        // Combined timer and payment check (like the example)
        const timer = setInterval(async () => {
            // Update countdown timer
            secondsLeft--;
            const m = String(Math.floor(secondsLeft / 60)).padStart(2, '0');
            const s = String(secondsLeft % 60).padStart(2, '0');
            timerEl.textContent = m + ':' + s;

            // Check payment status every second (like the example)
            if (!alreadyPaid && secondsLeft > 0) {
                try {
                    // Force HTTPS - use current page protocol
                    let paymentStatusUrl = "{{ route('payment.status', ['order' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', data.orderId);
                    // If it's a relative URL, make it absolute with current protocol
                    if (paymentStatusUrl.startsWith('/')) {
                        paymentStatusUrl = window.location.origin + paymentStatusUrl;
                    }
                    // Force HTTPS if somehow HTTP
                    paymentStatusUrl = paymentStatusUrl.replace(/^http:/, 'https:');

                    const res = await fetch(`${paymentStatusUrl}?t=${Date.now()}`, {
                        cache: 'no-store',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    if (res.ok) {
                        const result = await res.json();

                        // Check if paid (like the example checks responseCode === 0)
                        if (result.paid === true || result.responseCode === 0) {
                            alreadyPaid = true;
                            clearInterval(timer);

                            // Update UI to show payment received
                            statusText.textContent = 'Payment Received! Redirecting...';
                            statusText.classList.remove('text-gray-700');
                            statusText.classList.add('text-green-600', 'font-bold');
                            statusDot.classList.remove('bg-yellow-500', 'animate-pulse');
                            statusDot.classList.add('bg-green-500');

                            // Auto-redirect immediately - use redirect URL from response or construct it
                            const redirectUrl = result.redirect || (window.location.origin + '/order/success/' + data.orderId);
                            // Force HTTPS
                            const finalUrl = redirectUrl.replace(/^http:/, 'https:');
                            
                            // Redirect immediately, no delay needed
                            window.location.href = finalUrl;
                        } else {
                            // Keep showing waiting status
                            statusText.textContent = 'Waiting for payment...';
                        }
                    }
                } catch (err) {
                    console.warn('Payment check error:', err);
                    // Continue checking even on error
                }
            }

            // Handle expiration
            if (secondsLeft <= 0) {
                clearInterval(timer);
                if (!alreadyPaid) {
                    statusText.textContent = 'Payment expired. Please try again.';
                    statusDot.classList.remove('bg-yellow-500', 'animate-pulse');
                    statusDot.classList.add('bg-red-500');
                }
            }
        }, 1000); // Check every 1 second like the example

        // Close modal
        const closeModal = () => {
            modal.classList.add('hidden');
            clearInterval(timer);
            clearInterval(pollInterval);
        };

        document.getElementById('closeModal').onclick = closeModal;
        modal.onclick = (e) => { if (e.target === modal) closeModal(); };

    } catch (error) {
        console.error('Checkout error:', error);
        let errorMessage = 'Connection error. Please try again.';

        if (error instanceof TypeError && error.message.includes('fetch')) {
            errorMessage = 'Network error. Please check your internet connection and try again.';
        } else if (error.message) {
            errorMessage = 'Error: ' + error.message;
        }

        alert(errorMessage);
        btn.disabled = false;
        btn.textContent = originalText;
    }
});
</script>
@endsection
