<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    @include('partials.head')
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"
            data-navigate-track></script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-background text-on-background font-body-md min-h-screen flex flex-col items-center">
    <!-- TopAppBar -->
    <header class="bg-surface-container-lowest border-b border-outline-variant shadow-sm flex justify-between items-center w-full px-normal h-[64px] max-w-[600px] mx-auto sticky top-0 z-50">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">school</span>
            <h1 class="font-display-lg text-lg font-semibold text-primary">SIPAS-Hub</h1>
        </div>
        <div class="flex items-center gap-smallall">
            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                    class="w-10 h-10"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('parent.settings')" icon="cog" wire:navigate>
                            {{ __('Pengaturan Akun') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </div>
    </header>

    <main class="w-full max-w-[600px] px-normal py-large flex flex-col gap-large mb-24">
        {{ $slot }}
    </main>

    <!-- BottomNavBar -->
    <nav class="fixed bottom-0 left-0 right-0 w-full z-50 flex justify-around items-center px-4 py-2 max-w-[600px] mx-auto backdrop-blur-xl border-t border-outline-variant bg-surface-container-lowest/80 shadow-lg rounded-t-xl">
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('parent.dashboard') ? 'bg-primary-container text-on-primary-container rounded-xl px-4 py-1.5 scale-90' : 'text-on-surface-variant px-4 py-1.5 hover:text-primary' }} transition-all duration-200" href="{{ route('parent.dashboard') }}" wire:navigate>
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-label-bold text-[10px]">{{ __('Dashboard') }}</span>
        </a>
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('parent.invoices') ? 'bg-primary-container text-on-primary-container rounded-xl px-4 py-1.5 scale-90' : 'text-on-surface-variant px-4 py-1.5 hover:text-primary' }} transition-all duration-200" href="{{ route('parent.invoices') }}" wire:navigate>
            <span class="material-symbols-outlined">receipt_long</span>
            <span class="font-label-bold text-[10px]">{{ __('Tagihan') }}</span>
        </a>
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('parent.history') ? 'bg-primary-container text-on-primary-container rounded-xl px-4 py-1.5 scale-90' : 'text-on-surface-variant px-4 py-1.5 hover:text-primary' }} transition-all duration-200" href="{{ route('parent.history') }}" wire:navigate>
            <span class="material-symbols-outlined">history</span>
            <span class="font-label-bold text-[10px]">{{ __('Riwayat') }}</span>
        </a>
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('parent.students') ? 'bg-primary-container text-on-primary-container rounded-xl px-4 py-1.5 scale-90' : 'text-on-surface-variant px-4 py-1.5 hover:text-primary' }} transition-all duration-200" href="{{ route('parent.students') }}" wire:navigate>
            <span class="material-symbols-outlined">group</span>
            <span class="font-label-bold text-[10px]">{{ __('Siswa') }}</span>
        </a>
    </nav>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('show-snap-popup', (event) => {
                console.log('Global Midtrans Listener: Event Received', event);
                
                // Livewire 3/4 event parameters can be wrapped in an array or direct object
                const snapToken = event.snapToken || (Array.isArray(event) && event[0].snapToken);
                
                if (!snapToken) {
                    console.error('Snap Token missing in event data:', event);
                    return;
                }

                if (typeof window.snap === 'undefined') {
                    console.error('Midtrans Snap.js is not loaded.');
                    return;
                }

                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        console.log('Payment Success:', result);
                        window.location.href = '/parent/history';
                    },
                    onPending: function(result) {
                        console.log('Payment Pending:', result);
                        window.location.reload();
                    },
                    onError: function(result) {
                        console.error('Payment Error:', result);
                    },
                    onClose: function() {
                        console.log('User closed Midtrans popup');
                    }
                });
            });
        });
    </script>
</body>
</html>
