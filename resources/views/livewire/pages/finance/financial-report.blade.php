<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <!-- Header & Breadcrumbs -->
    <flux:header class="flex justify-between items-center">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('dashboard') }}" wire:navigate>{{ __('Keuangan') }}
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Laporan Keuangan') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="flex items-center gap-2">
            <flux:button variant="primary" icon="printer" wire:click="exportPdf">{{ __('Cetak PDF') }}</flux:button>
        </div>
    </flux:header>



    <!-- EXECUTIVE KPI CARDS -->


    <!-- MAIN DATA GRIDS -->
    <div class="space-y-10">

        <!-- SEKSI I: TREN PENERIMAAN KAS -->
        <div class="space-y-4">
            <div>
                <div class="flex items-center gap-2">
                    <flux:icon.chart-bar-square class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    <flux:heading size="lg" class="font-bold">{{ __('Tren Penerimaan Kas') }}</flux:heading>
                </div>
                <flux:text size="sm" class="text-zinc-500">
                    {{ __('Melihat ringkasan pertumbuhan kas masuk secara harian atau bulanan secara interaktif.') }}
                </flux:text>
            </div>

            <!-- Single Card Wrapper for Chart & Trend Table -->
            <flux:card class="p-0 overflow-hidden" 
                       x-data="{ 
                           chart: null,
                           init() {
                               this.render();
                               const observer = new MutationObserver(() => this.render());
                               observer.observe(this.$el, { attributes: true, attributeFilter: ['data-values'] });
                           },
                           render() {
                               const rawData = this.$el.getAttribute('data-values');
                               if (!rawData) return;
                               const trendData = JSON.parse(rawData);
                               const categories = trendData.map(item => item.label);
                               const seriesData = trendData.map(item => item.total_amount);

                               const options = {
                                   chart: {
                                       type: 'area',
                                       height: 320,
                                       fontFamily: 'Plus Jakarta Sans, sans-serif',
                                       toolbar: { show: false },
                                       sparkline: { enabled: false },
                                       zoom: { enabled: false },
                                       animations: {
                                           enabled: true,
                                           easing: 'easeinout',
                                           speed: 500
                                       }
                                   },
                                   colors: ['#4f46e5'],
                                   stroke: { curve: 'smooth', width: 3 },
                                   fill: {
                                       type: 'gradient',
                                       gradient: {
                                           shadeIntensity: 1,
                                           opacityFrom: 0.35,
                                           opacityTo: 0.02,
                                           stops: [0, 90, 100]
                                       }
                                   },
                                   series: [{ name: 'Kas Masuk', data: seriesData }],
                                   xaxis: {
                                       categories: categories,
                                       labels: { style: { colors: '#71717a', fontSize: '11px' } },
                                       axisBorder: { show: false },
                                       axisTicks: { show: false }
                                   },
                                   yaxis: {
                                       labels: {
                                           formatter: function(val) { return 'Rp ' + val.toLocaleString('id-ID'); },
                                           style: { colors: '#71717a', fontSize: '11px' }
                                       }
                                   },
                                   grid: {
                                       borderColor: '#e4e4e7',
                                       strokeDashArray: 4,
                                       xaxis: { lines: { show: false } },
                                       yaxis: { lines: { show: true } }
                                   },
                                   tooltip: {
                                       x: { show: true },
                                       y: { formatter: function(val) { return 'Rp ' + val.toLocaleString('id-ID'); } }
                                   },
                                   markers: {
                                       size: 0,
                                       hover: { size: 5 }
                                   }
                               };

                               if (this.chart) {
                                   this.chart.destroy();
                               }
                               if (window.ApexCharts) {
                                   this.chart = new ApexCharts(this.$refs.canvas, options);
                                   this.chart.render();
                               } else {
                                   const checkInterval = setInterval(() => {
                                       if (window.ApexCharts) {
                                           clearInterval(checkInterval);
                                           this.chart = new ApexCharts(this.$refs.canvas, options);
                                           this.chart.render();
                                       }
                                   }, 100);
                               }
                           }
                       }" data-values="{{ json_encode($this->trendData) }}">
                
                <!-- Card Header with Title and Toggle Inside -->
                <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 flex flex-col sm:flex-row justify-between sm:items-center gap-4 bg-zinc-50/50 dark:bg-zinc-800/10">
                    <div>
                        <flux:heading size="md" class="font-bold">
                            {{ $trendType === 'daily' ? __('Tren Penerimaan Kas Harian') : __('Tren Penerimaan Kas Bulanan') }}
                        </flux:heading>
                        <flux:text size="sm" class="text-zinc-500">
                            {{ __('Visualisasi grafik penerimaan beserta ringkasan data nominal terbaru.') }}
                        </flux:text>
                    </div>
                    <!-- Toggle Harian/Bulanan inside the Card -->
                    <div class="flex items-center gap-1 bg-zinc-200/60 dark:bg-zinc-800 p-0.5 rounded-lg self-start">
                        <button type="button" wire:click="$set('trendType', 'daily')"
                            class="px-3 py-1.5 text-xs font-semibold rounded-md transition duration-150 {{ $trendType === 'daily' ? 'bg-white dark:bg-zinc-700 shadow-sm text-zinc-950 dark:text-white font-bold' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-200' }}">
                            {{ __('Harian') }}
                        </button>
                        <button type="button" wire:click="$set('trendType', 'monthly')"
                            class="px-3 py-1.5 text-xs font-semibold rounded-md transition duration-150 {{ $trendType === 'monthly' ? 'bg-white dark:bg-zinc-700 shadow-sm text-zinc-950 dark:text-white font-bold' : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-200' }}">
                            {{ __('Bulanan') }}
                        </button>
                    </div>
                </div>

                <!-- Grid Content Area -->
                <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-zinc-100 dark:divide-zinc-800">
                    <!-- Column 1 & 2: Chart Canvas (2/3 width) -->
                    <div class="lg:col-span-2 p-6 flex flex-col justify-between">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xs text-zinc-400 uppercase font-semibold block">{{ __('Total Akumulasi Periode Ini') }}</span>
                            <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format(collect($this->trendData)->sum('total_amount'), 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full min-h-[320px]" x-ref="canvas" wire:ignore></div>
                    </div>

                    <!-- Column 3: Table (1/3 width) -->
                    <div class="flex flex-col justify-between">
                        <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/20 dark:bg-zinc-800/5">
                            <flux:heading size="sm" class="font-semibold">{{ $trendType === 'daily' ? __('5 Hari Terakhir') : __('5 Bulan Terakhir') }}</flux:heading>
                        </div>
                        <div class="overflow-y-auto flex-1">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-zinc-500 uppercase bg-zinc-50/50 dark:bg-zinc-800/40 border-b border-zinc-100 dark:border-zinc-800">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">{{ $trendType === 'daily' ? __('Tanggal') : __('Bulan') }}</th>
                                        <th scope="col" class="px-4 py-3 text-right">{{ __('Penerimaan') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                    @forelse($this->trendTable as $row)
                                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20">
                                            <td class="px-4 py-3.5 font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $row['label'] }}</td>
                                            <td class="px-4 py-3.5 text-right font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                                                Rp {{ number_format($row['total_amount'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-zinc-400 py-6">
                                                {{ __('Tidak ada data') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-800/30 border-t border-zinc-100 dark:border-zinc-800 text-xs text-zinc-500 flex justify-between items-center mt-auto">
                            <span>{{ __('Transaksi:') }} <strong>{{ collect($this->trendTable)->sum('count') }}x</strong></span>
                            <span>{{ __('Rata-rata:') }} <strong>Rp {{ number_format(collect($this->trendTable)->avg('total_amount'), 0, ',', '.') }}</strong></span>
                        </div>
                    </div>
                </div>
            </flux:card>

            <!-- Row Baru: SPP vs Non-SPP Ringkasan Pelunasan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Tabel SPP (Pelunasan Bulanan) -->
                <flux:card class="p-0 overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-800/20">
                            <div class="flex items-center gap-2">
                                <flux:icon.banknotes class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                <flux:heading size="md" class="font-semibold">{{ __('Ikhtisar SPP Bulanan') }}</flux:heading>
                            </div>
                            <span class="text-xs text-zinc-400 font-semibold">{{ __('Target vs Realisasi') }}</span>
                        </div>
                        <div class="overflow-x-auto">
                            <flux:table>
                                <flux:table.columns>
                                    <flux:table.column>{{ __('Bulan SPP') }}</flux:table.column>
                                    <flux:table.column align="end">{{ __('Target') }}</flux:table.column>
                                    <flux:table.column align="end">{{ __('Lunas') }}</flux:table.column>
                                    <flux:table.column align="end">{{ __('Sisa') }}</flux:table.column>
                                    <flux:table.column align="end">{{ __('Kelancaran') }}</flux:table.column>
                                </flux:table.columns>
                                <flux:table.rows>
                                    @forelse(array_slice($this->sppTrendSummary, 0, 5) as $row)
                                        @if($row['is_future'] ?? false)
                                            <flux:table.row class="bg-indigo-50/20 dark:bg-indigo-950/10">
                                                <flux:table.cell class="font-semibold text-sm flex items-center gap-1.5">
                                                    {{ $row['period'] }}
                                                    <flux:badge size="sm" color="indigo" class="text-[9px] px-1.5 py-0">{{ __('Masa Depan') }}</flux:badge>
                                                </flux:table.cell>
                                                <flux:table.cell align="end" class="font-mono text-xs text-zinc-400">-</flux:table.cell>
                                                <flux:table.cell align="end" class="font-mono text-xs text-indigo-600 dark:text-indigo-400 font-semibold">Rp {{ number_format($row['paid'], 0, ',', '.') }}</flux:table.cell>
                                                <flux:table.cell align="end" class="font-mono text-xs text-zinc-400">-</flux:table.cell>
                                                <flux:table.cell align="end">
                                                    <flux:badge size="sm" color="indigo" variant="outline" class="text-[10px]">
                                                        {{ $row['count_paid'] }} Siswa
                                                    </flux:badge>
                                                </flux:table.cell>
                                            </flux:table.row>
                                        @else
                                            <flux:table.row>
                                                <flux:table.cell class="font-semibold text-sm">{{ $row['period'] }}</flux:table.cell>
                                                <flux:table.cell align="end" class="font-mono text-xs text-zinc-500">Rp {{ number_format($row['target'], 0, ',', '.') }}</flux:table.cell>
                                                <flux:table.cell align="end" class="font-mono text-xs text-emerald-600 dark:text-emerald-400 font-semibold">Rp {{ number_format($row['paid'], 0, ',', '.') }}</flux:table.cell>
                                                <flux:table.cell align="end" class="font-mono text-xs text-rose-600 dark:text-rose-400">Rp {{ number_format($row['unpaid'], 0, ',', '.') }}</flux:table.cell>
                                                <flux:table.cell align="end">
                                                    <flux:badge size="sm" color="{{ $row['rate'] >= 80 ? 'green' : ($row['rate'] >= 50 ? 'yellow' : 'red') }}">
                                                        {{ $row['rate'] }}%
                                                    </flux:badge>
                                                </flux:table.cell>
                                            </flux:table.row>
                                        @endif
                                    @empty
                                        <flux:table.row>
                                            <flux:table.cell colspan="5" class="text-center text-zinc-400 py-6">{{ __('Tidak ada data SPP') }}</flux:table.cell>
                                        </flux:table.row>
                                    @endforelse
                                </flux:table.rows>
                            </flux:table>
                        </div>
                    </div>
                </flux:card>

                <!-- Tabel Non-SPP (Pelunasan per Program) -->
                <flux:card class="p-0 overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-800/20">
                            <div class="flex items-center gap-2">
                                <flux:icon.squares-plus class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                                <flux:heading size="md" class="font-semibold">{{ __('Ikhtisar Tagihan Non-SPP') }}</flux:heading>
                            </div>
                            <span class="text-xs text-zinc-400 font-semibold">{{ __('Target vs Realisasi') }}</span>
                        </div>
                        <div class="overflow-x-auto">
                            <flux:table>
                                <flux:table.columns>
                                    <flux:table.column>{{ __('Nama Tagihan') }}</flux:table.column>
                                    <flux:table.column align="end">{{ __('Target') }}</flux:table.column>
                                    <flux:table.column align="end">{{ __('Lunas') }}</flux:table.column>
                                    <flux:table.column align="end">{{ __('Sisa') }}</flux:table.column>
                                    <flux:table.column align="end">{{ __('Kelancaran') }}</flux:table.column>
                                </flux:table.columns>
                                <flux:table.rows>
                                    @forelse(array_slice($this->nonSppTrendSummary, 0, 5) as $row)
                                        <flux:table.row>
                                            <flux:table.cell class="font-semibold text-sm truncate max-w-[150px]">{{ $row['name'] }}</flux:table.cell>
                                            <flux:table.cell align="end" class="font-mono text-xs text-zinc-500">Rp {{ number_format($row['target'], 0, ',', '.') }}</flux:table.cell>
                                            <flux:table.cell align="end" class="font-mono text-xs text-emerald-600 dark:text-emerald-400 font-semibold">Rp {{ number_format($row['paid'], 0, ',', '.') }}</flux:table.cell>
                                            <flux:table.cell align="end" class="font-mono text-xs text-rose-600 dark:text-rose-400">Rp {{ number_format($row['unpaid'], 0, ',', '.') }}</flux:table.cell>
                                            <flux:table.cell align="end">
                                                <flux:badge size="sm" color="{{ $row['rate'] >= 80 ? 'green' : ($row['rate'] >= 50 ? 'yellow' : 'red') }}">
                                                    {{ $row['rate'] }}%
                                                </flux:badge>
                                            </flux:table.cell>
                                        </flux:table.row>
                                    @empty
                                        <flux:table.row>
                                            <flux:table.cell colspan="5" class="text-center text-zinc-400 py-6">{{ __('Tidak ada data Non-SPP') }}</flux:table.cell>
                                        </flux:table.row>
                                    @endforelse
                                </flux:table.rows>
                            </flux:table>
                        </div>
                    </div>
                </flux:card>
            </div>
        </div>

        <hr class="border-zinc-200 dark:border-zinc-800 my-6" />

        <!-- RINCIAN TRANSAKSI (LEDGER) -->
        <div class="space-y-4">
            <div>
                <div class="flex items-center gap-2">
                    <flux:icon.list-bullet class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    <flux:heading size="lg" class="font-bold">
                        {{ __('Rincian Transaksi Penerimaan Kas (Ledger)') }}</flux:heading>
                </div>
                <flux:text size="sm" class="text-zinc-500">
                    {{ __('Daftar riwayat transaksi masuk secara terperinci berdasarkan penyaringan di samping.') }}
                </flux:text>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Kolom Kiri: Filter Ledger (1/3 space) -->
                <flux:card class="p-6 space-y-4 bg-zinc-50/50 dark:bg-zinc-800/10 h-fit">
                    <flux:heading size="md" class="font-semibold border-b border-zinc-100 dark:border-zinc-800 pb-2">
                        {{ __('Filter Rincian Transaksi') }}
                    </flux:heading>

                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <flux:input type="date" wire:model.live="startDate" label="{{ __('Dari Tanggal') }}" />
                            </div>
                            <div>
                                <flux:input type="date" wire:model.live="endDate" label="{{ __('Sampai Tanggal') }}" />
                            </div>
                        </div>

                        <div>
                            <flux:select wire:model.live="selectedClass" label="{{ __('Kelas / Tingkat') }}">
                                <flux:select.option value="all">{{ __('Semua Kelas') }}</flux:select.option>
                                @foreach($this->classFilterOptions as $option)
                                    <flux:select.option value="{{ $option['value'] }}" class="{{ $option['is_grade'] ? 'font-bold text-indigo-600' : 'pl-4' }}">
                                        {{ $option['is_grade'] ? $option['label'] : '  ' . $option['label'] }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        <div>
                            <flux:select wire:model.live="category" label="{{ __('Kategori Tagihan') }}">
                                <flux:select.option value="all">{{ __('Semua Kategori') }}</flux:select.option>
                                <flux:select.option value="SPP">{{ __('SPP Bulanan') }}</flux:select.option>
                                <flux:select.option value="Non-SPP">{{ __('Tagihan Non-SPP') }}</flux:select.option>
                            </flux:select>
                        </div>

                        <div>
                            <flux:select wire:model.live="paymentMethod" label="{{ __('Metode Pembayaran') }}">
                                <flux:select.option value="all">{{ __('Semua Metode') }}</flux:select.option>
                                <flux:select.option value="midtrans">{{ __('Online (Midtrans)') }}</flux:select.option>
                                <flux:select.option value="manual">{{ __('Tunai / Kasir') }}</flux:select.option>
                            </flux:select>
                        </div>

                        <div>
                            <flux:input type="text" wire:model.live.debounce.500ms="search"
                                placeholder="{{ __('Cari Nama, NIS, Kwitansi...') }}" icon="magnifying-glass" label="{{ __('Kata Kunci Pencarian') }}" />
                        </div>
                    </div>

                    <hr class="border-zinc-200 dark:border-zinc-800 my-4" />

                    <div>
                        <flux:button type="button" wire:click="resetFilters" variant="ghost" class="w-full justify-center">
                            {{ __('Reset Filter') }}
                        </flux:button>
                    </div>
                </flux:card>

                <!-- Kolom Kanan: Tabel Ledger (2/3 space) -->
                <div class="lg:col-span-2">
                    <flux:card class="p-0 overflow-hidden">
                        <div class="overflow-x-auto relative min-h-[300px]">
                            <div wire:loading.flex
                                wire:target="startDate, endDate, category, paymentMethod, search, gotoPage, previousPage, nextPage"
                                class="absolute inset-0 bg-white/50 dark:bg-zinc-900/50 backdrop-blur-sm z-10 flex items-center justify-center">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-8 h-8 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                                    <span class="text-sm font-medium text-slate-600 dark:text-zinc-300">{{ __('Memuat data...') }}</span>
                                </div>
                            </div>

                            <flux:table>
                                <flux:table.columns>
                                    <flux:table.column>{{ __('Waktu Transaksi') }}</flux:table.column>
                                    <flux:table.column>{{ __('No. Kwitansi') }}</flux:table.column>
                                    <flux:table.column>{{ __('Siswa') }}</flux:table.column>
                                    <flux:table.column>{{ __('Rincian Tagihan') }}</flux:table.column>
                                    <flux:table.column>{{ __('Metode') }}</flux:table.column>
                                    <flux:table.column align="end">{{ __('Nominal Masuk') }}</flux:table.column>
                                    <flux:table.column align="end">{{ __('Aksi') }}</flux:table.column>
                                </flux:table.columns>
                                <flux:table.rows>
                                    @forelse($this->ledger as $payment)
                                        <flux:table.row :key="$payment->id">
                                            <flux:table.cell class="text-xs text-zinc-500 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y H:i') }}
                                            </flux:table.cell>
                                            <flux:table.cell class="font-mono text-xs uppercase">
                                                {{ $payment->receipt_number ?? '-' }}</flux:table.cell>
                                            <flux:table.cell>
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-sm text-slate-800 dark:text-zinc-200">{{ $payment->invoice->student->name ?? 'N/A' }}</span>
                                                    <span class="text-xs text-zinc-500">{{ __('Kelas:') }} {{ $payment->invoice->student->class ?? '-' }}</span>
                                                </div>
                                            </flux:table.cell>
                                            <flux:table.cell>{{ $payment->invoice->billing_detail ?? 'Tagihan' }}</flux:table.cell>
                                            <flux:table.cell>
                                                @if (in_array(strtolower($payment->method), ['manual', 'cash']))
                                                    <flux:badge size="sm" color="zinc">{{ __('Tunai/Kasir') }}</flux:badge>
                                                @else
                                                    <flux:badge size="sm" color="blue">{{ __('Online') }}</flux:badge>
                                                @endif
                                            </flux:table.cell>
                                            <flux:table.cell align="end" class="font-mono tabular-nums text-emerald-600 font-bold">
                                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                            </flux:table.cell>
                                            <flux:table.cell align="end" class="flex gap-2 justify-end">
                                                @if($payment->proof_file)
                                                    <flux:button size="sm" variant="ghost" icon="photo" wire:click="viewProof({{ $payment->id }})">
                                                        {{ __('Bukti') }}
                                                    </flux:button>
                                                @endif
                                                <flux:button size="sm" variant="ghost" icon="printer" as="a"
                                                    href="{{ route('finance.payments.receipt', $payment) }}" target="_blank">
                                                    {{ __('Cetak') }}</flux:button>
                                            </flux:table.cell>
                                        </flux:table.row>
                                    @empty
                                        <flux:table.row>
                                            <flux:table.cell colspan="7" class="text-center text-zinc-400 py-8">
                                                <flux:icon.inbox class="w-12 h-12 mx-auto mb-3 text-zinc-300" />
                                                {{ __('Tidak ditemukan transaksi yang sesuai dengan filter.') }}
                                            </flux:table.cell>
                                        </flux:table.row>
                                    @endforelse
                                </flux:table.rows>
                            </flux:table>
                        </div>
                        <div class="p-4 border-t border-zinc-100 dark:border-zinc-800">
                            {{ $this->ledger->links() }}
                        </div>
                    </flux:card>
                </div>
            </div>
        </div>

        <hr class="border-zinc-200 dark:border-zinc-800 my-6" />

        <!-- CETAK LAPORAN KEUANGAN -->
        <div class="space-y-4">
            <div>
                <div class="flex items-center gap-2">
                    <flux:icon.printer class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    <flux:heading size="lg" class="font-bold">
                        {{ __('Cetak Laporan Keuangan') }}</flux:heading>
                </div>
                <flux:text size="sm" class="text-zinc-500">
                    {{ __('Sesuaikan kriteria cetak di bawah ini untuk melihat pratinjau halaman laporan secara instan sebelum diunduh.') }}
                </flux:text>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Kolom Kiri: Filter Khusus Cetak (1/3 space) -->
                <flux:card class="p-6 space-y-4 bg-zinc-50/50 dark:bg-zinc-800/10 h-fit">
                    <flux:heading size="md" class="font-semibold border-b border-zinc-100 dark:border-zinc-800 pb-2">
                        {{ __('Kriteria Cetak Laporan') }}
                    </flux:heading>

                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <flux:input type="date" wire:model.live="printStartDate" label="{{ __('Dari Tanggal') }}" />
                            </div>
                            <div>
                                <flux:input type="date" wire:model.live="printEndDate" label="{{ __('Sampai Tanggal') }}" />
                            </div>
                        </div>

                        <div>
                            <flux:select wire:model.live="printSelectedClass" label="{{ __('Kelas / Tingkat') }}">
                                <flux:select.option value="all">{{ __('Semua Kelas') }}</flux:select.option>
                                @foreach($this->classFilterOptions as $option)
                                    <flux:select.option value="{{ $option['value'] }}" class="{{ $option['is_grade'] ? 'font-bold text-indigo-600' : 'pl-4' }}">
                                        {{ $option['is_grade'] ? $option['label'] : '  ' . $option['label'] }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        <div>
                            <flux:select wire:model.live="printCategory" label="{{ __('Kategori Laporan') }}">
                                <flux:select.option value="all">{{ __('Semua Kategori') }}</flux:select.option>
                                <flux:select.option value="SPP">{{ __('Khusus SPP') }}</flux:select.option>
                                <flux:select.option value="Non-SPP">{{ __('Khusus Non-SPP') }}</flux:select.option>
                            </flux:select>
                        </div>

                        <div>
                            <flux:select wire:model.live="printPaymentMethod" label="{{ __('Metode Transaksi') }}">
                                <flux:select.option value="all">{{ __('Semua Metode') }}</flux:select.option>
                                <flux:select.option value="midtrans">{{ __('Online (Midtrans)') }}</flux:select.option>
                                <flux:select.option value="manual">{{ __('Tunai / Kasir') }}</flux:select.option>
                            </flux:select>
                        </div>

                        <div>
                            <flux:input type="text" wire:model.live.debounce.500ms="printSearch"
                                placeholder="{{ __('Cari Nama, NIS, Kwitansi...') }}" icon="magnifying-glass" label="{{ __('Cari Transaksi Spesifik') }}" />
                        </div>
                    </div>

                    <hr class="border-zinc-200 dark:border-zinc-800 my-4" />

                    <div class="space-y-3">
                        <flux:button variant="primary" icon="printer" wire:click="exportPdf" class="w-full justify-center py-2.5 shadow-md">
                            {{ __('Unduh PDF Laporan Resmi') }}
                        </flux:button>
                        <flux:text size="2xs" class="text-center text-zinc-400 block leading-relaxed">
                            {{ __('Menghasilkan berkas PDF A4 resmi dengan format rekapitulasi, kriteria filter aktif, lampiran rincian ledger, dan tanda tangan.') }}
                        </flux:text>
                    </div>
                </flux:card>

                <!-- Kolom Kanan: Live Pratinjau Kertas Laporan (2/3 space) -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex justify-between items-center pb-2 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="flex gap-2">
                            <flux:button size="sm" variant="{{ $printPreviewTab === 'summary' ? 'filled' : 'ghost' }}" wire:click="$set('printPreviewTab', 'summary')" class="text-xs">
                                {{ __('Halaman 1: Ringkasan & Kategori') }}
                            </flux:button>
                            <flux:button size="sm" variant="{{ $printPreviewTab === 'details' ? 'filled' : 'ghost' }}" wire:click="$set('printPreviewTab', 'details')" class="text-xs">
                                {{ __('Halaman 2: Lampiran Rincian') }}
                            </flux:button>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs text-zinc-400 font-semibold">{{ __('Live Pratinjau Kertas') }}</span>
                        </div>
                    </div>

                    <!-- Kertas Simulai A4 -->
                    <div class="bg-white dark:bg-zinc-950 text-slate-800 dark:text-zinc-100 rounded-lg shadow-inner border border-zinc-200 dark:border-zinc-800/80 p-8 min-h-[600px] flex flex-col justify-between font-sans leading-relaxed text-xs overflow-x-auto relative">
                        <div wire:loading.flex
                            wire:target="printStartDate, printEndDate, printSelectedClass, printCategory, printPaymentMethod, printSearch, printPreviewTab"
                            class="absolute inset-0 bg-white/75 dark:bg-zinc-950/75 backdrop-blur-xs z-10 flex items-center justify-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-8 h-8 border-4 border-emerald-200 border-t-emerald-600 rounded-full animate-spin"></div>
                                <span class="text-xs font-semibold text-slate-600 dark:text-zinc-300">{{ __('Memperbarui pratinjau...') }}</span>
                            </div>
                        </div>

                        <div>
                            <!-- Header Laporan -->
                            <div class="text-center border-b-2 border-slate-800 dark:border-zinc-200 pb-3 mb-4">
                                <h2 class="text-sm font-black tracking-wider uppercase text-slate-900 dark:text-zinc-50">
                                    {{ __('LAPORAN KEUANGAN PENERIMAAN KAS') }}
                                </h2>
                                <span class="text-3xs text-zinc-400 font-semibold tracking-widest block uppercase mt-0.5">{{ __('PestPay Financial Reporting System') }}</span>
                                <p class="text-2xs text-zinc-500 mt-1 font-medium">
                                    {{ __('Periode:') }} <strong>{{ \Carbon\Carbon::parse($printStartDate)->translatedFormat('d M Y') }}</strong> {{ __('s.d') }} <strong>{{ \Carbon\Carbon::parse($printEndDate)->translatedFormat('d M Y') }}</strong>
                                </p>
                            </div>

                            @if($printPreviewTab === 'summary')
                                <!-- TAB 1: RINGKASAN REKAPITULASI -->
                                <div class="space-y-6">
                                    <!-- Kotak Ringkasan KPI -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 border border-zinc-200 dark:border-zinc-800 rounded bg-zinc-50/50 dark:bg-zinc-900/30 text-center divide-y md:divide-y-0 md:divide-x divide-zinc-200 dark:divide-zinc-800">
                                        <div class="p-3">
                                            <span class="text-3xs text-zinc-400 font-bold uppercase tracking-wider block mb-1">{{ __('Total Pemasukan Kas') }}</span>
                                            <span class="text-sm font-black text-emerald-600 dark:text-emerald-400 font-mono block">
                                                Rp {{ number_format($this->printKpi['totalInflow'], 0, ',', '.') }}
                                            </span>
                                            <span class="text-3xs text-zinc-400 block mt-0.5">{{ __('Dari') }} {{ $this->printKpi['totalInflowCount'] }} {{ __('transaksi') }}</span>
                                        </div>
                                        <div class="p-3">
                                            <span class="text-3xs text-zinc-400 font-bold uppercase tracking-wider block mb-1">{{ __('Tunggakan Baru Tercipta') }}</span>
                                            <span class="text-sm font-black text-rose-600 dark:text-rose-400 font-mono block">
                                                Rp {{ number_format($this->printKpi['totalNewDebt'], 0, ',', '.') }}
                                            </span>
                                            <span class="text-3xs text-zinc-400 block mt-0.5">{{ __('Dari') }} {{ $this->printKpi['totalNewDebtCount'] }} {{ __('tagihan') }}</span>
                                        </div>
                                        <div class="p-3">
                                            <span class="text-3xs text-zinc-400 font-bold uppercase tracking-wider block mb-1">{{ __('Kolektabilitas Lunas') }}</span>
                                            <span class="text-sm font-black text-blue-600 dark:text-blue-400 font-mono block">
                                                {{ $this->printKpi['collectionRate'] }}%
                                            </span>
                                            <span class="text-3xs text-zinc-400 block mt-0.5">{{ __('Rasio pelunasan tagihan') }}</span>
                                        </div>
                                    </div>

                                    <!-- Tabel Rincian per Kategori -->
                                    <div class="space-y-2">
                                        <h3 class="font-bold text-slate-800 dark:text-zinc-200 border-l-2 border-emerald-500 pl-2">
                                            {{ __('Rincian Pemasukan Kas per Kategori') }}
                                        </h3>
                                        <table class="w-full text-2xs border-collapse">
                                            <thead>
                                                <tr class="bg-zinc-100 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 font-bold text-zinc-500">
                                                    <th class="p-2 text-left">{{ __('Nama Kategori') }}</th>
                                                    <th class="p-2 text-right">{{ __('Total Tagihan (Target)') }}</th>
                                                    <th class="p-2 text-right">{{ __('Pemasukan (Lunas)') }}</th>
                                                    <th class="p-2 text-right">{{ __('Sisa Tunggakan') }}</th>
                                                    <th class="p-2 text-right">{{ __('Rate (%)') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900">
                                                @forelse($this->printBreakdown as $row)
                                                    <tr class="hover:bg-zinc-50/55 dark:hover:bg-zinc-900/10">
                                                        <td class="p-2 font-bold">{{ $row['category'] }}</td>
                                                        <td class="p-2 text-right font-mono text-zinc-500">Rp {{ number_format($row['target'], 0, ',', '.') }}</td>
                                                        <td class="p-2 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($row['paid'], 0, ',', '.') }}</td>
                                                        <td class="p-2 text-right font-mono text-rose-600 dark:text-rose-400">Rp {{ number_format($row['unpaid'], 0, ',', '.') }}</td>
                                                        <td class="p-2 text-right font-bold">{{ $row['rate'] }}%</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="p-4 text-center text-zinc-400 italic">
                                                            {{ __('Tidak ada data pada periode ini.') }}
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @elseif($printPreviewTab === 'details')
                                <!-- TAB 2: DETIL TRANSAKSI (LAMPIRAN) -->
                                <div class="space-y-3">
                                    <h3 class="font-bold text-slate-800 dark:text-zinc-200 border-l-2 border-emerald-500 pl-2 mb-2">
                                        {{ __('Lampiran Rincian Transaksi Kas Masuk') }}
                                    </h3>
                                    <table class="w-full text-[10px] border-collapse">
                                        <thead>
                                            <tr class="bg-zinc-100 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 font-bold text-zinc-500">
                                                <th class="p-2 text-center" width="5%">{{ __('No') }}</th>
                                                <th class="p-2 text-left" width="15%">{{ __('Waktu') }}</th>
                                                <th class="p-2 text-left" width="15%">{{ __('No. Kwitansi') }}</th>
                                                <th class="p-2 text-left" width="22%">{{ __('Siswa') }}</th>
                                                <th class="p-2 class-left" width="10%">{{ __('Kelas') }}</th>
                                                <th class="p-2 text-left" width="23%">{{ __('Rincian') }}</th>
                                                <th class="p-2 text-right" width="15%">{{ __('Nominal') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-900">
                                            @forelse($this->printPayments as $index => $payment)
                                                <tr class="hover:bg-zinc-50/55 dark:hover:bg-zinc-900/10">
                                                    <td class="p-2 text-center text-zinc-400">{{ $index + 1 }}</td>
                                                    <td class="p-2 text-zinc-500">{{ \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y H:i') }}</td>
                                                    <td class="p-2 font-mono text-3xs uppercase text-zinc-600 dark:text-zinc-300">{{ $payment->receipt_number ?? '-' }}</td>
                                                    <td class="p-2">
                                                        <div class="font-bold">{{ $payment->invoice->student->name ?? 'N/A' }}</div>
                                                        <div class="text-[9px] text-zinc-400">{{ $payment->invoice->student->nis ?? '-' }}</div>
                                                    </td>
                                                    <td class="p-2 text-zinc-500">{{ $payment->invoice->student->schoolClass->name ?? '-' }}</td>
                                                    <td class="p-2 text-zinc-500 truncate max-w-[150px]">{{ $payment->invoice->billing_detail ?? 'Tagihan' }}</td>
                                                    <td class="p-2 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="p-8 text-center text-zinc-400 italic">
                                                        {{ __('Tidak ada riwayat transaksi masuk pada kriteria filter.') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        @if(count($this->printPayments) > 0)
                                            <tfoot>
                                                <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-t border-zinc-200 dark:border-zinc-800 font-bold text-slate-900 dark:text-zinc-100">
                                                    <td colspan="6" class="p-2 text-right uppercase tracking-wider text-2xs">{{ __('Total Kas Masuk Terkumpul') }}</td>
                                                    <td class="p-2 text-right font-mono text-sm text-emerald-600 dark:text-emerald-400">
                                                        Rp {{ number_format(collect($this->printPayments)->sum('amount'), 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        @endif
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Tanda Tangan (Hanya muncul jika Summary tab aktif, mensimulasikan Halaman 1) -->
                        @if($printPreviewTab === 'summary')
                            <div class="grid grid-cols-2 text-center mt-12 text-zinc-500 pt-6 border-t border-dashed border-zinc-100 dark:border-zinc-800/80">
                                <div>
                                    <p>{{ __('Mengetahui,') }}</p>
                                    <p class="font-bold text-slate-800 dark:text-zinc-200 mt-0.5">{{ __('Kepala Sekolah') }}</p>
                                    <div class="h-10"></div>
                                    <p class="text-zinc-300">________________________</p>
                                </div>
                                <div>
                                    <p>{{ __('Dibuat Oleh,') }}</p>
                                    <p class="font-bold text-slate-800 dark:text-zinc-200 mt-0.5">{{ __('Admin Keuangan') }}</p>
                                    <div class="h-10"></div>
                                    <p class="text-zinc-300">________________________</p>
                                </div>
                            </div>
                        @else
                            <div class="text-right text-[10px] text-zinc-400 pt-6 border-t border-dashed border-zinc-100 dark:border-zinc-800/80">
                                {{ __('Dicetak pada:') }} {{ now()->translatedFormat('d F Y H:i') }} | {{ __('Sistem Pelaporan PestPay') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    <!-- Modal: View Proof Image -->
    <flux:modal wire:model="showProofModal" class="max-w-2xl">
        <div class="flex flex-col gap-6">
            <div class="flex justify-between items-start">
                <flux:heading size="lg">{{ __('Foto Bukti Transfer') }}</flux:heading>
                <flux:modal.close>
                    <flux:icon.x-mark class="w-5 h-5 text-zinc-400 hover:text-zinc-600 cursor-pointer" />
                </flux:modal.close>
            </div>
            
            <div class="flex justify-center bg-zinc-50 dark:bg-zinc-900 rounded-xl p-4 border border-zinc-100 dark:border-zinc-800">
                @if($proofFileUrl)
                    <img src="{{ $proofFileUrl }}" class="max-w-full max-h-[60vh] object-contain rounded-xl shadow-xs" alt="Bukti Transfer Berkas" />
                @else
                    <div class="py-12 flex flex-col items-center justify-center text-zinc-400">
                        <flux:icon.photo class="w-12 h-12 mb-2 opacity-50" />
                        <p>{{ __('Gambar bukti tidak tersedia.') }}</p>
                    </div>
                @endif
            </div>

            <div class="flex justify-between items-center">
                @if($proofFileUrl)
                    <flux:button as="a" :href="$proofFileUrl" download target="_blank" icon="arrow-down-tray" variant="ghost">
                        {{ __('Unduh Gambar') }}
                    </flux:button>
                @else
                    <div></div>
                @endif
                <flux:button class="px-6" variant="primary" wire:click="$set('showProofModal', false)">{{ __('Tutup') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush
