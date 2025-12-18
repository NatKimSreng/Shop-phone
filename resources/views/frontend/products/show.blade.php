@extends('layouts.front')

@section('title', $product->name)

@section('content')
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="mb-8 text-sm">
            <ol class="flex items-center space-x-2 text-gray-600">
                <li><a href="{{ route('home') }}" class="hover:text-black transition">Home</a></li>
                <li>/</li>
                <li><a href="{{ route('frontend.products.index') }}" class="hover:text-black transition">Products</a></li>
                @if($product->category)
                    <li>/</li>
                    <li><a href="{{ route('category.show', $product->category->slug ?? $product->category->id) }}" class="hover:text-black transition">{{ $product->category->category_name }}</a></li>
                @endif
                <li>/</li>
                <li class="text-black font-medium">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

            {{-- Product Image Section --}}
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden">
                <div class="relative aspect-square bg-gray-100">
                    @if($product->image)
                        <img src="{{ asset($product->image) }}"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-200">
                            <span class="text-6xl text-gray-400 font-thin">NO IMAGE</span>
                        </div>
                    @endif

                    @if(!$product->stock)
                        <div class="absolute top-6 left-6 bg-black/90 text-white px-6 py-3 rounded-full text-sm font-bold">
                            SOLD OUT
                        </div>
                    @endif

                    @if(isset($product->old_price) && $product->old_price > $product->price)
                        <div class="absolute top-6 right-6 bg-red-600 text-white px-5 py-2 rounded-full text-sm font-bold">
                            SALE
                        </div>
                    @endif
                </div>
            </div>

            {{-- Product Info Section --}}
            <div class="space-y-8">
                <div>
                    @if($product->category)
                        <a href="{{ route('category.show', $product->category->slug ?? $product->category->id) }}"
                           class="inline-block text-sm text-gray-600 hover:text-black transition mb-4">
                            {{ $product->category->category_name }}
                        </a>
                    @endif

                    <h1 class="text-4xl md:text-5xl font-black mb-6">
                        {{ $product->name }}
                    </h1>

                    <div class="flex items-baseline gap-4 mb-8">
                        <span class="text-5xl font-black">
                            ${{ number_format($product->price, 2) }}
                        </span>
                        @if(isset($product->old_price) && $product->old_price > $product->price)
                            <span class="text-2xl text-gray-500 line-through">
                                ${{ number_format($product->old_price, 2) }}
                            </span>
                            <span class="px-4 py-2 bg-red-100 text-red-700 rounded-full text-sm font-bold">
                                {{ round((($product->old_price - $product->price) / $product->old_price) * 100) }}% OFF
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Description --}}
                <div class="border-t border-gray-200 pt-8">
                    <h2 class="text-xl font-bold mb-4">Description</h2>
                    <p class="text-gray-700 leading-relaxed text-lg">
                        {{ $product->description ?? 'No description available.' }}
                    </p>
                </div>

                {{-- Stock Status --}}
                <div class="flex items-center gap-3">
                    @if($product->stock)
                        <div class="flex items-center gap-2 text-green-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">In Stock</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-red-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">Out of Stock</span>
                        </div>
                    @endif
                </div>

                {{-- Add to Cart Button --}}
                @if($product->stock)
                    <a href="{{ route('cart.add', $product->id) }}"
                       class="block w-full text-center px-8 py-5 bg-black text-white font-bold rounded-xl hover:bg-gray-800 transition shadow-lg hover:shadow-2xl text-lg">
                        Add to Cart
                    </a>
                @else
                    <button disabled
                            class="block w-full text-center px-8 py-5 bg-gray-300 text-gray-500 font-bold rounded-xl cursor-not-allowed text-lg">
                        Out of Stock
                    </button>
                @endif

                {{-- Trust Badges --}}
                <div class="grid grid-cols-3 gap-4 pt-8 border-t border-gray-200">
                    <div class="text-center">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <p class="text-xs text-gray-600">Free Shipping</p>
                    </div>
                    <div class="text-center">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <p class="text-xs text-gray-600">Secure Checkout</p>
                    </div>
                    <div class="text-center">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <p class="text-xs text-gray-600">30-Day Returns</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Products Section --}}
        @if($product->category)
            @php
                $relatedProducts = \App\Models\Product::where('category_id', $product->category_id)
                    ->where('id', '!=', $product->id)
                    ->where('stock', 1)
                    ->take(4)
                    ->get();
            @endphp

            @if($relatedProducts->count() > 0)
                <div class="mt-24">
                    <h2 class="text-4xl md:text-5xl font-black mb-12 text-center">Related Products</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                        @foreach($relatedProducts as $related)
                            <div class="group bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500">
                                <a href="{{ route('frontend.products.show', $related) }}" class="block">
                                    <div class="relative overflow-hidden bg-gray-100">
                                        @if($related->image)
                                            <img src="{{ asset($related->image) }}"
                                                 alt="{{ $related->name }}"
                                                 class="w-full h-64 object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 group-hover:scale-105">
                                        @else
                                            <div class="h-64 bg-gray-200 flex items-center justify-center">
                                                <span class="text-3xl text-gray-400 font-thin">NO IMAGE</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-6">
                                        <h3 class="font-bold text-lg mb-2 line-clamp-2 group-hover:text-gray-600 transition">
                                            {{ $related->name }}
                                        </h3>
                                        <div class="flex items-center justify-between">
                                            <span class="text-2xl font-bold">
                                                ${{ number_format($related->price, 2) }}
                                            </span>
                                            <span class="text-gray-400 group-hover:text-black transition text-2xl">→</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

    </div>
</div>
@endsection
