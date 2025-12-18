<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'MyApp') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
    @stack('scripts-head')
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900" x-data="{ mobileOpen: false }">
    <!-- Header / Navbar -->
    <header class="bg-white shadow-sm sticky top-0 z-40 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3">
                        <span class="text-xl font-bold text-black">{{ config('app.name') }}</span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex space-x-6 text-sm font-medium">
                    <a href="{{ route('home') }}"
                    class="{{ request()->routeIs('home') ? 'text-black font-bold' : 'text-gray-700 hover:text-black' }} px-4 py-2 rounded-md transition">
                        Home
                    </a>
                    <a href="{{ route('frontend.products.index') }}"
                    class="{{ request()->routeIs('frontend.products.*') ? 'text-black font-bold' : 'text-gray-700 hover:text-black' }} px-4 py-2 rounded-md transition">
                        Products
                    </a>
                    <a href="#" class="text-gray-700 hover:text-black px-4 py-2 rounded-md transition">
                        About
                    </a>
                    <a href="#" class="text-gray-700 hover:text-black px-4 py-2 rounded-md transition">
                        Contact
                    </a>
                </nav>

                <!-- Right Side (Cart, Auth Links / User Menu) -->
                <div class="flex items-center space-x-4">
                    @php
                        $cartCount = 0;
                        $cart = session('cart', []);
                        foreach ($cart as $item) {
                            $cartCount += $item['qty'];
                        }
                    @endphp
                    
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-700 hover:text-black transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        @if($cartCount > 0)
                            <span class="absolute top-0 right-0 bg-black text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    @auth
                        <div x-data="{ open: false }" class="relative hidden md:block">
                            <button @click="open = !open" class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-black focus:outline-none">
                                <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}" alt="Avatar" class="h-8 w-8 rounded-full">
                                <span class="hidden lg:inline">{{ auth()->user()->name }}</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.products.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Admin Panel</a>
                                    <hr class="my-1">
                                @endif
                                <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">My Orders</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('admin.login') }}" class="hidden md:block text-sm font-medium text-gray-700 hover:text-black">Log in</a>
                        <a href="{{ route('auth.register') }}" class="hidden md:block text-sm font-medium text-white bg-black hover:bg-gray-800 px-4 py-2 rounded-lg transition">Register</a>
                    @endauth

                    <!-- Mobile menu button -->
                    <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-gray-700 hover:text-black">
                        <svg x-show="!mobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileOpen" x-transition class="md:hidden bg-white border-t border-gray-200">
            <div class="px-4 pt-4 pb-6 space-y-2">
                <a href="{{ route('home') }}"
                   class="block px-4 py-3 rounded-lg {{ request()->routeIs('home') ? 'bg-black text-white font-bold' : 'text-gray-700 hover:bg-gray-50' }} transition">
                    Home
                </a>
                <a href="{{ route('frontend.products.index') }}"
                   class="block px-4 py-3 rounded-lg {{ request()->routeIs('frontend.products.*') ? 'bg-black text-white font-bold' : 'text-gray-700 hover:bg-gray-50' }} transition">
                    Products
                </a>
                <a href="{{ route('cart.index') }}"
                   class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cart @if($cartCount > 0)<span class="ml-2 bg-black text-white px-2 py-1 rounded-full text-xs">{{ $cartCount }}</span>@endif
                </a>
                <a href="#" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    About
                </a>
                <a href="#" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Contact
                </a>
                @auth
                    <hr class="my-2">
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.products.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Admin Panel
                        </a>
                    @endif
                    <a href="{{ route('orders.index') }}" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        My Orders
                    </a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Logout
                        </button>
                    </form>
                @else
                    <hr class="my-2">
                    <a href="{{ route('admin.login') }}" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Log in
                    </a>
                    <a href="{{ route('auth.register') }}" class="block px-4 py-3 rounded-lg bg-black text-white font-bold text-center">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('hero') <!-- Optional full-width hero section -->

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-white text-lg font-semibold mb-4">{{ config('app.name') }}</h3>
                    <p class="text-sm">Building amazing things with Laravel & Tailwind.</p>
                </div>

                <div>
                    <h4 class="text-white font-medium mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                        <li><a href="{{ route('frontend.products.index') }}" class="hover:text-white">Products</a></li>
                        <li><a href="#" class="hover:text-white">About</a></li>
                        <li><a href="#" class="hover:text-white">Contact</a></li>
                        <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-medium mb-4">Follow Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-white">Twitter</a>
                        <a href="#" class="hover:text-white">GitHub</a>
                        <a href="#" class="hover:text-white">LinkedIn</a>
                    </div>
                </div>

                <div>
                    <h4 class="text-white font-medium mb-4">Newsletter</h4>
                    <form class="flex flex-col sm:flex-row gap-2">
                        <input type="email" placeholder="Your email" class="px-4 py-2 rounded-lg text-gray-900 text-sm">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-10 pt-8 border-t border-gray-800 text-center text-sm">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </footer>
    @if($cartCount > 0)
    <a href="{{ route('cart.index') }}"
       class="fixed bottom-6 right-6 bg-black hover:bg-gray-800 text-white
              px-5 py-4 rounded-full shadow-2xl flex items-center gap-3
              transition transform hover:scale-105 z-50">

        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25h9.75m-9.75
                     0L5.106 5.272A1.125 1.125 0 0 0 4.02 4.5H2.25m5.25
                     9.75L9 18.75m7.5-4.5L15 18.75m-6 0a1.875 1.875 0 1
                     0 3.75 0m3.75 0a1.875 1.875 0 1 0 3.75 0" />
        </svg>

        <span class="bg-white text-black font-bold rounded-full px-3 py-1 text-sm">
            {{ $cartCount }}
        </span>

    </a>
    @endif



    @stack('scripts')
</body>
</html>
