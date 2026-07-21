<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('academic.hub') }}" wire:navigate>{{ __('Akademik') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('academic.hub', ['tab' => 'years']) }}" wire:navigate>{{ __('Tahun Ajaran') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Edit') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:spacer />
        <div class="hidden md:block font-bold text-sm text-slate-700 dark:text-zinc-300">
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
    </flux:header>

    <flux:main>
        <form wire:submit="save" class="space-y-6 max-w-2xl">
            <flux:card>
                <div class="space-y-6">
                    <flux:heading size="lg">{{ __('Edit Detail Tahun Ajaran') }}</flux:heading>

                    <flux:input wire:model="name" label="{{ __('Nama Tahun Ajaran') }}" placeholder="Contoh: 2024/2025" required />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input type="date" wire:model="start_date" label="{{ __('Tanggal Mulai') }}" required />
                        <flux:input type="date" wire:model="end_date" label="{{ __('Tanggal Selesai') }}" required />
                    </div>

                    <flux:checkbox wire:model="is_active" label="{{ __('Set sebagai Tahun Ajaran Aktif') }}" />

                    <div class="flex items-center gap-4">
                        <flux:button type="submit" variant="primary">{{ __('Simpan Perubahan') }}</flux:button>
                        <flux:button :href="route('academic.years.index')" variant="ghost" wire:navigate>{{ __('Batal') }}</flux:button>
                    </div>
                </div>
            </flux:card>
        </form>
    </flux:main>
</div>
