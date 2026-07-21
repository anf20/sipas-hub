<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('finance.hub') }}" wire:navigate>{{ __('Keuangan') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Manajemen SPP') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:spacer />
        <div class="hidden md:block font-bold text-sm text-slate-700 dark:text-zinc-300">
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
    </flux:header>

    <flux:main>
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <flux:heading size="xl">{{ __('Manajemen SPP Bulanan') }}</flux:heading>
                <div class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 font-bold px-4 py-2 rounded-lg text-sm border border-indigo-100 dark:border-indigo-800">
                    {{ $activeAcademicYearName }}
                </div>
            </div>

            <!-- SPP Metric Highlights (Bulan Lalu) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <flux:card class="flex flex-col gap-1 bg-blue-50/50 dark:bg-blue-950/20 border-blue-100 dark:border-blue-900 shadow-sm">
                    <flux:text size="xs" class="uppercase font-bold text-blue-800 dark:text-blue-400 tracking-wider">{{ __('Total SPP Ditagihkan (Bulan Lalu)') }}</flux:text>
                    <flux:heading size="lg" class="text-blue-900 dark:text-blue-300">Rp {{ number_format($sppLastMonthInvoiced, 0, ',', '.') }}</flux:heading>
                    <flux:text size="sm" class="text-blue-800/70 dark:text-blue-400/70">{{ __('Tersebar di :count Tagihan', ['count' => $sppLastMonthInvoicedCount]) }}</flux:text>
                </flux:card>

                <flux:card class="flex flex-col gap-1 bg-green-50/50 dark:bg-green-950/20 border-green-100 dark:border-green-900 shadow-sm">
                    <flux:text size="xs" class="uppercase font-bold text-green-800 dark:text-green-400 tracking-wider">{{ __('Total SPP Lunas (Bulan Lalu)') }}</flux:text>
                    <flux:heading size="lg" class="text-green-600 dark:text-green-400">Rp {{ number_format($sppLastMonthPaid, 0, ',', '.') }}</flux:heading>
                    <flux:text size="sm" class="text-green-800/70 dark:text-green-400/70">{{ __('Sudah dibayar oleh :count Siswa', ['count' => $sppLastMonthPaidCount]) }}</flux:text>
                </flux:card>

                <flux:card class="flex flex-col gap-1 bg-red-50/50 dark:bg-red-950/20 border-red-100 dark:border-red-900 shadow-sm">
                    <flux:text size="xs" class="uppercase font-bold text-red-800 dark:text-red-400 tracking-wider">{{ __('Total SPP Tunggakan (Bulan Lalu)') }}</flux:text>
                    <flux:heading size="lg" class="text-red-600 dark:text-red-400">Rp {{ number_format($sppLastMonthUnpaid, 0, ',', '.') }}</flux:heading>
                    <flux:text size="sm" class="text-red-800/70 dark:text-red-400/70">{{ __('Belum dibayar oleh :count Siswa', ['count' => $sppLastMonthUnpaidCount]) }}</flux:text>
                </flux:card>
            </div>

            <!-- SPP Metric Highlights (Bulan Ini) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <flux:card class="flex flex-col gap-1 bg-blue-50/50 dark:bg-blue-950/20 border-blue-100 dark:border-blue-900 shadow-sm">
                    <flux:text size="xs" class="uppercase font-bold text-blue-800 dark:text-blue-400 tracking-wider">{{ __('Total SPP Ditagihkan (Bulan Ini)') }}</flux:text>
                    <flux:heading size="lg" class="text-blue-900 dark:text-blue-300">Rp {{ number_format($sppTotalInvoiced, 0, ',', '.') }}</flux:heading>
                    <flux:text size="sm" class="text-blue-800/70 dark:text-blue-400/70">{{ __('Tersebar di :count Tagihan', ['count' => $sppTotalInvoicedCount]) }}</flux:text>
                </flux:card>

                <flux:card class="flex flex-col gap-1 bg-green-50/50 dark:bg-green-950/20 border-green-100 dark:border-green-900 shadow-sm">
                    <flux:text size="xs" class="uppercase font-bold text-green-800 dark:text-green-400 tracking-wider">{{ __('Total SPP Lunas (Bulan Ini)') }}</flux:text>
                    <flux:heading size="lg" class="text-green-600 dark:text-green-400">Rp {{ number_format($sppTotalPaid, 0, ',', '.') }}</flux:heading>
                    <flux:text size="sm" class="text-green-800/70 dark:text-green-400/70">{{ __('Sudah dibayar oleh :count Siswa', ['count' => $sppTotalPaidCount]) }}</flux:text>
                </flux:card>

                <flux:card class="flex flex-col gap-1 bg-red-50/50 dark:bg-red-950/20 border-red-100 dark:border-red-900 shadow-sm">
                    <flux:text size="xs" class="uppercase font-bold text-red-800 dark:text-red-400 tracking-wider">{{ __('Total SPP Tunggakan (Bulan Ini)') }}</flux:text>
                    <flux:heading size="lg" class="text-red-600 dark:text-red-400">Rp {{ number_format($sppTotalUnpaid, 0, ',', '.') }}</flux:heading>
                    <flux:text size="sm" class="text-red-800/70 dark:text-red-400/70">{{ __('Belum dibayar oleh :count Siswa', ['count' => $sppTotalUnpaidCount]) }}</flux:text>
                </flux:card>
            </div>



            <!-- Modal Generate SPP -->
            <flux:modal name="generate-spp-modal" class="min-w-[28rem]">
                <form wire:submit.prevent="generateSpp" class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('Mulai Tahun Ajaran Baru') }}</flux:heading>
                        <flux:subheading>{{ __('Sistem akan membuatkan invoice untuk 12 bulan (Juli - Juni) ke depan untuk semua siswa aktif.') }}</flux:subheading>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <flux:input type="number" wire:model="year" label="{{ __('Tahun Mulai (Juli)') }}" required />
                            <flux:error name="year" />
                        </div>

                        <div class="space-y-2">
                            <flux:input type="number" wire:model="default_amount" label="{{ __('Nominal SPP per Bulan (Rp)') }}" prefix="Rp" required />
                            <flux:error name="default_amount" />
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
            
            <!-- Modal Sesuaikan Nominal SPP -->
            <flux:modal name="adjust-spp-modal" class="min-w-[28rem]">
                <form wire:submit.prevent="adjustSppAmount" class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('Sesuaikan Nominal SPP') }}</flux:heading>
                        <flux:subheading>{{ __('Perubahan ini HANYA akan berlaku untuk tagihan SPP di masa depan dan yang belum lunas pada tahun ajaran yang sedang aktif.') }}</flux:subheading>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <flux:input type="number" wire:model="adjust_amount" label="{{ __('Nominal Baru (Rp)') }}" prefix="Rp" required />
                            <flux:error name="adjust_amount" />
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <flux:spacer />
                        <flux:modal.close>
                            <flux:button variant="ghost">{{ __('Batal') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled">{{ __('Simpan Perubahan') }}</flux:button>
                    </div>
                </form>
            </flux:modal>
            
            <!-- Daftar Riwayat SPP -->
            <flux:card class="p-0 overflow-hidden">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 border-b border-zinc-100 dark:border-zinc-800 gap-4">
                    <div class="flex items-center gap-4">
                        <flux:heading size="lg">{{ __('SPP Bulanan') }}</flux:heading>
                        <flux:radio.group wire:model.live="viewMode" variant="segmented" size="sm">
                            <flux:radio value="current_month" label="{{ __('Hingga Bulan Ini') }}" />
                            <flux:radio value="full_year" label="{{ __('1 Tahun Penuh') }}" />
                        </flux:radio.group>
                    </div>
                    <div class="flex gap-2 items-center">
                        <flux:modal.trigger name="adjust-spp-modal">
                            <flux:button variant="subtle" icon="currency-dollar">{{ __('Sesuaikan Nominal SPP') }}</flux:button>
                        </flux:modal.trigger>
                        <flux:modal.trigger name="generate-spp-modal">
                            <flux:button variant="primary">{{ __('Mulai Tahun Ajaran Baru') }}</flux:button>
                        </flux:modal.trigger>
                    </div>
                </div>

                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('Bulan & Tahun') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Total Ditagihkan') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Pemasukan SPP') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Tunggakan SPP') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Rate Tagihan') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Aksi') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($sppMonthlyTable as $row)
                            <flux:table.row :key="$row->period_year . '-' . $row->period_month">
                                <flux:table.cell font-weight="medium">
                                    {{ \Carbon\Carbon::create()->month($row->period_month)->translatedFormat('F') }} {{ $row->period_year }}
                                </flux:table.cell>
                                <flux:table.cell align="end" class="font-mono tabular-nums text-slate-700 dark:text-zinc-300">
                                    Rp {{ number_format($row->total_paid + $row->total_unpaid, 0, ',', '.') }}
                                </flux:table.cell>
                                <flux:table.cell align="end" class="font-mono tabular-nums text-emerald-600 dark:text-emerald-400 font-medium">
                                    Rp {{ number_format($row->total_paid, 0, ',', '.') }}
                                </flux:table.cell>
                                <flux:table.cell align="end" class="font-mono tabular-nums text-rose-600 dark:text-rose-400 font-medium">
                                    Rp {{ number_format($row->total_unpaid, 0, ',', '.') }}
                                </flux:table.cell>
                                <flux:table.cell align="end">
                                    @php $rate = $row->count_total > 0 ? round(($row->count_paid / $row->count_total) * 100, 1) : 0; @endphp
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="text-sm font-medium">{{ $rate }}%</span>
                                        <div class="w-16 bg-slate-200 dark:bg-zinc-700 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-blue-500 h-full" style="width: {{ $rate }}%"></div>
                                        </div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell align="end">
                                    <div class="flex gap-2 justify-end">
                                        <flux:button variant="ghost" size="sm" icon="eye" :href="route('finance.spp.months.show', $row->period_month)" wire:navigate>{{ __('Lihat Detail') }}</flux:button>
                                        
                                        @if($sppBatches->isNotEmpty())
                                            <flux:button variant="ghost" size="sm" icon="megaphone" class="text-green-600" :href="route('finance.fee-types.whatsapp-blast', $sppBatches->first()->id)" wire:navigate>{{ __('Umumkan') }}</flux:button>
                                        @endif
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
                
                <div class="p-4 border-t border-zinc-100 dark:border-zinc-800">
                    {{ $sppMonthlyTable->links() }}
                </div>
            </flux:card>
        </div>
    </flux:main>
</div>
