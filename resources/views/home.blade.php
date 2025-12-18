{{-- resources/views/frontend/home.blade.php --}}
@extends('layouts.front')

@section('title', config('app.name'))

{{-- Hero Section - Monochrome & Elegant --}}
@section('hero')
<div class="relative bg-black text-white min-h-screen flex items-center">
    <div class="absolute inset-0 bg-gradient-to-br from-black via-gray-900 to-black opacity-90"></div>
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1558591710-4b4a1ae0f04d?w=1920&q=80')] bg-cover bg-center opacity-5"></div>

    <div class="relative max-w-7xl mx-auto px-6 lg:px-8 text-center py-24 lg:py-32">
        <h1 class="text-5xl md:text-7xl font-black tracking-tight mb-6">
            {{ config('app.name') }}
        </h1>
        <p class="text-xl md:text-2xl font-light text-gray-300 max-w-3xl mx-auto mb-10 leading-relaxed">
            Timeless craftsmanship. Premium materials. Designed for those who value quality over everything.
        </p>

        <!-- Search Bar -->
        <form action="{{ route('search') }}" method="GET" class="max-w-2xl mx-auto mt-12">
            <div class="flex flex-col sm:flex-row gap-4">
                <input
                    type="text"
                    name="q"
                    placeholder="Search products..."
                    value="{{ request('q') }}"
                    class="flex-1 px-8 py-5 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl text-white placeholder-gray-400 focus:outline-none focus:border-white/50 focus:bg-white/20 transition text-lg"
                    required
                >
                <button class="px-12 py-5 bg-white text-black font-bold rounded-2xl hover:bg-gray-200 transition shadow-2xl">
                    Search
                </button>
            </div>
        </form>

        <!-- Trust Line -->
        <div class="mt-16 flex flex-wrap justify-center gap-x-12 gap-y-4 text-sm text-gray-400">
            <span>• Secure Checkout</span>
            <span>• Free Shipping Over $100</span>
            <span>• 30-Day Returns</span>
            <span>• Carbon Neutral Shipping</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="bg-gray-50 py-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">

        {{-- Featured Categories --}}
        <section class="mb-24">
            <h2 class="text-4xl md:text-5xl font-black text-center mb-16">Shop by Category</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach(\App\Models\Category::whereHas('products')->take(8)->get() as $category)
                    <a href="{{ route('category.show', $category->slug ?? $category->id) }}"
                       class="group relative overflow-hidden rounded-3xl bg-white shadow-lg hover:shadow-2xl transition-all duration-500">
                        <div class="aspect-square relative">
                            <img src="https://ih1.redbubble.net/image.5527749050.5297/bg,f8f8f8-flat,750x,075,f-pad,750x1000,f8f8f8.u1.jpg"
                                 alt="{{ $category->category_name }}"
                                 class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                                <h3 class="text-2xl font-bold mb-1">{{ $category->category_name }}</h3>
                                <p class="text-sm opacity-80">{{ $category->products()->count() }} items</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Featured Products --}}
        <section class="mb-24">
            <div class="flex justify-between items-center mb-12">
                <h2 class="text-4xl md:text-5xl font-black">Featured Products</h2>
                    <a href="{{ route('frontend.products.index') }}" class="text-gray-600 hover:text-black font-medium text-lg transition">
                    View All →
                </a>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($products->take(8) as $product)
                        <div class="group bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500">
                            <a href="{{ route('frontend.products.show', $product) }}" class="block">
                                <div class="relative overflow-hidden bg-gray-100">
                                    @if($product->image)
                                        <img src="{{ asset($product->image) }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-80 object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 group-hover:scale-105">
                                    @else
                                        <div class="h-80 bg-gray-200 flex items-center justify-center">
                                            <span class="text-5xl text-gray-400 font-thin">NO IMAGE</span>
                                        </div>
                                    @endif

                                    @if(!$product->stock)
                                        <div class="absolute top-4 left-4 bg-black/90 text-white px-4 py-2 rounded-full text-xs font-bold">
                                            SOLD OUT
                                        </div>
                                    @endif
                                </div>

                                <div class="p-8">
                                    <h3 class="font-medium text-lg mb-2 line-clamp-2 group-hover:text-gray-600 transition">
                                        {{ $product->name }}
                                    </h3>
                                    @if($product->category)
                                        <p class="text-sm text-gray-500 mb-4">{{ $product->category->category_name }}</p>
                                    @endif

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="text-3xl font-bold">
                                                ${{ number_format($product->price, 2) }}
                                            </span>
                                            @if(isset($product->old_price) && $product->old_price > $product->price)
                                                <span class="text-sm text-gray-500 line-through ml-3">
                                                    ${{ number_format($product->old_price, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-gray-400 group-hover:text-black transition">→</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-24 bg-white rounded-3xl">
                    <p class="text-2xl text-gray-600 mb-6">No products yet.</p>
                    <a href="" class="text-black font-medium underline">Add your first product →</a>
                </div>
            @endif
        </section>

        {{-- Promo Banner - Full Width Monochrome --}}
        <section class="my-32">
            <div class="bg-black text-white rounded-3xl p-16 md:p-24 text-center">
                <h2 class="text-5xl md:text-6xl font-black mb-6">End of Season Clearance</h2>
                <p class="text-2xl font-light text-gray-300 mb-10 max-w-2xl mx-auto">
                    Up to 70% off selected items. Once they're gone, they're gone forever.
                </p>
                <a href="#" class="inline-block bg-white text-black px-12 py-5 rounded-full font-bold text-xl hover:bg-gray-200 transition shadow-2xl">
                    Shop Sale
                </a>
            </div>
        </section>

        {{-- Testimonials --}}
        <section class="my-32">
            <h2 class="text-4xl md:text-5xl font-black text-center mb-16">What People Say</h2>
            <div class="grid md:grid-cols-3 gap-10">
                @for($i = 1; $i <= 3; $i++)
                    <div class="bg-white p-10 rounded-3xl shadow-lg">
                        <div class="flex mb-6">
                            @for($j = 1; $j <= 5; $j++)
                                <svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.965a1 1 0 00.95.69h4.163c.969 0 1.371 1.24.588 1.81l-3.374 2.456a1 1 0 00-.364 1.118l1.287 3.965c.3.921-.755 1.688-1.54 1.118l-3.374-2.456a1 1 0 00-1.175 0l-3.374 2.456c-.784.57-1.838-.197-1.54-1.118l1.287-3.965a1 1 0 00-.364-1.118L2.317 9.392c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.965z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-lg text-gray-700 italic mb-6">
                            "Absolutely stunning quality. The attention to detail is unmatched. Will definitely order again."
                        </p>
                        <p class="font-semibold">— Alex Chen</p>
                    </div>
                @endfor
            </div>
        </section>

        {{-- Newsletter --}}
        <section class="my-32 text-center">
            <div class="bg-black text-white rounded-3xl p-16">
                <h2 class="text-4xl md:text-5xl font-black mb-6">Stay in the loop</h2>
                <p class="text-xl text-gray-300 mb-10 max-w-2xl mx-auto">
                    Be the first to know about new drops, exclusive offers, and restocks.
                </p>
                <form class="max-w-md mx-auto flex flex-col sm:flex-row gap-4">
                    <input type="email" placeholder="Your email" class="flex-1 px-6 py-4 bg-white/10 border border-white/20 rounded-xl placeholder-gray-400 focus:outline-none focus:border-white">
                    <button class="px-10 py-4 bg-white text-black font-bold rounded-xl hover:bg-gray-200 transition">
                        Subscribe
                    </button>
                </form>
                <p class="text-sm text-gray-400 mt-6">No spam. Unsubscribe anytime.</p>
            </div>
        </section>

    </div>
</div>
@endsection
