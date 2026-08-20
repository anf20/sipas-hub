<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light theme-parent">
    <head>
        @include('partials.head')
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
        <style>
            .hide-scrollbar::-webkit-scrollbar {
                display: none;
            }
            .hide-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
            /* Custom Input styling from Design System */
            flux-input input, flux-input [data-input] {
                background-color: #FFFFFF !important;
                border-color: #E0E2DC !important;
                border-width: 1px !important;
                border-style: solid !important;
                box-shadow: inset 0 1px 2px rgba(0,0,0,0.02) !important;
            }
            /* Icon color Neutral Gray (#777775) */
            flux-input svg {
                color: #777775 !important;
            }
            /* Placeholder color Neutral Gray (#777775) */
            flux-input input::placeholder {
                color: #777775 !important;
                opacity: 1 !important;
            }
        </style>
    </head>
    <body class="min-h-screen bg-[#142d1e] text-on-background font-body-md antialiased relative overflow-x-hidden flex flex-col justify-center items-center p-4">
        <!-- Glowing ambient background blobs for premium feel -->
        <div class="absolute top-[-20%] left-[-20%] w-[60vw] h-[60vw] max-w-[600px] bg-primary-container/20 rounded-full blur-[100px] -z-10 pointer-events-none"></div>
        <div class="absolute bottom-[-20%] right-[-20%] w-[60vw] h-[60vw] max-w-[600px] bg-primary/10 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

        <!-- Clean Centered Mobile-first Card (No rigid phone bezels) -->
        <div class="w-full max-w-[412px] bg-[#F0F1EE] rounded-[36px] shadow-2xl overflow-hidden flex flex-col border border-white/5">
            
            <!-- 1. Header Area: Darker Sage green background -->
            <div class="bg-[#527658] px-8 pt-8 pb-10 flex flex-col items-start relative overflow-hidden shrink-0 z-10">
                <!-- Graduation Cap Logo -->
                <div class="text-[#0f2a1d] mb-4 transform transition-transform hover:scale-105 duration-300">
                    <x-app-logo-icon class="size-16" />
                </div>
                <!-- Greetings -->
                <h2 class="text-3xl font-extrabold text-white tracking-tight leading-none mb-1.5 font-display">Assalamualaikum</h2>
                <p class="text-[#0f2a1d] text-sm font-medium tracking-wide">Selamat datang di SIPAS-Hub</p>

                <!-- Soft light blob decorative -->
                <div class="absolute -right-8 -bottom-8 w-28 h-28 bg-white/15 rounded-full blur-xl"></div>
            </div>

            <!-- 2. Form Area: White card emerging from below with rounded-t -->
            <div class="bg-[#F0F1EE] px-8 py-8 flex-1 rounded-t-[32px] -mt-5 relative z-10 shadow-[0_-8px_20px_rgba(15,42,29,0.04)]">
                {{ $slot }}
            </div>
        </div>

        <!-- Footer copyright -->
        <div class="text-center text-white/40 text-xs mt-4">
            &copy; {{ date('Y') }} {{ config('app.name', 'SIPAS-Hub') }}
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
