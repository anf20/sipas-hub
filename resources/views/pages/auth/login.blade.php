<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col">
        <!-- Session Status -->
        <x-auth-session-status class="text-center mb-4" :status="session('status')" />

        <div class="mb-6">
            <h3 class="text-2xl font-bold font-display text-[#0f2a1d]">{{ __('Login') }}</h3>
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            <div class="flex flex-col gap-4">
                <!-- Email Address -->
                <flux:input
                    name="email"
                    :label="__('Alamat Email')"
                    :value="old('email')"
                    type="email"
                    icon="envelope"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@contoh.com"
                />

                <!-- Password -->
                <div class="flex flex-col gap-1.5">
                    <flux:input
                        name="password"
                        type="password"
                        icon="lock-closed"
                        :label="__('Kata Sandi')"
                        required
                        autocomplete="current-password"
                        :placeholder="__('Kata Sandi')"
                        viewable
                    />
                    @if (Route::has('password.request'))
                        <div class="flex justify-end px-0.5">
                            <flux:link class="text-xs text-[#6b9071] hover:text-[#527658] font-medium hover:underline font-sans" :href="route('password.request')" wire:navigate>
                                {{ __('Lupa kata sandi?') }}
                            </flux:link>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Ingat saya')" :checked="old('remember')" class="text-[#777775]" />

            <div class="mt-4">
                <button type="submit" class="w-full h-12 text-base font-bold bg-[#0f2a1d] hover:bg-[#3a1d1f] text-white rounded-2xl shadow-md cursor-pointer transition-all duration-200" data-test="login-button">
                    {{ __('Masuk') }}
                </button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="mt-8 text-sm text-center text-[#777775]">
                <span>{{ __('Belum punya akun?') }}</span>
                <flux:link :href="route('register')" wire:navigate class="font-bold text-[#6b9071] hover:text-[#527658] hover:underline ml-1">{{ __('Daftar sekarang') }}</flux:link>
            </div>
        @endif
    </div>
</x-layouts::auth>
