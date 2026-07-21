<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('finance.hub') }}" wire:navigate>{{ __('Keuangan') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Ringkasan & SPP') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:spacer />
        <div class="hidden md:block font-bold text-sm text-slate-700 dark:text-zinc-300">
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
    </flux:header>

    <flux:main>
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <flux:heading size="xl">{{ __('Manajemen Keuangan') }}</flux:heading>
            </div>

            <!-- KARTU OVERVIEW UTAMA (Ringkasan Keuangan Tahun Ajaran) -->
            <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xs">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <flux:heading size="lg" class="text-slate-800 dark:text-zinc-200 font-semibold">{{ __('Ringkasan Keuangan SPP') }} - TA {{ $activeStartYear }}/{{ $activeEndYear }}</flux:heading>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <!-- Metrik 1 -->
                    <div class="flex flex-col gap-1 p-4 bg-white dark:bg-zinc-800 border border-slate-100 dark:border-zinc-700 rounded-xl shadow-sm">
                        <flux:text size="xs" class="uppercase font-bold text-slate-500 tracking-wider">{{ __('Total Proyeksi Piutang SPP') }}</flux:text>
                        <flux:heading size="lg" class="text-slate-800 dark:text-zinc-200">Rp {{ number_format($proyeksiPiutangBulanIni, 0, ',', '.') }}</flux:heading>
                    </div>
                    <!-- Metrik 2 -->
                    <div class="flex flex-col gap-1 p-4 bg-white dark:bg-zinc-800 border border-slate-100 dark:border-zinc-700 rounded-xl shadow-sm">
                        <flux:text size="xs" class="uppercase font-bold text-slate-500 tracking-wider">{{ __('Tingkat Pelunasan SPP') }}</flux:text>
                        <div class="flex items-end gap-2">
                            <flux:heading size="lg" class="text-slate-800 dark:text-zinc-200">{{ $sppCollectionRateCard }}%</flux:heading>
                        </div>
                    </div>
                    <!-- Metrik 3 -->
                    <div class="flex flex-col gap-1 p-4 bg-white dark:bg-zinc-800 border border-slate-100 dark:border-zinc-700 rounded-xl shadow-sm">
                        <flux:text size="xs" class="uppercase font-bold text-slate-500 tracking-wider">{{ __('Total Tunggakan Non-SPP') }}</flux:text>
                        <flux:heading size="lg" class="text-rose-600 dark:text-rose-400">Rp {{ number_format($tunggakanNonSpp, 0, ',', '.') }}</flux:heading>
                    </div>
                </div>

                <!-- Progress Bar & Rincian -->
                <div class="pt-6 border-t border-slate-100 dark:border-zinc-800">
                    <div class="flex justify-between items-end mb-2">
                        <div>
                            <flux:heading size="md" class="text-slate-800 dark:text-zinc-200">{{ __('Progres Penagihan SPP TA Ini') }}</flux:heading>
                            <flux:text size="sm" class="text-slate-500">{{ __('Berdasarkan tagihan aktif') }}</flux:text>
                        </div>
                        <div class="text-right">
                            <flux:heading size="md" class="text-slate-800 dark:text-zinc-200">Rp {{ number_format($boxTotalLunas, 0, ',', '.') }}</flux:heading>
                            <flux:text size="sm" class="text-emerald-600 font-medium">{{ __('Terkumpul dari :count tagihan', ['count' => $boxTotalLunasCount]) }}</flux:text>
                        </div>
                    </div>

                    <div class="w-full bg-slate-100 dark:bg-zinc-800 h-3 rounded-full overflow-hidden my-4">
                        <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $boxTotalDitagihkan > 0 ? ($boxTotalLunas / $boxTotalDitagihkan) * 100 : 0 }}%"></div>
                    </div>

                    <div class="flex justify-between items-center mt-2">
                        <div>
                            <flux:text size="sm" class="text-slate-500">{{ __('Total Target Tagihan SPP') }}</flux:text>
                            <flux:heading size="sm" class="text-slate-700 dark:text-zinc-300">Rp {{ number_format($boxTotalDitagihkan, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">({{ $boxTotalDitagihkanCount }} tagihan)</span></flux:heading>
                        </div>
                        <div class="text-right">
                            <flux:text size="sm" class="text-slate-500">{{ __('Sisa Belum Dibayar') }}</flux:text>
                            <flux:heading size="sm" class="text-rose-600 dark:text-rose-400">Rp {{ number_format($boxTotalTunggakan, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">({{ $boxTotalTunggakanCount }} tagihan)</span></flux:heading>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL TUNGGAKAN NON-SPP AKTIF -->
            <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xs">
                <flux:heading size="lg" class="mb-4 text-slate-800 dark:text-zinc-200">{{ __('Daftar Tagihan Non-SPP Aktif') }}</flux:heading>
                
                @if($activeFeeArrears && count($activeFeeArrears) > 0)
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>{{ __('Tagihan') }}</flux:table.column>
                            <flux:table.column align="end">{{ __('Progress') }}</flux:table.column>
                            <flux:table.column align="end">{{ __('Tunggakan (Rp)') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach($activeFeeArrears as $fee)
                                <flux:table.row>
                                    <flux:table.cell class="font-medium">{{ $fee->name }}</flux:table.cell>
                                    <flux:table.cell align="end">
                                        @php $progress = $fee->total_target > 0 ? round(($fee->paid_target / $fee->total_target) * 100, 1) : 0; @endphp
                                        <div class="flex items-center justify-end gap-2">
                                            <span class="text-sm font-medium">{{ $progress }}%</span>
                                            <div class="w-16 bg-slate-200 dark:bg-zinc-700 h-1.5 rounded-full overflow-hidden">
                                                <div class="bg-indigo-500 h-full" style="width: {{ $progress }}%"></div>
                                            </div>
                                        </div>
                                    </flux:table.cell>
                                    <flux:table.cell align="end" class="font-bold text-rose-600 dark:text-rose-400">Rp {{ number_format($fee->unpaid_amount, 0, ',', '.') }}</flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                @else
                    <div class="text-center py-6 text-slate-500">{{ __('Tidak ada tunggakan Non-SPP aktif saat ini.') }}</div>
                @endif
            </div>
            
        </div>
    </flux:main>
</div>
