<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'HR Platform') }}</title>
    <meta name="description" content="{{ $description ?? 'HR Outsourcing & Talent Management Platform' }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">HR</span>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">{{ config('app.name', 'HR Platform') }}</span>
                </a>

                <nav class="hidden md:flex space-x-6">
                    <a href="/" class="text-gray-600 hover:text-gray-900 transition">Home</a>
                    <a href="#" class="text-gray-600 hover:text-gray-900 transition">About</a>
                    <a href="#" class="text-gray-600 hover:text-gray-900 transition">Services</a>
                    <a href="#" class="text-gray-600 hover:text-gray-900 transition">Contact</a>
                </nav>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium transition">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen flex flex-col sm:justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full sm:max-w-md">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">HR</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900">{{ config('app.name') }}</span>
                    </div>
                    <p class="text-gray-600 text-sm">
                        Your trusted partner for HR outsourcing and talent management solutions.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900 mb-3">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-gray-600 hover:text-indigo-600 transition">About Us</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-indigo-600 transition">Services</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-indigo-600 transition">Pricing</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-indigo-600 transition">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900 mb-3">Legal</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-gray-600 hover:text-indigo-600 transition">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-indigo-600 transition">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-indigo-600 transition">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-200 mt-8 pt-8 text-center text-sm text-gray-600">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
    <x-toastr />
</body>
</html>
