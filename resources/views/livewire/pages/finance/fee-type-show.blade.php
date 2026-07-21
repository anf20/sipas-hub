<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('finance.hub') }}" wire:navigate>{{ __('Keuangan') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('finance.hub', ['tab' => $feeType->category === 'SPP' ? 'spp' : 'fees']) }}" wire:navigate>
                {{ $feeType->category === 'SPP' ? __('Manajemen SPP') : __('Tagihan Lainnya') }}
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Detail') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:spacer />
        <div class="hidden md:block font-bold text-sm text-slate-700 dark:text-zinc-300">
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
    </flux:header>

    <flux:main class="space-y-6">
        <!-- Header Info -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <flux:heading size="xl">{{ $feeType->name }}</flux:heading>
                    <flux:badge  size="sm" inset="top">{{ ucfirst($feeType->category) }}</flux:badge>
                </div>
                <flux:subheading>
                    {{ $feeType->is_recurring ? __('Tagihan Bulanan') : __('Tagihan Sekali Saja') }} 
                    <span class="mx-1">•</span>
                    {{ __('Dibuat pada') }} {{ $feeType->created_at->format('d M Y') }}
                </flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:button :href="route('finance.fee-types.edit', $feeType)" icon="pencil" variant="ghost" size="sm" wire:navigate>
                    {{ __('Edit Master') }}
                </flux:button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <flux:card class="space-y-1">
                <flux:text size="sm" >{{ __('Total Tagihan') }}</flux:text>
                <div class="flex items-end gap-2">
                    <flux:heading size="xl">{{ $stats['total_count'] }}</flux:heading>
                    <flux:text size="xs"  class="mb-1">{{ __('Invoice') }}</flux:text>
                </div>
            </flux:card>

            <flux:card class="space-y-1">
                <flux:text size="sm" >{{ __('Sudah Bayar') }}</flux:text>
                <div class="flex items-end gap-2 text-green-600 dark:text-green-400">
                    <flux:heading size="xl">{{ $stats['paid_count'] }}</flux:heading>
                    <flux:text size="xs" class="mb-1 font-medium">{{ __('Lunas') }}</flux:text>
                </div>
            </flux:card>

            <flux:card class="space-y-1">
                <flux:text size="sm" >{{ __('Belum Bayar') }}</flux:text>
                <div class="flex items-end gap-2 text-orange-600 dark:text-orange-400">
                    <flux:heading size="xl">{{ $stats['unpaid_count'] }}</flux:heading>
                    <flux:text size="xs" class="mb-1 font-medium">{{ __('Tunggakan') }}</flux:text>
                </div>
            </flux:card>

            <flux:card class="space-y-1 bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-700">
                <flux:text size="sm" >{{ __('Dana Terkumpul') }}</flux:text>
                <div class="flex items-end gap-1">
                    <flux:text size="sm" class="mb-1 font-bold">Rp</flux:text>
                    <flux:heading size="xl">{{ number_format($stats['paid_amount'], 0, ',', '.') }}</flux:heading>
                </div>
                <flux:text size="xs" >{{ __('dari') }} Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</flux:text>
            </flux:card>
        </div>

        <!-- Filter & Table Section -->
        <flux:card class="p-0 overflow-hidden">
            <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 space-y-4">
                <div class="flex flex-col md:flex-row justify-between gap-4">
                    <flux:heading size="lg">{{ __('Daftar Invoice Siswa') }}</flux:heading>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <flux:radio.group wire:model.live="status" variant="segmented" size="sm">
                            <flux:radio value="all" label="{{ __('Semua') }}" />
                            <flux:radio value="unpaid" label="{{ __('Unpaid') }}" />
                            <flux:radio value="paid" label="{{ __('Paid') }}" />
                        </flux:radio.group>
                        
                        <div class="w-full sm:w-64">
                            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Cari NIS atau Nama...') }}" size="sm" />
                        </div>
                    </div>
                </div>
            </div>

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Nama Siswa') }}</flux:table.column>
                    <flux:table.column>{{ __('NIS') }}</flux:table.column>
                    <flux:table.column>{{ __('Kelas') }}</flux:table.column>
                    <flux:table.column>{{ __('Periode') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Nominal') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($invoices as $invoice)
                        <flux:table.row :key="$invoice->id">
                            <flux:table.cell font-weight="medium">{{ $invoice->student->name }}</flux:table.cell>
                            <flux:table.cell>{{ $invoice->student->nis }}</flux:table.cell>
                            <flux:table.cell>{{ $invoice->student->schoolClass->name }}</flux:table.cell>
                            <flux:table.cell>
                                @if($feeType->is_recurring)
                                    @php
                                        $monthNames = [
                                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                                        ];
                                    @endphp
                                    {{ $monthNames[(int)$invoice->period_month] ?? 'Unknown' }} {{ $invoice->period_year }}
                                @else
                                    {{ __('Sekali Saja') }}
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$invoice->status === 'paid' ? 'green' : 'orange'" variant="pill" size="sm">
                                    {{ $invoice->status === 'paid' ? __('Lunas') : __('Belum Bayar') }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell align="end" class="font-mono tabular-nums">
                                Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            @if($invoices->isEmpty())
                <div class="py-20 text-center">
                    <flux:text >{{ __('Tidak ada data invoice yang sesuai dengan kriteria.') }}</flux:text>
                </div>
            @endif

            <div class="px-6 py-4 bg-zinc-50/50 dark:bg-zinc-900/50 border-t border-zinc-100 dark:border-zinc-800">
                {{ $invoices->links() }}
            </div>
        </flux:card>
    </flux:main>
</div>
