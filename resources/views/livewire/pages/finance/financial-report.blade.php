<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <!-- Header & Breadcrumbs -->
    <flux:header class="flex justify-between items-center">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('dashboard') }}" wire:navigate>{{ __('Keuangan') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Laporan Keuangan') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        
        <div class="flex items-center gap-2">
            <flux:button variant="primary" icon="printer" wire:click="exportPdf">{{ __('Cetak PDF') }}</flux:button>
        </div>
    </flux:header>

    <!-- TOP FILTER BAR -->
    <flux:card class="p-4 bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div>
                <flux:input type="date" wire:model.live="startDate" label="{{ __('Dari Tanggal') }}" />
            </div>
            <div>
                <flux:input type="date" wire:model.live="endDate" label="{{ __('Sampai Tanggal') }}" />
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
                <flux:input type="text" wire:model.live.debounce.500ms="search" placeholder="{{ __('Cari Nama, NIS, atau No. Kwitansi...') }}" icon="magnifying-glass" />
            </div>
        </div>
    </flux:card>

    <!-- EXECUTIVE KPI CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Penerimaan Kas Lunas -->
        <flux:card class="flex flex-col gap-2 relative overflow-hidden group">
            <div class="absolute right-0 top-0 opacity-5 group-hover:scale-110 transition-transform -mr-4 -mt-4">
                <flux:icon.banknotes class="w-24 h-24 text-emerald-500" />
            </div>
            <flux:text size="sm" class="uppercase font-bold tracking-wider text-emerald-600 dark:text-emerald-400">{{ __('Penerimaan Kas Lunas') }}</flux:text>
            <flux:heading size="xl" class="text-slate-800 dark:text-zinc-100">Rp {{ number_format($this->kpi['penerimaan'], 0, ',', '.') }}</flux:heading>
            
            <div class="flex flex-col mt-1">
                <flux:text size="xs" class="text-zinc-500">{{ __('Total dana masuk sesuai filter') }}</flux:text>
                @if($this->kpi['advance_payment'] > 0)
                    <flux:text size="xs" class="text-blue-600 dark:text-blue-400 font-medium mt-0.5">
                        {{ __('Termasuk Rp ' . number_format($this->kpi['advance_payment'], 0, ',', '.') . ' bayar di muka') }}
                    </flux:text>
                @endif
            </div>
        </flux:card>

        <!-- Tunggakan Jatuh Tempo -->
        <flux:card class="flex flex-col gap-2 relative overflow-hidden group">
            <div class="absolute right-0 top-0 opacity-5 group-hover:scale-110 transition-transform -mr-4 -mt-4">
                <flux:icon.clock class="w-24 h-24 text-rose-500" />
            </div>
            <flux:text size="sm" class="uppercase font-bold tracking-wider text-rose-600 dark:text-rose-400">{{ __('Tunggakan Jatuh Tempo') }}</flux:text>
            <flux:heading size="xl" class="text-slate-800 dark:text-zinc-100">Rp {{ number_format($this->kpi['tunggakan_jatuh_tempo'], 0, ',', '.') }}</flux:heading>
            <flux:text size="xs" class="text-zinc-500">{{ __('Melewati batas waktu pembayaran') }}</flux:text>
        </flux:card>

        <!-- Collection Rate -->
        <flux:card class="flex flex-col gap-2 relative overflow-hidden group">
            <div class="absolute right-0 top-0 opacity-5 group-hover:scale-110 transition-transform -mr-4 -mt-4">
                <flux:icon.chart-bar class="w-24 h-24 text-blue-500" />
            </div>
            <flux:text size="sm" class="uppercase font-bold tracking-wider text-blue-600 dark:text-blue-400">{{ __('Collection Rate') }}</flux:text>
            <flux:heading size="xl" class="text-slate-800 dark:text-zinc-100">{{ $this->kpi['collection_rate'] }}%</flux:heading>
            
            <div class="w-full bg-slate-100 dark:bg-zinc-800 h-1.5 rounded-full overflow-hidden mt-1">
                <div class="bg-blue-500 h-full" style="width: {{ $this->kpi['collection_rate'] }}%"></div>
            </div>
        </flux:card>

        <!-- Tagihan Masa Depan / Inactive -->
        <flux:card class="flex flex-col gap-2 relative overflow-hidden group">
            <div class="absolute right-0 top-0 opacity-5 group-hover:scale-110 transition-transform -mr-4 -mt-4">
                <flux:icon.calendar-days class="w-24 h-24 text-purple-500" />
            </div>
            <flux:text size="sm" class="uppercase font-bold tracking-wider text-purple-600 dark:text-purple-400">{{ __('Tagihan Masa Depan') }}</flux:text>
            <flux:heading size="xl" class="text-slate-800 dark:text-zinc-100">Rp {{ number_format($this->kpi['tagihan_masa_depan'], 0, ',', '.') }}</flux:heading>
            <flux:text size="xs" class="text-zinc-500">{{ __('Piutang dari invoice inactive') }}</flux:text>
        </flux:card>
    </div>

    <!-- MAIN DATA GRIDS -->
    <div class="space-y-6">
        
        <!-- TABEL 1: REKAPITULASI KATEGORI -->
        <flux:card class="p-0 overflow-hidden">
            <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-2">
                <flux:icon.squares-plus class="w-5 h-5 text-indigo-500" />
                <flux:heading size="lg">{{ __('Rekapitulasi Penagihan per Kategori') }}</flux:heading>
            </div>
            <div class="overflow-x-auto">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('Nama Kategori') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Target Ditagihkan') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Realisasi Lunas') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Sisa Tunggakan') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Rate Pelunasan') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($this->rekapKategori as $row)
                            <flux:table.row>
                                <flux:table.cell><strong>{{ $row['name'] }}</strong></flux:table.cell>
                                <flux:table.cell align="end" class="font-mono tabular-nums text-slate-600">Rp {{ number_format($row['target'], 0, ',', '.') }}</flux:table.cell>
                                <flux:table.cell align="end" class="font-mono tabular-nums text-emerald-600 font-medium">Rp {{ number_format($row['paid'], 0, ',', '.') }}</flux:table.cell>
                                <flux:table.cell align="end" class="font-mono tabular-nums text-rose-600 font-medium">Rp {{ number_format($row['unpaid'], 0, ',', '.') }}</flux:table.cell>
                                <flux:table.cell align="end">
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="text-sm font-bold {{ $row['rate'] >= 80 ? 'text-emerald-600' : ($row['rate'] >= 50 ? 'text-amber-500' : 'text-rose-600') }}">{{ $row['rate'] }}%</span>
                                        <div class="w-16 bg-slate-100 dark:bg-zinc-800 h-1.5 rounded-full overflow-hidden">
                                            <div class="h-full {{ $row['rate'] >= 80 ? 'bg-emerald-500' : ($row['rate'] >= 50 ? 'bg-amber-400' : 'bg-rose-500') }}" style="width: {{ $row['rate'] }}%"></div>
                                        </div>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="5" class="text-center text-zinc-400 py-6">{{ __('Tidak ada data tagihan pada periode ini.') }}</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:card>

        <!-- TABEL 2: REKAPITULASI SPP (Only if Category == all or SPP) -->
        @if($category === 'all' || $category === 'SPP')
        <flux:card class="p-0 overflow-hidden">
            <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-2">
                <flux:icon.calendar-days class="w-5 h-5 text-indigo-500" />
                <flux:heading size="lg">{{ __('Rekapitulasi SPP Bulanan') }}</flux:heading>
            </div>
            <div class="overflow-x-auto">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('Periode') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Target SPP') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Realisasi Lunas') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Tunggakan SPP') }}</flux:table.column>
                        <flux:table.column align="center">{{ __('Rasio Siswa Lunas') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Status Kolektabilitas') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($this->rekapSpp as $row)
                            <flux:table.row>
                                <flux:table.cell font-weight="medium">{{ $row['period'] }}</flux:table.cell>
                                <flux:table.cell align="end" class="font-mono tabular-nums text-slate-600">Rp {{ number_format($row['target'], 0, ',', '.') }}</flux:table.cell>
                                <flux:table.cell align="end" class="font-mono tabular-nums text-emerald-600 font-medium">Rp {{ number_format($row['paid'], 0, ',', '.') }}</flux:table.cell>
                                <flux:table.cell align="end" class="font-mono tabular-nums text-rose-600 font-medium">Rp {{ number_format($row['unpaid'], 0, ',', '.') }}</flux:table.cell>
                                <flux:table.cell align="center" class="text-zinc-600 text-sm">{{ $row['ratio'] }}</flux:table.cell>
                                <flux:table.cell align="end">
                                    <flux:badge size="sm" color="{{ $row['rate'] >= 80 ? 'green' : ($row['rate'] >= 50 ? 'yellow' : 'red') }}">{{ $row['rate'] }}%</flux:badge>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="6" class="text-center text-zinc-400 py-6">{{ __('Tidak ada data SPP pada periode ini.') }}</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:card>
        @endif

        <!-- TABEL 3: TRANSACTION LEDGER -->
        <flux:card class="p-0 overflow-hidden">
            <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-2 bg-slate-50 dark:bg-zinc-800/50">
                <flux:icon.list-bullet class="w-5 h-5 text-indigo-500" />
                <flux:heading size="lg">{{ __('Rincian Transaksi Penerimaan Kas (Ledger)') }}</flux:heading>
            </div>
            
            <div class="overflow-x-auto relative min-h-[300px]">
                <div wire:loading.flex wire:target="startDate, endDate, category, paymentMethod, search, gotoPage, previousPage, nextPage" class="absolute inset-0 bg-white/50 dark:bg-zinc-900/50 backdrop-blur-sm z-10 flex items-center justify-center">
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
                                <flux:table.cell class="text-xs text-zinc-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y H:i') }}</flux:table.cell>
                                <flux:table.cell class="font-mono text-xs uppercase">{{ $payment->receipt_number ?? '-' }}</flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-sm text-slate-800 dark:text-zinc-200">{{ $payment->invoice->student->name ?? 'N/A' }}</span>
                                        <span class="text-xs text-zinc-500">{{ __('Kelas:') }} {{ $payment->invoice->student->class ?? '-' }}</span>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>{{ $payment->invoice->billing_detail ?? 'Tagihan' }}</flux:table.cell>
                                <flux:table.cell>
                                    @if(in_array(strtolower($payment->method), ['manual', 'cash']))
                                        <flux:badge size="sm" color="zinc">{{ __('Tunai/Kasir') }}</flux:badge>
                                    @else
                                        <flux:badge size="sm" color="blue">{{ __('Online') }}</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell align="end" class="font-mono tabular-nums text-emerald-600 font-bold">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </flux:table.cell>
                                <flux:table.cell align="end">
                                    <flux:button size="sm" variant="ghost" icon="printer" as="a" href="#" target="_blank">{{ __('Cetak') }}</flux:button>
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
