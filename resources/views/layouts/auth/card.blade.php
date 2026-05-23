<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-background antialiased">
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-4 md:p-10">
            <div class="flex w-full max-w-lg flex-col gap-6">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary text-white shadow-xl mb-2">
                        <x-app-logo-icon class="size-10 fill-current" />
                    </span>
                    <span class="text-3xl font-bold text-primary tracking-tight">SIPAS-Hub</span>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>

                <div class="flex flex-col gap-6">
                    <div class="rounded-3xl border border-outline-variant bg-surface-container-lowest shadow-sm overflow-hidden">
                        <div class="px-6 py-10 sm:px-10 md:px-12">{{ $slot }}</div>
                    </div>
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
