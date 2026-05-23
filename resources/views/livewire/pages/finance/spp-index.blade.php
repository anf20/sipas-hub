<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:heading size="xl">{{ __('Manajemen SPP Bulanan') }}</flux:heading>
    </flux:header>

    <flux:main>
        <div class="space-y-6">
             <div class="flex justify-between items-center bg-blue-50 dark:bg-blue-900/20 p-6 rounded-xl border border-blue-100 dark:border-blue-800">
                 <div>
                     <flux:heading size="lg" class="text-blue-900 dark:text-blue-100">{{ __('Generate SPP Massal') }}</flux:heading>
                     <flux:text class="text-blue-700 dark:text-blue-300 mt-1">{{ __('Buat tagihan SPP untuk seluruh siswa aktif secara otomatis dalam satu kali klik.') }}</flux:text>
                 </div>
                 
                 <flux:modal.trigger name="generate-spp-modal">
                     <flux:button variant="primary" icon="sparkles" class="bg-blue-600 hover:bg-blue-700 text-white border-none shadow-sm">{{ __('Generate SPP Sekarang') }}</flux:button>
                 </flux:modal.trigger>
             </div>

             <!-- Modal Generate SPP -->
             <flux:modal name="generate-spp-modal" class="min-w-[28rem]">
                 <form wire:submit.prevent="generateSpp" class="space-y-6">
                     <div>
                         <flux:heading size="lg">{{ __('Generate SPP Bulanan') }}</flux:heading>
                         <flux:subheading>{{ __('Sistem akan membuatkan invoice untuk semua siswa aktif.') }}</flux:subheading>
                     </div>

                     <div class="space-y-4">
                         <div class="grid grid-cols-2 gap-4">
                             <div class="space-y-2">
                                 <flux:select wire:model="month" label="{{ __('Periode Bulan') }}" required>
                                     @foreach($months as $num => $name)
                                         <flux:select.option value="{{ $num }}">{{ $name }}</flux:select.option>
                                     @endforeach
                                 </flux:select>
                                 <flux:error name="month" />
                             </div>
                             <div class="space-y-2">
                                 <flux:input type="number" wire:model="year" label="{{ __('Tahun') }}" required />
                                 <flux:error name="year" />
                             </div>
                         </div>

                         <div class="space-y-2">
                             <flux:input type="number" wire:model="default_amount" label="{{ __('Nominal SPP (Rp)') }}" prefix="Rp" required />
                             <flux:error name="default_amount" />
                         </div>

                         <div class="space-y-2">
                             <flux:input type="date" wire:model="due_date" label="{{ __('Tanggal Jatuh Tempo') }}" required />
                             <flux:error name="due_date" />
                         </div>
                     </div>

                     <div class="flex gap-2">
                         <flux:spacer />
                         <flux:modal.close>
                             <flux:button variant="ghost">{{ __('Batal') }}</flux:button>
                         </flux:modal.close>
                         <flux:button type="submit" variant="primary" wire:loading.attr="disabled">{{ __('Eksekusi Generate') }}</flux:button>
                     </div>
                 </form>
             </flux:modal>
             
             <!-- Daftar Riwayat SPP -->
             <flux:card class="p-0 overflow-hidden">
                 <div class="p-6 border-b border-zinc-100 dark:border-zinc-800">
                     <flux:heading size="lg">{{ __('Riwayat Generate SPP') }}</flux:heading>
                 </div>

                 <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('Nama Tagihan') }}</flux:table.column>
                        <flux:table.column>{{ __('Nominal Dasar') }}</flux:table.column>
                        <flux:table.column>{{ __('Tanggal Generate') }}</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($sppBatches as $batch)
                            <flux:table.row :key="$batch->id">
                                <flux:table.cell font-weight="medium">{{ $batch->name }}</flux:table.cell>
                                <flux:table.cell class="font-mono tabular-nums">Rp {{ number_format($batch->default_amount, 0, ',', '.') }}</flux:table.cell>
                                <flux:table.cell>{{ $batch->created_at->format('d M Y H:i') }}</flux:table.cell>
                                <flux:table.cell align="end">
                                    <div class="flex gap-2 justify-end">
                                        <flux:button variant="ghost" size="sm" icon="eye" :href="route('finance.fee-types.show', $batch->id)" wire:navigate>{{ __('Lihat Detail Siswa') }}</flux:button>
                                        
                                        <flux:modal.trigger name="delete-spp-{{ $batch->id }}">
                                            <flux:button variant="ghost" size="sm" icon="trash" class="text-red-500" />
                                        </flux:modal.trigger>

                                        <flux:modal name="delete-spp-{{ $batch->id }}" class="min-w-[22rem] text-left">
                                            <div class="space-y-6">
                                                <div>
                                                    <flux:heading size="lg">{{ __('Hapus Catatan SPP?') }}</flux:heading>
                                                    <flux:subheading>{{ __('Tindakan ini hanya menghapus master data, pastikan invoice siswa juga dikelola jika ada kesalahan.') }}</flux:subheading>
                                                </div>
                                                <div class="flex gap-2">
                                                    <flux:spacer />
                                                    <flux:modal.close>
                                                        <flux:button variant="ghost">{{ __('Batal') }}</flux:button>
                                                    </flux:modal.close>
                                                    <flux:button variant="danger" wire:click="delete({{ $batch->id }})" wire:loading.attr="disabled">{{ __('Hapus') }}</flux:button>
                                                </div>
                                            </div>
                                        </flux:modal>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                 </flux:table>

                 @if($sppBatches->isEmpty())
                    <div class="py-16 text-center">
                        <flux:icon.banknotes class="mx-auto mb-4 text-zinc-300 dark:text-zinc-600" size="xl" />
                        <flux:text class="text-zinc-500">{{ __('Belum ada riwayat generate SPP bulanan.') }}</flux:text>
                    </div>
                 @endif
             </flux:card>
        </div>
    </flux:main>
</div>
