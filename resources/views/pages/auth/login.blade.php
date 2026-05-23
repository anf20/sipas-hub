<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-8">
        <x-auth-header :title="__('Selamat Datang')" :description="__('Silakan masuk ke akun SIPAS-Hub Anda')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <div class="flex flex-col gap-5">
                <!-- Email Address -->
                <flux:input
                    name="email"
                    :label="__('Alamat Email')"
                    :value="old('email')"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@contoh.com"
                />

                <!-- Password -->
                <div class="flex flex-col gap-2">
                    <div class="flex justify-between items-center px-0.5">
                        <flux:label>{{ __('Kata Sandi') }}</flux:label>
                        @if (Route::has('password.request'))
                            <flux:link class="text-xs" :href="route('password.request')" wire:navigate>
                                {{ __('Lupa kata sandi?') }}
                            </flux:link>
                        @endif
                    </div>
                    <flux:input
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        :placeholder="__('Kata Sandi')"
                        viewable
                    />
                </div>
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Ingat saya')" :checked="old('remember')" />

            <div class="mt-2">
                <flux:button variant="primary" type="submit" class="w-full h-12 text-base font-semibold" data-test="login-button">
                    {{ __('Masuk') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="mt-2 py-4 px-6 rounded-2xl bg-surface-container-low text-sm text-center text-on-surface-variant border border-outline-variant/30">
                <span>{{ __('Belum punya akun?') }}</span>
                <flux:link :href="route('register')" wire:navigate class="font-bold text-primary hover:underline ml-1">{{ __('Daftar sekarang') }}</flux:link>
            </div>
        @endif
    </div>
</x-layouts::auth>
