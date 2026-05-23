<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('finance.hub') }}" wire:navigate>{{ __('Keuangan') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>
                @if($tab === 'overview') {{ __('Ringkasan') }}
                @elseif($tab === 'spp') {{ __('Manajemen SPP') }}
                @elseif($tab === 'fees') {{ __('Tagihan Lainnya') }}
                @elseif($tab === 'reports') {{ __('Laporan') }}
                @elseif($tab === 'audit') {{ __('Log Audit') }}
                @endif
            </flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </flux:header>

    <flux:main>
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <flux:heading size="xl">{{ __('Manajemen Keuangan') }}</flux:heading>
            </div>

            <flux:navlist variant="outline" class="flex-row gap-2 border-b border-zinc-200 dark:border-zinc-700 pb-0">
                <flux:navlist.item wire:click="$set('tab', 'overview')" :current="$tab === 'overview'" class="cursor-pointer">{{ __('Ringkasan') }}</flux:navlist.item>
                <flux:navlist.item wire:click="$set('tab', 'spp')" :current="$tab === 'spp'" class="cursor-pointer">{{ __('Manajemen SPP') }}</flux:navlist.item>
                <flux:navlist.item wire:click="$set('tab', 'fees')" :current="$tab === 'fees'" class="cursor-pointer">{{ __('Tagihan Lainnya') }}</flux:navlist.item>
                <flux:navlist.item wire:click="$set('tab', 'reports')" :current="$tab === 'reports'" class="cursor-pointer">{{ __('Laporan') }}</flux:navlist.item>
                <flux:navlist.item wire:click="$set('tab', 'audit')" :current="$tab === 'audit'" class="cursor-pointer">{{ __('Log Audit') }}</flux:navlist.item>
            </flux:navlist>

            <div class="mt-4">
                @if($tab === 'overview')
                    <div class="space-y-6">
                        <!-- Main Stat Cards - BACKGROUND COLOR DESIGN -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <flux:card class="flex flex-col gap-1 bg-green-50/50 dark:bg-green-950/20 border-green-100 dark:border-green-900">
                                <flux:text size="xs" class="uppercase font-bold text-green-800 dark:text-green-400 tracking-wider">{{ __('Pemasukan Bulan Ini') }}</flux:text>
                                <flux:heading size="lg" class="text-green-900 dark:text-green-300">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}</flux:heading>
                                <flux:text size="xs" color="{{ $revenueThisMonth >= $revenueLastMonth ? 'green' : 'red' }}">
                                    {{ $revenueThisMonth >= $revenueLastMonth ? '↑' : '↓' }} vs bln lalu (Rp {{ number_format($revenueLastMonth, 0, ',', '.') }})
                                </flux:text>
                            </flux:card>

                            <flux:card class="flex flex-col gap-1 bg-red-50/50 dark:bg-red-950/20 border-red-100 dark:border-red-900">
                                <flux:text size="xs" class="uppercase font-bold text-red-800 dark:text-red-400 tracking-wider">{{ __('Total Tunggakan') }}</flux:text>
                                <flux:heading size="lg" class="text-red-600 dark:text-red-400">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</flux:heading>
                                <flux:text size="xs" class="text-red-800/70 dark:text-red-400/70">{{ __('Seluruh kategori tagihan') }}</flux:text>
                            </flux:card>

                            <flux:card class="flex flex-col gap-1 bg-blue-50/50 dark:bg-blue-950/20 border-blue-100 dark:border-blue-900">
                                <flux:text size="xs" class="uppercase font-bold text-blue-800 dark:text-blue-400 tracking-wider">{{ __('Rate Penagihan') }}</flux:text>
                                <flux:heading size="lg" class="text-blue-900 dark:text-blue-300">{{ $collectionRate }}%</flux:heading>
                                <div class="w-full bg-blue-200 dark:bg-zinc-700 h-1.5 rounded-full overflow-hidden mt-1">
                                    <div class="bg-blue-600 h-full" style="width: {{ $collectionRate }}%"></div>
                                </div>
                            </flux:card>

                            <flux:card class="flex flex-col gap-1 bg-zinc-50/50 dark:bg-zinc-900/50 border-zinc-100 dark:border-zinc-800">
                                <flux:text size="xs" class="uppercase font-bold text-zinc-800 dark:text-zinc-400 tracking-wider">{{ __('Total Terbayar (All Time)') }}</flux:text>
                                <flux:heading size="lg" class="text-zinc-900 dark:text-zinc-300">Rp {{ number_format($revenueThisMonth + $revenueLastMonth, 0, ',', '.') }}</flux:heading>
                                <flux:text size="xs" class="text-zinc-500">{{ __('Akumulasi pembayaran lunas') }}</flux:text>
                            </flux:card>
                        </div>

                        <!-- SPP Summary Banner -->
                        <flux:card class="bg-blue-50 dark:bg-blue-900/20 border-blue-100 dark:border-blue-800 space-y-4 shadow-sm">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div>
                                    <flux:heading size="lg" class="text-blue-900 dark:text-blue-300">{{ __('Ringkasan Performa SPP') }}</flux:heading>
                                    <flux:subheading class="text-blue-800/70 dark:text-blue-400/70">{{ __('Statistik penagihan dan pelunasan khusus kategori SPP Bulanan.') }}</flux:subheading>
                                </div>
                                <div class="text-right">
                                    <flux:text size="sm" class="text-blue-800 dark:text-blue-400 font-medium">{{ __('Collection Rate SPP') }}</flux:text>
                                    <flux:heading size="xl" class="text-blue-700 dark:text-blue-400">{{ $sppCollectionRate }}%</flux:heading>
                                </div>
                            </div>
                            
                            <div class="w-full bg-blue-200/50 dark:bg-zinc-700 h-3 rounded-full overflow-hidden">
                                <div class="bg-blue-600 h-full transition-all duration-500" style="width: {{ $sppCollectionRate }}%"></div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                                <div class="flex flex-col">
                                    <flux:text size="xs" class="text-blue-800/70 dark:text-blue-400/70 uppercase font-bold tracking-wider">{{ __('Total Ditagihkan') }}</flux:text>
                                    <flux:text size="lg" font-weight="semibold" class="text-blue-900 dark:text-blue-200">Rp {{ number_format($sppTotalInvoiced, 0, ',', '.') }} <span class="text-xs font-normal opacity-70">({{ $sppTotalInvoicedCount }} {{ __('Tagihan') }})</span></flux:text>
                                </div>
                                <div class="flex flex-col border-l border-blue-200/50 dark:border-blue-800 pl-4">
                                    <flux:text size="xs" class="text-green-700 dark:text-green-400 uppercase font-bold tracking-wider">{{ __('Total Lunas') }}</flux:text>
                                    <flux:text size="lg" font-weight="semibold" class="text-green-600">Rp {{ number_format($sppTotalPaid, 0, ',', '.') }} <span class="text-xs font-normal opacity-70">({{ $sppTotalPaidCount }} {{ __('Siswa') }})</span></flux:text>
                                </div>
                                <div class="flex flex-col border-l border-blue-200/50 dark:border-blue-800 pl-4">
                                    <flux:text size="xs" class="text-red-700 dark:text-red-400 uppercase font-bold tracking-wider">{{ __('Sisa Tunggakan') }}</flux:text>
                                    <flux:text size="lg" font-weight="semibold" class="text-red-600">Rp {{ number_format($sppTotalUnpaid, 0, ',', '.') }} <span class="text-xs font-normal opacity-70">({{ $sppTotalUnpaidCount }} {{ __('Siswa') }})</span></flux:text>
                                </div>
                            </div>
                        </flux:card>

                        <!-- Other Fees Summary Banner -->
                        <flux:card class="bg-indigo-50 dark:bg-indigo-900/20 border-indigo-100 dark:border-indigo-800 space-y-4 shadow-sm">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div>
                                    <flux:heading size="lg" class="text-indigo-900 dark:text-indigo-300">{{ __('Ringkasan Performa Tagihan Lainnya') }}</flux:heading>
                                    <flux:subheading class="text-indigo-800/70 dark:text-indigo-400/70">{{ __('Statistik penagihan dan pelunasan untuk kategori Non-SPP (Seragam, Kegiatan, dll).') }}</flux:subheading>
                                </div>
                                <div class="text-right">
                                    <flux:text size="sm" class="text-indigo-800 dark:text-indigo-400 font-medium">{{ __('Collection Rate') }}</flux:text>
                                    <flux:heading size="xl" class="text-indigo-700 dark:text-indigo-400">{{ $otherCollectionRate }}%</flux:heading>
                                </div>
                            </div>
                            
                            <div class="w-full bg-indigo-200/50 dark:bg-zinc-700 h-3 rounded-full overflow-hidden">
                                <div class="bg-indigo-600 h-full transition-all duration-500" style="width: {{ $otherCollectionRate }}%"></div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                                <div class="flex flex-col">
                                    <flux:text size="xs" class="text-indigo-800/70 dark:text-indigo-400/70 uppercase font-bold tracking-wider">{{ __('Total Ditagihkan') }}</flux:text>
                                    <flux:text size="lg" font-weight="semibold" class="text-indigo-900 dark:text-indigo-200">Rp {{ number_format($otherTotalInvoiced, 0, ',', '.') }} <span class="text-xs font-normal opacity-70">({{ $otherTotalInvoicedCount }} {{ __('Tagihan') }})</span></flux:text>
                                </div>
                                <div class="flex flex-col border-l border-indigo-200/50 dark:border-indigo-800 pl-4">
                                    <flux:text size="xs" class="text-green-700 dark:text-green-400 uppercase font-bold tracking-wider">{{ __('Total Lunas') }}</flux:text>
                                    <flux:text size="lg" font-weight="semibold" class="text-green-600">Rp {{ number_format($otherTotalPaid, 0, ',', '.') }} <span class="text-xs font-normal opacity-70">({{ $otherTotalPaidCount }} {{ __('Siswa') }})</span></flux:text>
                                </div>
                                <div class="flex flex-col border-l border-indigo-200/50 dark:border-indigo-800 pl-4">
                                    <flux:text size="xs" class="text-red-700 dark:text-red-400 uppercase font-bold tracking-wider">{{ __('Sisa Tunggakan') }}</flux:text>
                                    <flux:text size="lg" font-weight="semibold" class="text-red-600">Rp {{ number_format($otherTotalUnpaid, 0, ',', '.') }} <span class="text-xs font-normal opacity-70">({{ $otherTotalUnpaidCount }} {{ __('Siswa') }})</span></flux:text>
                                </div>
                            </div>
                        </flux:card>

                        <!-- Data Breakdown -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <flux:card class="space-y-4 border-t-2 border-t-zinc-200">
                                <flux:heading size="lg">{{ __('Metode Pembayaran') }}</flux:heading>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            <flux:icon icon="banknotes" class="text-green-600" />
                                            <flux:text>{{ __('Pembayaran Manual (Tunai)') }}</flux:text>
                                        </div>
                                        <flux:text font-weight="medium">Rp {{ number_format($manualPayments, 0, ',', '.') }}</flux:text>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            <flux:icon icon="credit-card" class="text-blue-600" />
                                            <flux:text>{{ __('Pembayaran Online (Gateway)') }}</flux:text>
                                        </div>
                                        <flux:text font-weight="medium">Rp {{ number_format($onlinePayments, 0, ',', '.') }}</flux:text>
                                    </div>
                                </div>
                            </flux:card>

                            <flux:card class="space-y-4 border-t-2 border-t-zinc-200">
                                <flux:heading size="lg">{{ __('Transaksi Terakhir') }}</flux:heading>
                                <flux:table>
                                    <flux:table.rows>
                                        @foreach($recentTransactions->take(5) as $trx)
                                            <flux:table.row>
                                                <flux:table.cell>
                                                    <div class="flex flex-col">
                                                        <span class="text-sm font-medium">{{ $trx->invoice->student->name }}</span>
                                                        <span class="text-xs text-zinc-500">{{ $trx->invoice->feeType->name }}</span>
                                                    </div>
                                                </flux:table.cell>
                                                <flux:table.cell align="end">
                                                    <div class="flex flex-col items-end">
                                                        <span class="text-sm font-bold">Rp {{ number_format($trx->amount, 0, ',', '.') }}</span>
                                                        <span class="text-xs text-zinc-500">{{ $trx->paid_at->format('d/m/y H:i') }}</span>
                                                    </div>
                                                </flux:table.cell>
                                            </flux:table.row>
                                        @endforeach
                                    </flux:table.rows>
                                </flux:table>
                            </flux:card>
                        </div>
                    </div>
                @endif

                @if($tab === 'spp')
                    <div class="space-y-6">
                        <!-- SPP Metric Highlights -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:card class="flex flex-col gap-1 bg-blue-50/50 dark:bg-blue-950/20 border-blue-100 dark:border-blue-900 shadow-sm">
                                <flux:text size="xs" class="uppercase font-bold text-blue-800 dark:text-blue-400 tracking-wider">{{ __('Total SPP Ditagihkan') }}</flux:text>
                                <flux:heading size="lg" class="text-blue-900 dark:text-blue-300">Rp {{ number_format($sppTotalInvoiced, 0, ',', '.') }}</flux:heading>
                                <flux:text size="sm" class="text-blue-800/70 dark:text-blue-400/70">{{ __('Tersebar di :count Tagihan', ['count' => $sppTotalInvoicedCount]) }}</flux:text>
                            </flux:card>

                            <flux:card class="flex flex-col gap-1 bg-green-50/50 dark:bg-green-950/20 border-green-100 dark:border-green-900 shadow-sm">
                                <flux:text size="xs" class="uppercase font-bold text-green-800 dark:text-green-400 tracking-wider">{{ __('Total SPP Lunas') }}</flux:text>
                                <flux:heading size="lg" class="text-green-600 dark:text-green-400">Rp {{ number_format($sppTotalPaid, 0, ',', '.') }}</flux:heading>
                                <flux:text size="sm" class="text-green-800/70 dark:text-green-400/70">{{ __('Sudah dibayar oleh :count Siswa', ['count' => $sppTotalPaidCount]) }}</flux:text>
                            </flux:card>

                            <flux:card class="flex flex-col gap-1 bg-red-50/50 dark:bg-red-950/20 border-red-100 dark:border-red-900 shadow-sm">
                                <flux:text size="xs" class="uppercase font-bold text-red-800 dark:text-red-400 tracking-wider">{{ __('Total SPP Tunggakan') }}</flux:text>
                                <flux:heading size="lg" class="text-red-600 dark:text-red-400">Rp {{ number_format($sppTotalUnpaid, 0, ',', '.') }}</flux:heading>
                                <flux:text size="sm" class="text-red-800/70 dark:text-red-400/70">{{ __('Belum dibayar oleh :count Siswa', ['count' => $sppTotalUnpaidCount]) }}</flux:text>
                            </flux:card>
                        </div>

                        <div class="flex justify-between items-center pt-4">
                            <flux:heading size="lg">{{ __('Riwayat Batch SPP') }}</flux:heading>
                            <flux:modal.trigger name="generate-spp-modal">
                                <flux:button variant="primary" icon="plus">{{ __('Generate SPP') }}</flux:button>
                            </flux:modal.trigger>
                        </div>

                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('Nama Batch') }}</flux:table.column>
                                <flux:table.column>{{ __('Nominal Default') }}</flux:table.column>
                                <flux:table.column>{{ __('Tanggal Dibuat') }}</flux:table.column>
                                <flux:table.column></flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach($sppBatches as $batch)
                                    <flux:table.row :key="'spp-'.$batch->id">
                                        <flux:table.cell font-weight="medium">{{ $batch->name }}</flux:table.cell>
                                        <flux:table.cell>Rp {{ number_format($batch->default_amount, 0, ',', '.') }}</flux:table.cell>
                                        <flux:table.cell>{{ $batch->created_at->format('d/m/Y') }}</flux:table.cell>
                                        <flux:table.cell>
                                            <div class="flex gap-2 justify-end">
                                                <flux:button variant="ghost" size="sm" icon="eye" :href="route('finance.fee-types.show', $batch->id)" wire:navigate />
                                                <flux:button variant="ghost" size="sm" icon="trash" wire:click="deleteFee({{ $batch->id }})" wire:confirm="{{ __('Hapus batch SPP ini?') }}" />
                                            </div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </div>

                    <!-- Modal Generate SPP -->
                    <flux:modal name="generate-spp-modal" class="md:w-[500px]">
                        <form wire:submit="generateSpp" class="space-y-6">
                            <div>
                                <flux:heading size="lg">{{ __('Generate Tagihan SPP Massal') }}</flux:heading>
                                <flux:subheading>{{ __('Tagihan akan dibuat untuk seluruh siswa dengan status AKTIF.') }}</flux:subheading>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <flux:select wire:model="month" label="{{ __('Bulan') }}">
                                    @foreach($months as $num => $name)
                                        <flux:select.option :value="$num">{{ $name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:input wire:model="year" type="number" label="{{ __('Tahun') }}" />
                            </div>

                            <flux:input wire:model="default_amount" type="number" label="{{ __('Nominal SPP (Rp)') }}" placeholder="Contoh: 250000" />
                            <flux:input wire:model="due_date" type="date" label="{{ __('Tanggal Jatuh Tempo') }}" />

                            <div class="flex gap-2 justify-end">
                                <flux:modal.close>
                                    <flux:button variant="ghost">{{ __('Batal') }}</flux:button>
                                </flux:modal.close>
                                <flux:button type="submit" variant="primary">{{ __('Proses Sekarang') }}</flux:button>
                            </div>
                        </form>
                    </flux:modal>
                @endif

                @if($tab === 'fees')
                    <div class="space-y-6">
                        <!-- Other Fees Metric Highlights -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:card class="flex flex-col gap-1 bg-indigo-50/50 dark:bg-indigo-950/20 border-indigo-100 dark:border-indigo-900 shadow-sm">
                                <flux:text size="xs" class="uppercase font-bold text-indigo-800 dark:text-indigo-400 tracking-wider">{{ __('Total Tagihan Lainnya') }}</flux:text>
                                <flux:heading size="lg" class="text-indigo-900 dark:text-indigo-300">Rp {{ number_format($otherTotalInvoiced, 0, ',', '.') }}</flux:heading>
                                <flux:text size="sm" class="text-indigo-800/70 dark:text-indigo-400/70">{{ __('Tersebar di :count Tagihan', ['count' => $otherTotalInvoicedCount]) }}</flux:text>
                            </flux:card>

                            <flux:card class="flex flex-col gap-1 bg-green-50/50 dark:bg-green-950/20 border-green-100 dark:border-green-900 shadow-sm">
                                <flux:text size="xs" class="uppercase font-bold text-green-800 dark:text-green-400 tracking-wider">{{ __('Total Terbayar') }}</flux:text>
                                <flux:heading size="lg" class="text-green-600 dark:text-green-400">Rp {{ number_format($otherTotalPaid, 0, ',', '.') }}</flux:heading>
                                <flux:text size="sm" class="text-green-800/70 dark:text-green-400/70">{{ __('Sudah dibayar oleh :count Siswa', ['count' => $otherTotalPaidCount]) }}</flux:text>
                            </flux:card>

                            <flux:card class="flex flex-col gap-1 bg-red-50/50 dark:bg-red-950/20 border-red-100 dark:border-red-900 shadow-sm">
                                <flux:text size="xs" class="uppercase font-bold text-red-800 dark:text-red-400 tracking-wider">{{ __('Sisa Tunggakan') }}</flux:text>
                                <flux:heading size="lg" class="text-red-600 dark:text-red-400">Rp {{ number_format($otherTotalUnpaid, 0, ',', '.') }}</flux:heading>
                                <flux:text size="sm" class="text-red-800/70 dark:text-red-400/70">{{ __('Belum dibayar oleh :count Siswa', ['count' => $otherTotalUnpaidCount]) }}</flux:text>
                            </flux:card>
                        </div>

                        <div class="flex justify-between items-center pt-4">
                            <flux:heading size="lg">{{ __('Daftar Tagihan Non-SPP') }}</flux:heading>
                            <flux:button :href="route('finance.fee-types.create')" variant="primary" icon="plus" wire:navigate>{{ __('Tambah Tagihan') }}</flux:button>
                        </div>
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('Nama Tagihan') }}</flux:table.column>
                                <flux:table.column>{{ __('Nominal') }}</flux:table.column>
                                <flux:table.column>{{ __('Target (Siswa)') }}</flux:table.column>
                                <flux:table.column>{{ __('Lunas') }}</flux:table.column>
                                <flux:table.column>{{ __('Progress') }}</flux:table.column>
                                <flux:table.column>{{ __('Status') }}</flux:table.column>
                                <flux:table.column></flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach($otherFees as $fee)
                                    <flux:table.row :key="'fee-'.$fee->id">
                                        <flux:table.cell font-weight="medium">
                                            <div class="flex flex-col">
                                                <span>{{ $fee->name }}</span>
                                                <span class="text-xs text-zinc-500">{{ ucfirst($fee->category) }}</span>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell>Rp {{ number_format($fee->default_amount, 0, ',', '.') }}</flux:table.cell>
                                        <flux:table.cell>{{ $fee->total_target }}</flux:table.cell>
                                        <flux:table.cell class="text-green-600 font-medium">{{ $fee->paid_target }}</flux:table.cell>
                                        <flux:table.cell>
                                            @php
                                                $progress = $fee->total_target > 0 ? round(($fee->paid_target / $fee->total_target) * 100) : 0;
                                            @endphp
                                            <div class="flex items-center gap-2">
                                                <div class="w-16 bg-zinc-200 dark:bg-zinc-700 h-1.5 rounded-full overflow-hidden">
                                                    <div class="bg-indigo-500 h-full" style="width: {{ $progress }}%"></div>
                                                </div>
                                                <span class="text-xs font-medium">{{ $progress }}%</span>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge :color="$fee->is_active ? 'green' : 'gray'">{{ $fee->is_active ? __('Aktif') : __('Nonaktif') }}</flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <div class="flex gap-2 justify-end">
                                                <flux:button variant="ghost" size="sm" icon="eye" :href="route('finance.fee-types.show', $fee->id)" wire:navigate />
                                                <flux:button variant="ghost" size="sm" icon="pencil" :href="route('finance.fee-types.edit', $fee->id)" wire:navigate />
                                                <flux:button variant="ghost" size="sm" icon="trash" wire:click="deleteFee({{ $fee->id }})" wire:confirm="{{ __('Hapus tagihan ini?') }}" />
                                            </div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </div>
                @endif

                @if($tab === 'reports')
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <flux:heading size="lg">{{ __('Laporan Transaksi Terbaru') }}</flux:heading>
                            <flux:button icon="document-arrow-down" variant="primary" :href="route('finance.reports.payments.pdf')" target="_blank">
                                {{ __('Download PDF') }}
                            </flux:button>
                        </div>
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('Tanggal') }}</flux:table.column>
                                <flux:table.column>{{ __('Siswa') }}</flux:table.column>
                                <flux:table.column>{{ __('Tagihan') }}</flux:table.column>
                                <flux:table.column>{{ __('Metode') }}</flux:table.column>
                                <flux:table.column align="end">{{ __('Nominal') }}</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach($recentTransactions as $trx)
                                    <flux:table.row :key="'rpt-'.$trx->id">
                                        <flux:table.cell>{{ $trx->paid_at->format('d/m/Y H:i') }}</flux:table.cell>
                                        <flux:table.cell>{{ $trx->invoice->student->name }}</flux:table.cell>
                                        <flux:table.cell>{{ $trx->invoice->feeType->name }}</flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge size="sm" inset="top bottom">{{ ucfirst($trx->method) }}</flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell align="end" class="font-bold">Rp {{ number_format($trx->amount, 0, ',', '.') }}</flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                        @if($recentTransactions->isEmpty())
                            <div class="py-8 text-center">
                                <flux:text >{{ __('Belum ada data transaksi untuk ditampilkan.') }}</flux:text>
                            </div>
                        @endif
                    </div>
                @endif

                @if($tab === 'audit')
                    <div class="space-y-4">
                        <flux:heading size="lg">{{ __('Riwayat Aktivitas Keuangan') }}</flux:heading>
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('Waktu') }}</flux:table.column>
                                <flux:table.column>{{ __('User') }}</flux:table.column>
                                <flux:table.column>{{ __('Aksi') }}</flux:table.column>
                                <flux:table.column>{{ __('Data / Model') }}</flux:table.column>
                                <flux:table.column>{{ __('Keterangan Perubahan') }}</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach($auditLogs as $log)
                                    <flux:table.row :key="'audit-'.$log->id">
                                        <flux:table.cell class="whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</flux:table.cell>
                                        <flux:table.cell font-weight="medium">{{ $log->user?->name ?? 'System' }}</flux:table.cell>
                                        <flux:table.cell>
                                            @php
                                                $color = match($log->action) {
                                                    'created' => 'green',
                                                    'updated' => 'blue',
                                                    'deleted' => 'red',
                                                    default => 'gray'
                                                };
                                            @endphp
                                            <flux:badge :color="$color" size="sm" inset="top bottom">{{ strtoupper($log->action) }}</flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <div class="flex flex-col">
                                                <span class="text-sm">{{ class_basename($log->model_type) }}</span>
                                                <span class="text-xs text-zinc-500">ID: {{ $log->model_id }}</span>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            @if($log->action === 'updated')
                                                <div class="text-xs space-y-1">
                                                    @foreach($log->new_values as $key => $value)
                                                        @if(!in_array($key, ['updated_at', 'created_at']))
                                                            <div>
                                                                <span class="font-bold">{{ $key }}:</span> 
                                                                <span class="text-red-500 line-through">{{ $log->old_values[$key] ?? 'N/A' }}</span> 
                                                                → 
                                                                <span class="text-green-600">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @elseif($log->action === 'created')
                                                <span class="text-xs text-zinc-500">{{ __('Data baru dibuat') }}</span>
                                            @else
                                                <span class="text-xs text-red-500">{{ __('Data telah dihapus') }}</span>
                                            @endif
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                        @if($auditLogs->isEmpty())
                            <div class="py-8 text-center">
                                <flux:text >{{ __('Belum ada riwayat aktivitas yang tercatat.') }}</flux:text>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </flux:main>
</div>
