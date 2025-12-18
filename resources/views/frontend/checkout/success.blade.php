@extends('layouts.front')

@section('title', 'Order Confirmation')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-6 lg:px-8">

        {{-- Success Icon --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full mb-6">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-4xl md:text-5xl font-black mb-4">Order Confirmed!</h1>
            <p class="text-xl text-gray-600">Thank you for your purchase. We've received your order.</p>
        </div>

        {{-- Order Details Card --}}
        <div class="bg-white rounded-3xl shadow-lg p-8 mb-8">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 pb-6 border-b border-gray-200">
                <div>
                    <h2 class="text-2xl font-black mb-2">
                        Order #{{ isset($order->order_number) && $order->order_number ? $order->order_number : $order->id }}
                    </h2>
                    <p class="text-gray-600">
                        Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}
                    </p>
                </div>
                <div class="mt-4 md:mt-0">
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'paid' => 'bg-green-100 text-green-800',
                            'processing' => 'bg-blue-100 text-blue-800',
                            'shipped' => 'bg-indigo-100 text-indigo-800',
                            'delivered' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800'
                        ];
                        $statusClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-block px-4 py-2 {{ $statusClass }} rounded-full font-bold text-sm">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="mb-6">
                <h3 class="text-lg font-bold mb-4">Order Items</h3>
                <div class="space-y-4">
                    @foreach($order->orderItems as $item)
                        <div class="flex gap-4 pb-4 border-b border-gray-100 last:border-0">
                            @if($item->product && $item->product->image)
                                <img src="{{ asset($item->product->image) }}"
                                     alt="{{ $item->product->name }}"
                                     class="w-20 h-20 object-cover rounded-lg">
                            @else
                                <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <span class="text-xs text-gray-400">No Image</span>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h4 class="font-bold mb-1">
                                    {{ $item->product->name ?? 'Product #' . $item->product_id }}
                                </h4>
                                <p class="text-sm text-gray-500">Quantity: {{ $item->qty }}</p>
                                <p class="text-sm font-semibold mt-1">
                                    ${{ number_format($item->price, 2) }} each
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold">
                                    ${{ number_format($item->subtotal ?? ($item->price * $item->qty), 2) }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="bg-gray-50 rounded-xl p-6 space-y-3">
                @if(isset($order->subtotal) && $order->subtotal != $order->total)
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span>${{ number_format($order->subtotal, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-gray-600">
                    <span>Shipping</span>
                    <span class="text-green-600">Free</span>
                </div>
                <div class="flex justify-between text-2xl font-black pt-4 border-t border-gray-300">
                    <span>Total</span>
                    <span>${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Shipping Information --}}
        <div class="bg-white rounded-3xl shadow-lg p-8 mb-8">
            <h3 class="text-xl font-black mb-4">Shipping Information</h3>
            <div class="space-y-2 text-gray-700">
                <p class="font-semibold">{{ $order->name }}</p>
                <p>{{ $order->address }}</p>
                @if($order->city)
                    <p>{{ $order->city }}{{ $order->postal_code ? ', ' . $order->postal_code : '' }}</p>
                @endif
                @if($order->country)
                    <p>{{ $order->country }}</p>
                @endif
                <p class="mt-2">
                    <span class="font-semibold">Email:</span> {{ $order->email }}
                </p>
                <p>
                    <span class="font-semibold">Phone:</span> {{ $order->phone }}
                </p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('home') }}"
               class="px-8 py-4 bg-black text-white font-bold rounded-xl hover:bg-gray-800 transition text-center">
                Continue Shopping
            </a>
            @if(auth()->check())
                <a href="{{ route('orders.index') }}"
                   class="px-8 py-4 bg-gray-200 text-black font-bold rounded-xl hover:bg-gray-300 transition text-center">
                    View Order History
                </a>
            @endif
        </div>

        {{-- Help Text --}}
        <div class="mt-8 text-center text-gray-600">
            <p>We'll send you a confirmation email at <strong>{{ $order->email }}</strong></p>
            <p class="mt-2">If you have any questions, please contact our support team.</p>
        </div>

    </div>
</div>
@if($order->payment_method === 'khqr' && $order->status !== 'paid')
<script>
    // Final silent check on page load (in case polling missed it)
    fetch("{{ route('payment.status', $order) }}?t=" + Date.now())
        .then(r => r.json())
        .then(data => {
            if (data.paid) {
                setTimeout(() => location.reload(), 1500);
            }
        });
</script>
@endif
@endsection
