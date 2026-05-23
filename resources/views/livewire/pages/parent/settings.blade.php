<div class="flex flex-col gap-huge">
    <!-- Page Title -->
    <section class="flex flex-col gap-tiny px-1">
        <h2 class="font-headline-md text-2xl font-semibold text-primary">{{ __('Pengaturan Akun') }}</h2>
        <p class="font-body-md text-sm text-on-surface-variant">{{ __('Perbarui informasi profil dan kata sandi Anda.') }}</p>
    </section>

    <!-- Profile Information Card -->
    <section class="bg-surface-container-lowest rounded-3xl border border-outline-variant shadow-sm overflow-hidden p-normal flex flex-col gap-large">
        <div class="flex flex-col gap-tiny border-b border-outline-variant/50 pb-normal">
            <h3 class="font-title-md text-lg font-semibold text-primary">{{ __('Informasi Profil') }}</h3>
            <p class="font-caption text-xs text-on-surface-variant">{{ __('Perbarui nama, email, dan nomor telepon akun Anda.') }}</p>
        </div>

        <form wire:submit="updateProfile" class="flex flex-col gap-normal">
            <flux:input wire:model="name" label="{{ __('Nama Lengkap') }}" required />
            <flux:input type="email" wire:model="email" label="{{ __('Alamat Email') }}" required />
            <flux:input wire:model="phone" label="{{ __('Nomor Telepon/WA') }}" />

            <div class="pt-small">
                <flux:button type="submit" variant="primary" class="w-full !rounded-2xl !py-4 shadow-sm active:scale-[0.98] transition-all flex justify-center">
                    {{ __('Simpan Profil') }}
                </flux:button>
            </div>
        </form>
    </section>

    <!-- Password Update Card -->
    <section class="bg-surface-container-lowest rounded-3xl border border-outline-variant shadow-sm overflow-hidden p-normal flex flex-col gap-large">
        <div class="flex flex-col gap-tiny border-b border-outline-variant/50 pb-normal">
            <h3 class="font-title-md text-lg font-semibold text-primary">{{ __('Ubah Kata Sandi') }}</h3>
            <p class="font-caption text-xs text-on-surface-variant">{{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk tetap aman.') }}</p>
        </div>

        <form wire:submit="updatePassword" class="flex flex-col gap-normal">
            <flux:input type="password" wire:model="current_password" label="{{ __('Kata Sandi Saat Ini') }}" viewable required />
            <flux:input type="password" wire:model="password" label="{{ __('Kata Sandi Baru') }}" viewable required />
            <flux:input type="password" wire:model="password_confirmation" label="{{ __('Konfirmasi Kata Sandi') }}" viewable required />

            <div class="pt-small">
                <flux:button type="submit" variant="primary" class="w-full !rounded-2xl !py-4 shadow-sm active:scale-[0.98] transition-all flex justify-center">
                    {{ __('Ubah Kata Sandi') }}
                </flux:button>
            </div>
        </form>
    </section>
</div>
