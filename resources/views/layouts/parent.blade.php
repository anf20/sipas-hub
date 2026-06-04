<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    @include('partials.head')
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"
            data-navigate-track></script>
    <style>
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
            <flux:icon.academic-cap variant="solid" class="size-6 text-primary" />
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
            <flux:icon.squares-2x2 :variant="request()->routeIs('parent.dashboard') ? 'solid' : 'outline'" class="size-6" />
            <span class="font-label-bold text-[10px]">{{ __('Dashboard') }}</span>
        </a>
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('parent.invoices') ? 'bg-primary-container text-on-primary-container rounded-xl px-4 py-1.5 scale-90' : 'text-on-surface-variant px-4 py-1.5 hover:text-primary' }} transition-all duration-200" href="{{ route('parent.invoices') }}" wire:navigate>
            <flux:icon.document-text :variant="request()->routeIs('parent.invoices') ? 'solid' : 'outline'" class="size-6" />
            <span class="font-label-bold text-[10px]">{{ __('Tagihan') }}</span>
        </a>
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('parent.history') ? 'bg-primary-container text-on-primary-container rounded-xl px-4 py-1.5 scale-90' : 'text-on-surface-variant px-4 py-1.5 hover:text-primary' }} transition-all duration-200" href="{{ route('parent.history') }}" wire:navigate>
            <flux:icon.clock :variant="request()->routeIs('parent.history') ? 'solid' : 'outline'" class="size-6" />
            <span class="font-label-bold text-[10px]">{{ __('Riwayat') }}</span>
        </a>
        <a class="flex flex-col items-center justify-center {{ request()->routeIs('parent.students') ? 'bg-primary-container text-on-primary-container rounded-xl px-4 py-1.5 scale-90' : 'text-on-surface-variant px-4 py-1.5 hover:text-primary' }} transition-all duration-200" href="{{ route('parent.students') }}" wire:navigate>
            <flux:icon.users :variant="request()->routeIs('parent.students') ? 'solid' : 'outline'" class="size-6" />
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
            Livewire.on('show-snap-popup', (data) => {
                console.log('Global Midtrans Listener: Event Received', data);
                
                // Extract snapToken from possible wrapped formats
                let snapToken = null;
                
                if (typeof data === 'string') {
                    snapToken = data;
                } else if (data && data.snapToken) {
                    snapToken = data.snapToken;
                } else if (Array.isArray(data) && data[0] && data[0].snapToken) {
                    snapToken = data[0].snapToken;
                } else if (data && typeof data === 'object' && Object.keys(data).length > 0) {
                    // Try to find snapToken in any key if nested
                    snapToken = data.snapToken || Object.values(data)[0]?.snapToken;
                }

                console.log('Extracted Snap Token:', snapToken);
                
                if (!snapToken) {
                    console.error('Snap Token missing in event data:', data);
                    alert('Gagal mendapatkan token pembayaran. Silakan muat ulang halaman.');
                    return;
                }

                if (typeof window.snap === 'undefined') {
                    console.error('Midtrans Snap.js is not loaded.');
                    alert('Sistem pembayaran sedang tidak tersedia. Silakan muat ulang halaman.');
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
                        alert('Terjadi kesalahan saat memproses pembayaran.');
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
