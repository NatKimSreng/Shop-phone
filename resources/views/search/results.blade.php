@extends('layouts.front')

@section('title', 'Search Results' . ($searchQuery ? ' for "' . $searchQuery . '"' : ''))

@section('content')
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        
        {{-- Breadcrumb --}}
        <nav class="mb-8 text-sm">
            <ol class="flex items-center space-x-2 text-gray-600">
                <li><a href="{{ route('home') }}" class="hover:text-black transition">Home</a></li>
                <li>/</li>
                <li class="text-black font-medium">Search Results</li>
            </ol>
        </nav>

        {{-- Header Section --}}
        <div class="mb-12">
            <h1 class="text-5xl md:text-6xl font-black text-center mb-4">
                Search Results
            </h1>
            @if($searchQuery)
                <p class="text-xl text-gray-600 text-center mb-4">
                    Results for <span class="font-bold">"{{ $searchQuery }}"</span>
                </p>
            @endif
            <p class="text-lg text-gray-500 text-center">
                {{ $products->total() }} {{ Str::plural('result', $products->total()) }} found
            </p>
        </div>

        {{-- Search Form --}}
        <div class="mb-10 bg-white rounded-3xl p-6 shadow-lg">
            <form action="{{ route('search') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <input
                    type="text"
                    name="q"
                    placeholder="Search products..."
                    value="{{ $searchQuery }}"
                    class="flex-1 px-6 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-black"
                >
                <button type="submit" class="px-8 py-4 bg-black text-white font-bold rounded-xl hover:bg-gray-800 transition">
                    Search
                </button>
            </form>
        </div>

        {{-- Products Grid --}}
        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
                @foreach($products as $product)
                    <div class="group bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500">
                        <a href="{{ route('frontend.products.show', $product) }}" class="block">
                            <div class="relative overflow-hidden bg-gray-100">
                                @if($product->image)
                                    <img src="{{ asset($product->image) }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-80 object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 group-hover:scale-105">
                                @else
                                    <div class="h-80 bg-gray-200 flex items-center justify-center">
                                        <span class="text-4xl text-gray-400 font-thin">NO IMAGE</span>
                                    </div>
                                @endif

                                @if(!$product->stock)
                                    <div class="absolute top-4 left-4 bg-black/90 text-white px-4 py-2 rounded-full text-xs font-bold">
                                        SOLD OUT
                                    </div>
                                @endif

                                @if(isset($product->old_price) && $product->old_price > $product->price)
                                    <div class="absolute top-4 right-4 bg-red-600 text-white px-3 py-1 rounded-full text-xs font-bold">
                                        SALE
                                    </div>
                                @endif
                            </div>

                            <div class="p-6">
                                <h3 class="font-bold text-lg mb-2 line-clamp-2 group-hover:text-gray-600 transition">
                                    {{ $product->name }}
                                </h3>
                                @if($product->category)
                                    <p class="text-sm text-gray-500 mb-4">{{ $product->category->category_name }}</p>
                                @endif

                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-2xl font-bold">
                                            ${{ number_format($product->price, 2) }}
                                        </span>
                                        @if(isset($product->old_price) && $product->old_price > $product->price)
                                            <span class="text-sm text-gray-500 line-through ml-2">
                                                ${{ number_format($product->old_price, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-gray-400 group-hover:text-black transition text-2xl">→</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-24 bg-white rounded-3xl shadow-lg">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="text-2xl text-gray-600 mb-4">No products found</p>
                <p class="text-gray-500 mb-6">Try adjusting your search terms</p>
                <div class="flex gap-4 justify-center">
                    <a href="{{ route('search') }}" class="inline-block px-8 py-4 bg-gray-200 text-black font-bold rounded-xl hover:bg-gray-300 transition">
                        New Search
                    </a>
                    <a href="{{ route('frontend.products.index') }}" class="inline-block px-8 py-4 bg-black text-white font-bold rounded-xl hover:bg-gray-800 transition">
                        Browse All Products
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
