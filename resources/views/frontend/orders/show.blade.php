@extends('layouts.front')

@section('title', 'Order Details #' . ($order->order_number ?? $order->id))

@section('content')
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        
        {{-- Breadcrumb --}}
        <nav class="mb-8 text-sm">
            <ol class="flex items-center space-x-2 text-gray-600">
                <li><a href="{{ route('home') }}" class="hover:text-black transition">Home</a></li>
                <li>/</li>
                <li><a href="{{ route('orders.index') }}" class="hover:text-black transition">My Orders</a></li>
                <li>/</li>
                <li class="text-black font-medium">Order Details</li>
            </ol>
        </nav>

        <div class="flex items-center justify-between mb-8">
            <h1 class="text-4xl md:text-5xl font-black">
                Order #{{ $order->order_number ?? $order->id }}
            </h1>
            <a href="{{ route('orders.index') }}" 
               class="px-6 py-3 bg-gray-200 text-black font-bold rounded-xl hover:bg-gray-300 transition">
                ← Back to Orders
            </a>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Order Status --}}
                <div class="bg-white rounded-3xl shadow-lg p-6">
                    <h2 class="text-2xl font-black mb-4">Order Status</h2>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                            'processing' => 'bg-blue-100 text-blue-800 border-blue-300',
                            'shipped' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                            'delivered' => 'bg-green-100 text-green-800 border-green-300',
                            'cancelled' => 'bg-red-100 text-red-800 border-red-300'
                        ];
                        $color = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-300';
                    @endphp
                    <div class="flex items-center gap-4">
                        <span class="px-4 py-2 rounded-full text-sm font-bold border-2 {{ $color }}">
                            {{ ucfirst($order->status) }}
                        </span>
                        <span class="text-gray-600">
                            Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}
                        </span>
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="bg-white rounded-3xl shadow-lg p-6">
                    <h2 class="text-2xl font-black mb-6">Order Items</h2>
                    <div class="space-y-4">
                        @foreach($order->orderItems as $item)
                            <div class="flex gap-4 pb-4 border-b border-gray-200 last:border-0">
                                @if($item->product && $item->product->image)
                                    <img src="{{ asset($item->product->image) }}" 
                                         alt="{{ $item->product->name }}"
                                         class="w-24 h-24 object-cover rounded-xl">
                                @else
                                    <div class="w-24 h-24 bg-gray-200 rounded-xl flex items-center justify-center">
                                        <span class="text-xs text-gray-400">No Image</span>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h3 class="font-bold text-lg mb-1">
                                        {{ $item->product->name ?? 'Deleted Product #' . $item->product_id }}
                                    </h3>
                                    @if($item->product && $item->product->category)
                                        <p class="text-sm text-gray-500 mb-2">{{ $item->product->category->category_name }}</p>
                                    @endif
                                    <div class="flex items-center gap-4 text-sm">
                                        <span class="text-gray-600">Quantity: <strong>{{ $item->qty }}</strong></span>
                                        <span class="text-gray-600">Price: <strong>${{ number_format($item->price, 2) }}</strong></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl font-black">
                                        ${{ number_format($item->subtotal ?? ($item->price * $item->qty), 2) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Order Notes --}}
                @if($order->notes)
                    <div class="bg-white rounded-3xl shadow-lg p-6">
                        <h2 class="text-2xl font-black mb-4">Order Notes</h2>
                        <p class="text-gray-700">{{ $order->notes }}</p>
                    </div>
                @endif

            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                
                {{-- Order Summary --}}
                <div class="bg-white rounded-3xl shadow-lg p-6">
                    <h2 class="text-2xl font-black mb-6">Order Summary</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>${{ number_format($order->subtotal ?? $order->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="text-green-600">Free</span>
                        </div>
                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex justify-between text-2xl font-black">
                                <span>Total</span>
                                <span>${{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Shipping Address --}}
                <div class="bg-white rounded-3xl shadow-lg p-6">
                    <h2 class="text-2xl font-black mb-4">Shipping Address</h2>
                    <div class="text-gray-700 space-y-1">
                        <p class="font-semibold">{{ $order->name }}</p>
                        <p>{{ $order->address }}</p>
                        @if($order->city)
                            <p>{{ $order->city }}{{ $order->postal_code ? ', ' . $order->postal_code : '' }}</p>
                        @endif
                        @if($order->country)
                            <p>{{ $order->country }}</p>
                        @endif
                    </div>
                </div>

                {{-- Contact Information --}}
                <div class="bg-white rounded-3xl shadow-lg p-6">
                    <h2 class="text-2xl font-black mb-4">Contact</h2>
                    <div class="space-y-2 text-gray-700">
                        <p><strong>Email:</strong> <a href="mailto:{{ $order->email }}" class="text-blue-600 hover:underline">{{ $order->email }}</a></p>
                        <p><strong>Phone:</strong> <a href="tel:{{ $order->phone }}" class="text-blue-600 hover:underline">{{ $order->phone }}</a></p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

