<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('finance.hub') }}" wire:navigate>{{ __('Keuangan') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('finance.hub', ['tab' => $feeType->category === 'SPP' ? 'spp' : 'fees']) }}" wire:navigate>
                {{ $feeType->category === 'SPP' ? __('Manajemen SPP') : __('Tagihan Lainnya') }}
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Edit') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </flux:header>

    <flux:main>
        <form wire:submit="save" class="space-y-6 max-w-2xl">
            <flux:card>
                <div class="space-y-6">
                    <flux:heading size="lg">{{ __('Edit Detail Tagihan') }}</flux:heading>

                    @if($isLocked)
                        <flux:card class="bg-yellow-50 border-yellow-200">
                            <div class="flex gap-3 items-center text-yellow-800">
                                <flux:icon icon="lock-closed" size="sm" />
                                <flux:text size="sm" class="text-yellow-800 font-medium">
                                    {{ __('Tagihan ini sudah memiliki transaksi pelunasan. Nominal dan Kategori telah dikunci untuk menjaga integritas data keuangan.') }}
                                </flux:text>
                            </div>
                        </flux:card>
                    @endif

                    <flux:input wire:model="name" label="{{ __('Nama Tagihan') }}" placeholder="Contoh: SPP Bulanan" required />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:select wire:model="category" label="{{ __('Kategori') }}" required :disabled="$isLocked">
                            <flux:select.option value="SPP">{{ __('SPP') }}</flux:select.option>
                            <flux:select.option value="kegiatan">{{ __('Kegiatan') }}</flux:select.option>
                            <flux:select.option value="seragam">{{ __('Seragam') }}</flux:select.option>
                            <flux:select.option value="lain">{{ __('Lain-lain') }}</flux:select.option>
                        </flux:select>

                        <flux:input type="number" wire:model="default_amount" label="{{ __('Nominal Default') }}" prefix="Rp" required :disabled="$isLocked" />
                    </div>

                    <flux:checkbox wire:model="is_active" label="{{ __('Aktif') }}" />

                    <div class="flex items-center gap-4">
                        <flux:button type="submit" variant="primary">{{ __('Simpan Perubahan') }}</flux:button>
                        <flux:button :href="route('finance.fee-types.index')" variant="ghost" wire:navigate>{{ __('Batal') }}</flux:button>
                    </div>
                </div>
            </flux:card>
        </form>
    </flux:main>
</div>
