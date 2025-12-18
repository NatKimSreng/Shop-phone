@extends('layouts.front')

@section('title', 'My Orders')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        
        {{-- Breadcrumb --}}
        <nav class="mb-8 text-sm">
            <ol class="flex items-center space-x-2 text-gray-600">
                <li><a href="{{ route('home') }}" class="hover:text-black transition">Home</a></li>
                <li>/</li>
                <li class="text-black font-medium">My Orders</li>
            </ol>
        </nav>

        <h1 class="text-4xl md:text-5xl font-black mb-8">My Orders</h1>

        {{-- Filters --}}
        <div class="bg-white rounded-3xl shadow-lg p-6 mb-8">
            <form action="{{ route('orders.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <select name="status" class="px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-black">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-black">
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       class="px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-black">
                <button type="submit" class="px-8 py-3 bg-black text-white font-bold rounded-xl hover:bg-gray-800 transition">
                    Filter
                </button>
                @if(request()->hasAny(['status', 'date_from', 'date_to']))
                    <a href="{{ route('orders.index') }}" class="px-8 py-3 bg-gray-200 text-black font-bold rounded-xl hover:bg-gray-300 transition">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Orders List --}}
        @if($orders->count() > 0)
            <div class="space-y-6">
                @foreach($orders as $order)
                    <div class="bg-white rounded-3xl shadow-lg overflow-hidden hover:shadow-2xl transition">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-4 mb-3">
                                        <h3 class="text-xl font-black">
                                            Order #{{ $order->order_number ?? $order->id }}
                                        </h3>
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'processing' => 'bg-blue-100 text-blue-800',
                                                'shipped' => 'bg-indigo-100 text-indigo-800',
                                                'delivered' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800'
                                            ];
                                            $color = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $color }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 mb-2">
                                        Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $order->orderItems->count() }} item(s) • Total: ${{ number_format($order->total, 2) }}
                                    </p>
                                </div>
                                <div class="flex gap-3">
                                    <a href="{{ route('orders.show', $order) }}" 
                                       class="px-6 py-3 bg-black text-white font-bold rounded-xl hover:bg-gray-800 transition">
                                        View Details
                                    </a>
                                </div>
                            </div>

                            {{-- Order Items Preview --}}
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @foreach($order->orderItems->take(4) as $item)
                                        <div class="flex items-center gap-3">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ asset($item->product->image) }}" 
                                                     alt="{{ $item->product->name }}"
                                                     class="w-16 h-16 object-cover rounded-lg">
                                            @else
                                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <span class="text-xs text-gray-400">No Image</span>
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium truncate">
                                                    {{ $item->product->name ?? 'Deleted Product' }}
                                                </p>
                                                <p class="text-xs text-gray-500">Qty: {{ $item->qty }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($order->orderItems->count() > 4)
                                        <div class="flex items-center justify-center">
                                            <span class="text-sm text-gray-500">
                                                +{{ $order->orderItems->count() - 4 }} more
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-24 bg-white rounded-3xl shadow-lg">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <p class="text-2xl text-gray-600 mb-4">No orders found</p>
                <p class="text-gray-500 mb-6">
                    @if(request()->hasAny(['status', 'date_from', 'date_to']))
                        Try adjusting your filters
                    @else
                        You haven't placed any orders yet
                    @endif
                </p>
                <a href="{{ route('frontend.products.index') }}" 
                   class="inline-block px-8 py-4 bg-black text-white font-bold rounded-xl hover:bg-gray-800 transition">
                    Start Shopping
                </a>
            </div>
        @endif

    </div>
</div>
@endsection

