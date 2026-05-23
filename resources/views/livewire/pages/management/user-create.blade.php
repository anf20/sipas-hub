<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('management.users.index') }}" wire:navigate>{{ __('Manajemen Pengguna') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Tambah Pengguna') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </flux:header>

    <flux:main>
        <form wire:submit="save" class="space-y-6 max-w-2xl">
            <flux:card>
                <div class="space-y-6">
                    <flux:heading size="lg">{{ __('Informasi Pengguna') }}</flux:heading>

                    <flux:input wire:model="name" label="{{ __('Nama Lengkap') }}" placeholder="Contoh: Admin Keuangan" required />
                    
                    <flux:input type="email" wire:model="email" label="{{ __('Email') }}" placeholder="admin@example.com" required />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input type="password" wire:model="password" label="{{ __('Password') }}" required />
                        <flux:input type="password" wire:model="password_confirmation" label="{{ __('Konfirmasi Password') }}" required />
                    </div>

                    <flux:fieldset>
                        <flux:legend>{{ __('Pilih Role') }}</flux:legend>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($roles as $role)
                                <flux:checkbox wire:model="selected_roles" :value="$role->name" :label="$role->name" />
                            @endforeach
                        </div>
                        <flux:error name="selected_roles" />
                    </flux:fieldset>

                    <div class="flex items-center gap-4">
                        <flux:button type="submit" variant="primary">{{ __('Simpan Pengguna') }}</flux:button>
                        <flux:button :href="route('management.users.index')" variant="ghost" wire:navigate>{{ __('Batal') }}</flux:button>
                    </div>
                </div>
            </flux:card>
        </form>
    </flux:main>
</div>
