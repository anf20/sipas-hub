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
                    <flux:table.column>{{ __('Nama Tagihan / Event') }}</flux:table.column>
                    <flux:table.column>{{ __('Kategori') }}</flux:table.column>
                    <flux:table.column>{{ __('Sasaran / Scope') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Total Ditagihkan (Target)') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Pemasukan (Lunas)') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Sisa Tunggakan') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Rate Pelunasan (%)') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Aksi') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($feeTypes as $type)
                        <flux:table.row :key="$type->id">
                            <flux:table.cell font-weight="medium">
                                {{ $type->name }}
                                @if(!$type->is_active)
                                    <flux:badge size="sm" class="ml-2 bg-red-100 text-red-700">Nonaktif</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" class="bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700" inset="top">{{ ucfirst($type->category) }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                @php
                                    $grades = is_string($type->applicable_grades) ? json_decode($type->applicable_grades, true) : $type->applicable_grades;
                                @endphp
                                @if(empty($grades))
                                    <span class="text-sm text-slate-500">{{ __('Semua Siswa') }}</span>
                                @else
                                    <span class="text-sm text-slate-700 dark:text-zinc-300">Kelas {{ implode(', ', $grades) }}</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell align="end" class="font-mono tabular-nums text-slate-700 dark:text-zinc-300">
                                Rp {{ number_format($type->total_paid_amount + $type->total_unpaid_amount, 0, ',', '.') }}
                            </flux:table.cell>
                            <flux:table.cell align="end" class="font-mono tabular-nums text-emerald-600 dark:text-emerald-400 font-medium">
                                Rp {{ number_format($type->total_paid_amount, 0, ',', '.') }}
                            </flux:table.cell>
                            <flux:table.cell align="end" class="font-mono tabular-nums text-rose-600 dark:text-rose-400 font-medium">
                                Rp {{ number_format($type->total_unpaid_amount, 0, ',', '.') }}
                            </flux:table.cell>
                            <flux:table.cell align="end">
                                @php $rate = $type->total_invoices > 0 ? round(($type->paid_invoices / $type->total_invoices) * 100, 1) : 0; @endphp
                                <div class="flex items-center justify-end gap-2">
                                    <span class="text-sm font-medium">{{ $rate }}%</span>
                                    <div class="w-16 bg-slate-200 dark:bg-zinc-700 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-blue-500 h-full" style="width: {{ $rate }}%"></div>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell align="end">
                                <div class="flex gap-2 justify-end">
                                    <flux:button variant="ghost" size="sm" icon="eye" :href="route('finance.fee-types.show', $type->id)" wire:navigate />
                                    <flux:button variant="ghost" size="sm" icon="megaphone" class="text-green-600" :href="route('finance.fee-types.whatsapp-blast', $type->id)" wire:navigate />
                                    <flux:button variant="ghost" size="sm" icon="pencil" :href="route('finance.fee-types.edit', $type->id)" wire:navigate />
                                    
                                    <flux:modal.trigger name="delete-feetype-{{ $type->id }}">
                                        <flux:button variant="ghost" size="sm" icon="trash" class="text-red-500" />
                                    </flux:modal.trigger>

                                    <flux:modal name="delete-feetype-{{ $type->id }}" class="min-w-[22rem] text-left">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">{{ __('Hapus Jenis Tagihan?') }}</flux:heading>
                                                <flux:subheading>{{ __('Tindakan ini akan menghapus master data tagihan ini secara permanen.') }}</flux:subheading>
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
