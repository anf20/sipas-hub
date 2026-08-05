<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light theme-admin">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-background text-on-background">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                @unlessrole('Orang Tua')
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                @endunlessrole

                @hasanyrole(['Super Admin', 'Admin Akademik'])
                <flux:sidebar.group :heading="__('Akademik')" class="grid">
                    <flux:sidebar.item icon="building-library" :href="route('academic.dashboard')" :current="request()->routeIs('academic.dashboard')" wire:navigate>
                        {{ __('Dashboard Akademik') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('academic.students.index')" :current="request()->routeIs('academic.students.*')" wire:navigate>
                        {{ __('Data Siswa') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="building-office-2" :href="route('academic.classes.index')" :current="request()->routeIs('academic.classes.*')" wire:navigate>
                        {{ __('Manajemen Kelas') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="calendar-days" :href="route('academic.years.index')" :current="request()->routeIs('academic.years.*')" wire:navigate>
                        {{ __('Tahun Ajaran') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                @endhasanyrole

                @hasanyrole(['Super Admin', 'Admin Keuangan'])
                <flux:sidebar.group :heading="__('Keuangan')" class="grid">
                    <flux:sidebar.item icon="chart-bar-square" :href="route('finance.hub')" :current="request()->routeIs('finance.hub')" wire:navigate>
                        {{ __('Laporan Keuangan') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="banknotes" :href="route('finance.spp.index')" :current="request()->routeIs('finance.spp.*')" wire:navigate>
                        {{ __('Manajemen SPP') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="document-text" :href="route('finance.fee-types.index')" :current="request()->routeIs('finance.fee-types.index')" wire:navigate>
                        {{ __('Tagihan Lainnya') }}
                    </flux:sidebar.item>
                    <!-- Reports and Audit commented out since we don't have separate pages for them yet or they were removed -->
                    <!--
                    <flux:sidebar.item icon="clipboard-document-list" :href="route('finance.hub', ['tab' => 'reports'])" :current="request()->routeIs('finance.hub') && request('tab') === 'reports'" wire:navigate>
                        {{ __('Laporan Keuangan') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="shield-check" :href="route('finance.hub', ['tab' => 'audit'])" :current="request()->routeIs('finance.hub') && request('tab') === 'audit'" wire:navigate>
                        {{ __('Log Audit') }}
                    </flux:sidebar.item>
                    -->
                    <flux:sidebar.item icon="banknotes" :href="route('finance.invoice.manual-payment')" :current="request()->routeIs('finance.invoice.manual-payment')" wire:navigate>
                        {{ __('Pembayaran Manual') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chat-bubble-left-right" :href="route('finance.whatsapp-broadcast.general')" :current="request()->routeIs('finance.whatsapp-broadcast.general')" wire:navigate>
                        {{ __('Broadcast Pengumuman') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                @endhasanyrole

                @hasrole('Orang Tua')
                <flux:sidebar.group :heading="__('Portal Orang Tua')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('parent.dashboard')" :current="request()->routeIs('parent.dashboard')" wire:navigate>
                        {{ __('Dashboard Orang Tua') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="credit-card" :href="route('parent.invoices')" :current="request()->routeIs('parent.invoices')" wire:navigate>
                        {{ __('Tagihan Anak') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="clock" href="#" wire:navigate>
                        {{ __('Riwayat Pembayaran') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                @endrole

                @hasrole('Super Admin')
                <flux:sidebar.group :heading="__('Sistem')" class="grid">
                    <flux:sidebar.item icon="user-group" :href="route('management.users.index')" :current="request()->routeIs('management.users.*')" wire:navigate>
                        {{ __('Manajemen Pengguna') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="arrow-path" :href="route('management.recovery')" :current="request()->routeIs('management.recovery')" wire:navigate>
                        {{ __('Pusat Pemulihan') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="shield-check" :href="route('management.audit_logs')" :current="request()->routeIs('management.audit_logs')" wire:navigate>
                        {{ __('Log Audit') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="bell-alert" :href="route('settings.notifications')" :current="request()->routeIs('settings.notifications')" wire:navigate>
                        {{ __('Pusat Notifikasi') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                @endhasrole
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Desktop Top Navigation Bar -->
        <flux:header class="hidden lg:flex border-b border-forest-light-sage/10 bg-transparent px-8 py-4 justify-between items-center w-full">
            <!-- Search Bar -->
            <div class="relative w-80">
                <span class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                    <flux:icon.magnifying-glass class="size-4 text-forest-text-muted" />
                </span>
                <input type="text" placeholder="Cari data..." class="w-full ps-10 pe-4 py-2 bg-white border border-forest-light-sage/30 rounded-full text-sm text-forest-text-main placeholder-forest-text-muted focus:outline-none focus:ring-2 focus:ring-forest-sage" />
            </div>

            <flux:spacer />

            <!-- Actions & Profile -->
            <div class="flex items-center gap-4">
                <!-- Notification Icon -->
                <flux:button variant="ghost" size="sm" class="relative bg-white border border-forest-light-sage/30 rounded-full p-2.5 hover:bg-forest-surface hover:border-forest-light-sage/50 cursor-pointer">
                    <flux:icon.bell class="size-5 text-forest-text-main" />
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-forest-danger rounded-full ring-2 ring-white"></span>
                </flux:button>

                <!-- User Profile Dropdown -->
                <flux:dropdown position="bottom" align="end">
                    <button class="flex items-center gap-2 bg-white border border-forest-light-sage/30 rounded-full px-4 py-1.5 hover:bg-forest-surface hover:border-forest-light-sage/50 transition duration-150 cursor-pointer">
                        <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" size="xs" class="bg-forest-sage text-white" />
                        <span class="text-sm font-medium text-forest-text-main">{{ auth()->user()->name }}</span>
                        <flux:icon.chevron-down class="size-3 text-forest-text-muted" />
                    </button>
                    <flux:menu class="min-w-48">
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                        <flux:menu.separator />
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item
                                as="button"
                                type="submit"
                                icon="arrow-right-start-on-rectangle"
                                class="w-full cursor-pointer text-red-600 hover:text-red-700"
                            >
                                {{ __('Log out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </flux:header>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden bg-forest-surface border-b border-forest-light-sage/20 px-4">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
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
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
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
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
        @stack('scripts')
    </body>
</html>
