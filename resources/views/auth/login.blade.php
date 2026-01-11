<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $tetapanUmum = \App\Models\TetapanUmum::first();
        $systemName = $tetapanUmum ? $tetapanUmum->nama_sistem : config('app.name', 'RISDA Odometer');
    @endphp

    <title>{{ $systemName }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
        
        @keyframes pulse-glow {
            0%, 100% {
                transform: scale(1);
                opacity: 0.8;
            }
            50% {
                transform: scale(1.05);
                opacity: 1;
            }
        }
        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        .animate-pulse-glow:nth-child(1) {
            animation-delay: 0s;
        }
        .animate-pulse-glow:nth-child(2) {
            animation-delay: 0.3s;
        }
        .animate-pulse-glow:nth-child(3) {
            animation-delay: 0.6s;
        }
    </style>
</head>
<body class="antialiased" style="font-family: 'Poppins', sans-serif; margin: 0; padding: 0;">
    <div class="min-h-screen w-full bg-white text-[#1b1b18] flex">
        <!-- Left: 70% (branding/illustration) -->
        <div class="w-[70%] hidden md:flex relative p-12 bg-gradient-to-br from-[#3b82f6] to-[#1e40af]">
            <div class="w-full flex flex-col items-center justify-center text-white">
                <!-- Logo JARA -->
                <div class="mb-8">
                    <img src="{{ asset('images/logo.png') }}" alt="JARA Logo" class="w-32 h-32 object-contain">
                </div>
                <div class="max-w-2xl w-full text-center">
                    <h1 class="font-bold mb-4" style="font-size: 36px; line-height: 1.2;">Selamat Datang ke Sistem JARA</h1>
                    <p class="text-white/90 mb-6" style="font-size: 14px; line-height: 1.6;">
                        Jejak Aset dan Rekod Automotif
                    </p>
                    <div class="grid grid-cols-3 gap-6 mt-12">
                        <div class="text-center animate-pulse-glow">
                            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-white/20 flex items-center justify-center transition-all duration-300 hover:bg-white/30 hover:scale-110">
                                <span class="material-symbols-outlined" style="font-size: 32px;">location_on</span>
                            </div>
                            <p style="font-size: 12px;">Jejak Perjalanan</p>
                        </div>
                        <div class="text-center animate-pulse-glow">
                            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-white/20 flex items-center justify-center transition-all duration-300 hover:bg-white/30 hover:scale-110">
                                <span class="material-symbols-outlined" style="font-size: 32px;">directions_car</span>
                            </div>
                            <p style="font-size: 12px;">Pengurusan Kenderaan</p>
                        </div>
                        <div class="text-center animate-pulse-glow">
                            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-white/20 flex items-center justify-center transition-all duration-300 hover:bg-white/30 hover:scale-110">
                                <span class="material-symbols-outlined" style="font-size: 32px;">receipt_long</span>
                            </div>
                            <p style="font-size: 12px;">Laporan Terperinci</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: 30% (login form) -->
        <div class="w-full md:w-[30%] flex items-center justify-center p-6 md:p-10 bg-gray-50">
            <div class="w-full max-w-sm">
                <!-- Logo & Title -->
                <div class="text-center mb-8">
                    <div class="mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="JARA Logo" class="w-20 h-20 mx-auto object-contain">
                    </div>
                    <h2 class="font-semibold text-gray-800" style="font-size: 20px;">Log Masuk</h2>
                    <p class="text-gray-500 mt-2" style="font-size: 12px;">Sila masukkan kelayakan anda</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Login Card -->
                <div class="bg-white rounded-sm shadow-lg border border-gray-200 p-8">

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-forms.input-label for="email" value="Emel" />
                        <x-forms.text-input id="email" class="block mt-1 w-full shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" style="padding: 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 2px;" />
                        <x-forms.input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-forms.input-label for="password" value="Kata Laluan" />
                        <x-forms.text-input id="password" class="block mt-1 w-full shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" type="password" name="password" required autocomplete="current-password" style="padding: 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 2px;" />
                        <x-forms.input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                            <span class="ms-2 text-sm text-gray-600" style="font-size: 12px;">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full justify-center bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-[1.01]" style="padding: 10px; font-size: 14px; border-radius: 2px; border: none; cursor: pointer;">
                            AKSES
                        </button>
                    </div>
                </form>
                </div>

                <!-- Footer Links -->
                <div class="mt-6 text-center w-full" x-data>
                    <div class="flex items-center justify-between text-gray-500" style="font-size: 11px;">
                        <a href="#" @click.prevent="$dispatch('open-disclaimer')" class="hover:text-gray-700 hover:underline cursor-pointer">Penafian</a>
                        <span>/</span>
                        <a href="#" @click.prevent="$dispatch('open-privacy')" class="hover:text-gray-700 hover:underline cursor-pointer">Privasi</a>
                        <span>/</span>
                        <a href="#" @click.prevent="$dispatch('open-terms')" class="hover:text-gray-700 hover:underline cursor-pointer">Terma Penggunaan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Policy Modals - Outside main container for proper z-index --}}
    <div x-data="{
        disclaimerModal: false,
        privacyModal: false,
        termsModal: false
    }"
    @open-disclaimer.window="disclaimerModal = true"
    @open-privacy.window="privacyModal = true"
    @open-terms.window="termsModal = true">
        @include('help.partials.policy-disclaimer-modal')
        @include('help.partials.policy-privacy-modal')
        @include('help.partials.policy-terms-modal')
    </div>
</body>
</html>
