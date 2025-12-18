@extends('layouts.front')

@section('title', 'Shopping Cart')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-4xl font-black text-gray-900 mb-2">Shopping Cart</h1>
            <p class="text-gray-600">Review your items before checkout</p>
        </div>

    @if(empty($cart))
            {{-- Empty Cart --}}
            <div class="bg-white rounded-3xl shadow-lg p-12 text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 rounded-full mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Your cart is empty</h2>
                <p class="text-gray-600 mb-8">Start adding some products to your cart!</p>
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center px-6 py-3 bg-black text-white font-bold rounded-xl hover:bg-gray-800 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Continue Shopping
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Cart Items --}}
                <div class="lg:col-span-2 space-y-4">
                    @foreach($cart as $id => $item)
                        @php
                            $subtotal = $item['price'] * $item['qty'];
                        @endphp
                        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-6" data-item-id="{{ $id }}">
                            <div class="flex flex-col sm:flex-row gap-6">
                                
                                {{-- Product Image --}}
                                <div class="flex-shrink-0">
                                    <img src="{{ asset($item['image']) }}" 
                                         alt="{{ $item['name'] }}"
                                         class="w-32 h-32 rounded-xl object-cover border-2 border-gray-100">
                                </div>

                                {{-- Product Info --}}
                                <div class="flex-1 flex flex-col justify-between">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $item['name'] }}</h3>
                                        <p class="text-lg font-semibold text-gray-900 mb-4">
                                            $<span class="item-price">{{ number_format($item['price'], 2) }}</span>
                                        </p>
                                    </div>

                                    {{-- Quantity Controls --}}
                                    <div class="flex items-center gap-4">
                                        <label class="text-sm font-semibold text-gray-700">Quantity:</label>
                                        <div class="flex items-center border-2 border-gray-200 rounded-lg overflow-hidden">
                                            <button type="button" 
                                                    class="quantity-decrease px-4 py-2 bg-gray-100 hover:bg-gray-200 transition font-bold text-gray-700"
                                                    data-item-id="{{ $id }}"
                                                    data-current-qty="{{ $item['qty'] }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                </svg>
                                            </button>
                                            <input type="number" 
                                                   name="qty" 
                                                   value="{{ $item['qty'] }}" 
                                                   min="1" 
                                                   max="999"
                                                   class="quantity-input w-16 px-3 py-2 text-center font-bold text-gray-900 border-0 focus:ring-2 focus:ring-black outline-none"
                                                   data-item-id="{{ $id }}"
                                                   data-price="{{ $item['price'] }}">
                                            <button type="button" 
                                                    class="quantity-increase px-4 py-2 bg-gray-100 hover:bg-gray-200 transition font-bold text-gray-700"
                                                    data-item-id="{{ $id }}"
                                                    data-current-qty="{{ $item['qty'] }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Subtotal & Remove --}}
                                <div class="flex flex-col items-end justify-between">
                                    <div class="text-right mb-4">
                                        <p class="text-sm text-gray-500 mb-1">Subtotal</p>
                                        <p class="text-2xl font-black text-gray-900 item-subtotal">
                                            ${{ number_format($subtotal, 2) }}
                                        </p>
                                    </div>
                                    <button type="button"
                                            class="remove-item text-red-600 hover:text-red-700 font-semibold text-sm transition"
                                            data-item-id="{{ $id }}">
                                        <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Remove
                                    </button>
                                </div>
                            </div>
        </div>
        @endforeach
    </div>

                {{-- Order Summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-4">
                        <h2 class="text-2xl font-black text-gray-900 mb-6">Order Summary</h2>
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between text-gray-600">
                                <span>Items ({{ count($cart) }})</span>
                                <span class="cart-items-count">{{ count($cart) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="cart-subtotal">
                                    ${{ number_format(array_sum(array_map(function($item) { return $item['price'] * $item['qty']; }, $cart)), 2) }}
                                </span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping</span>
                                <span class="text-green-600 font-semibold">Free</span>
                            </div>
                            <hr class="border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-lg font-bold text-gray-900">Total</span>
                                <span class="text-2xl font-black text-gray-900 cart-total">
                                    ${{ number_format(array_sum(array_map(function($item) { return $item['price'] * $item['qty']; }, $cart)), 2) }}
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('checkout.index') }}" 
                           class="block w-full px-6 py-4 bg-black text-white font-bold rounded-xl hover:bg-gray-800 transition text-center mb-4">
            Proceed to Checkout
        </a>
                        
                        <a href="{{ route('home') }}" 
                           class="block w-full px-6 py-3 bg-gray-100 text-gray-900 font-semibold rounded-xl hover:bg-gray-200 transition text-center">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Update quantity function
    function updateQuantity(itemId, newQty) {
        if (newQty < 1) newQty = 1;
        if (newQty > 999) newQty = 999;
        
        fetch(`/cart/update/${itemId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ qty: newQty })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update item subtotal
                const itemCard = document.querySelector(`[data-item-id="${itemId}"]`);
                const price = parseFloat(itemCard.querySelector('.item-price').textContent);
                const subtotal = price * newQty;
                itemCard.querySelector('.item-subtotal').textContent = '$' + subtotal.toFixed(2);
                
                // Update cart totals
                document.querySelector('.cart-total').textContent = '$' + data.total;
                document.querySelector('.cart-subtotal').textContent = '$' + data.total;
                
                // Update input value
                const input = itemCard.querySelector('.quantity-input');
                input.value = newQty;
                input.setAttribute('data-current-qty', newQty);
                
                // Update buttons
                itemCard.querySelectorAll('.quantity-decrease, .quantity-increase').forEach(btn => {
                    btn.setAttribute('data-current-qty', newQty);
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            location.reload();
        });
    }
    
    // Quantity decrease
    document.querySelectorAll('.quantity-decrease').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemId = this.getAttribute('data-item-id');
            const currentQty = parseInt(this.getAttribute('data-current-qty'));
            if (currentQty > 1) {
                updateQuantity(itemId, currentQty - 1);
            }
        });
    });
    
    // Quantity increase
    document.querySelectorAll('.quantity-increase').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemId = this.getAttribute('data-item-id');
            const currentQty = parseInt(this.getAttribute('data-current-qty'));
            updateQuantity(itemId, currentQty + 1);
        });
    });
    
    // Quantity input change
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const itemId = this.getAttribute('data-item-id');
            const newQty = parseInt(this.value) || 1;
            updateQuantity(itemId, newQty);
        });
        
        input.addEventListener('blur', function() {
            if (!this.value || parseInt(this.value) < 1) {
                this.value = 1;
                const itemId = this.getAttribute('data-item-id');
                updateQuantity(itemId, 1);
            }
        });
    });
    
    // Remove item
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                const itemId = this.getAttribute('data-item-id');
                window.location.href = `/cart/remove/${itemId}`;
            }
        });
    });
});
</script>
@endpush
@endsection
