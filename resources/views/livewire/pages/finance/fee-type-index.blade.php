<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:heading size="xl">{{ __('Data Tagihan') }}</flux:heading>
        <flux:spacer />
        <div class="hidden md:block font-bold text-sm text-slate-700 dark:text-zinc-300">
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
    </flux:header>

    <flux:main>
        <div class="space-y-4">
             <div class="flex justify-between items-center">
                 <div class="flex gap-2">
                     <flux:button :href="route('finance.fee-types.create')" variant="primary" icon="plus" wire:navigate>{{ __('Tambah Tagihan Baru') }}</flux:button>
                     <flux:button :href="route('finance.spp.index')" icon="banknotes" wire:navigate>{{ __('Manajemen SPP') }}</flux:button>
                 </div>
                 <flux:text size="sm">{{ __('Daftar semua jenis tagihan non-SPP.') }}</flux:text>
             </div>
             
             <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Nama Tagihan') }}</flux:table.column>
                    <flux:table.column>{{ __('Kategori') }}</flux:table.column>
                    <flux:table.column>{{ __('Tipe') }}</flux:table.column>
                    <flux:table.column>{{ __('Nominal') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($feeTypes as $type)
                        <flux:table.row :key="$type->id">
                            <flux:table.cell font-weight="medium">{{ $type->name }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" class="bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700" inset="top">{{ ucfirst($type->category) }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $type->is_recurring ? __('Bulanan') : __('Sekali Saja') }}
                            </flux:table.cell>
                            <flux:table.cell>Rp {{ number_format($type->default_amount, 0, ',', '.') }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:switch wire:click="toggleStatus({{ $type->id }})" :checked="$type->is_active" />
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2">
                                    <flux:button variant="ghost" size="sm" icon="eye" :href="route('finance.fee-types.show', $type->id)" wire:navigate />
                                    <flux:button variant="ghost" size="sm" icon="megaphone" class="text-green-600" :href="route('finance.fee-types.whatsapp-blast', $type->id)" wire:navigate />
                                    <flux:button variant="ghost" size="sm" icon="pencil" :href="route('finance.fee-types.edit', $type->id)" wire:navigate />
                                    
                                    <flux:modal.trigger name="delete-feetype-{{ $type->id }}">
                                        <flux:button variant="ghost" size="sm" icon="trash" />
                                    </flux:modal.trigger>

                                    <flux:modal name="delete-feetype-{{ $type->id }}" class="min-w-[22rem]">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">{{ __('Hapus Jenis Tagihan?') }}</flux:heading>
                                                <flux:subheading>{{ __('Tindakan ini akan menghapus master data tagihan ini.') }}</flux:subheading>
                                            </div>
                                            <div class="flex gap-2">
                                                <flux:spacer />
                                                <flux:modal.close>
                                                    <flux:button variant="ghost">{{ __('Batal') }}</flux:button>
                                                </flux:modal.close>
                                                <flux:button variant="danger" wire:click="delete({{ $type->id }})" wire:loading.attr="disabled">{{ __('Hapus') }}</flux:button>
                                            </div>
                                        </div>
                                    </flux:modal>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
             </flux:table>

             @if($feeTypes->isEmpty())
                <div class="py-20 text-center border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-2xl">
                    <flux:icon.document-text class="mx-auto mb-4 text-zinc-300 dark:text-zinc-600" size="xl" />
                    <flux:text class="text-zinc-500">{{ __('Belum ada data tagihan non-SPP.') }}</flux:text>
                    <flux:button :href="route('finance.fee-types.create')" variant="ghost" class="mt-4" wire:navigate>{{ __('Buat Tagihan Pertama') }}</flux:button>
                </div>
             @endif
        </div>
    </flux:main>
</div>
