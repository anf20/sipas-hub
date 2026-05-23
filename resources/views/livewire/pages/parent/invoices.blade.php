<div class="flex flex-col gap-huge">
    <!-- Page Title & Summary -->
    <section class="flex flex-col gap-tiny px-1">
        <h2 class="font-headline-md text-2xl font-semibold text-primary">{{ __('Tagihan Aktif') }}</h2>
        <p class="font-body-md text-sm text-on-surface-variant">{{ __('Tinjau dan selesaikan biaya pendidikan putra-putri Anda.') }}</p>
    </section>

    <!-- Category Filters -->
    <nav class="flex gap-2 overflow-x-auto hide-scrollbar py-1 -mx-md px-normal">
        <button 
            wire:click="setFilter('all')"
            class="{{ $filter === 'all' ? 'bg-primary-container text-on-primary-container shadow-sm' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }} px-large py-2.5 rounded-xl font-label-bold text-xs font-semibold transition-all active:scale-95 whitespace-nowrap"
        >
            {{ __('Semua') }}
        </button>
        <button 
            wire:click="setFilter('unpaid')"
            class="{{ $filter === 'unpaid' ? 'bg-primary-container text-on-primary-container shadow-sm' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }} px-large py-2.5 rounded-xl font-label-bold text-xs font-semibold transition-all active:scale-95 whitespace-nowrap"
        >
            {{ __('Belum Bayar') }}
        </button>
        <button 
            wire:click="setFilter('paid')"
            class="{{ $filter === 'paid' ? 'bg-primary-container text-on-primary-container shadow-sm' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }} px-large py-2.5 rounded-xl font-label-bold text-xs font-semibold transition-all active:scale-95 whitespace-nowrap"
        >
            {{ __('Sudah Bayar') }}
        </button>
    </nav>

    <!-- Grouped Invoices -->
    <div class="flex flex-col gap-large">
        @forelse($groupedInvoices as $studentName => $studentInvoices)
            <div class="flex flex-col gap-normal">
                <div class="flex items-center gap-2 px-1">
                    <div class="w-8 h-8 rounded-full bg-primary-container/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-[20px]">face</span>
                    </div>
                    <h3 class="font-title-sm text-lg font-medium text-primary">{{ $studentName }}</h3>
                    <div class="h-[1px] flex-1 bg-outline-variant opacity-50 ml-2"></div>
                </div>
                <div class="flex flex-col gap-3">
                    @foreach($studentInvoices as $invoice)
                        <a 
                            href="{{ route('parent.invoices.show', $invoice) }}"
                            wire:navigate
                            class="bg-surface-container-lowest p-normal rounded-xl border border-outline-variant shadow-sm flex items-center justify-between hover:bg-surface-container transition-colors group cursor-pointer text-left"
                        >
                            <div class="flex items-center gap-normal text-left">
                                <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center group-hover:bg-surface-container-highest transition-colors shrink-0">
                                    <span class="material-symbols-outlined text-primary">
                                        @php
                                            $category = strtolower($invoice->feeType->category);
                                        @endphp
                                        @if($category === 'spp') 
                                            menu_book 
                                        @elseif($category === 'seragam') 
                                            checkroom 
                                        @elseif($category === 'kegiatan')
                                            event_available
                                        @else 
                                            payments 
                                        @endif
                                    </span>
                                </div>
                                <div class="flex flex-col text-left">
                                    <p class="font-label-bold text-sm font-semibold text-on-surface leading-tight">{{ $invoice->feeType->name }}</p>
                                    <div class="flex flex-col gap-0.5">
                                        @if($invoice->period_month && $invoice->period_year)
                                            <p class="font-caption text-[10px] text-on-surface-variant/80">
                                                {{ __('Periode: :month :year', [
                                                    'month' => Carbon\Carbon::create()->month($invoice->period_month)->translatedFormat('F'),
                                                    'year' => $invoice->period_year
                                                ]) }}
                                            </p>
                                        @endif
                                        <p class="font-caption text-xs text-on-surface-variant">{{ __('Jatuh Tempo: :date', ['date' => $invoice->due_date->translatedFormat('d M Y')]) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right flex flex-col items-end gap-1">
                                <p class="font-display-lg text-lg font-semibold text-primary">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</p>
                                @if($invoice->status === 'paid')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-secondary-container text-on-secondary-container">
                                        {{ __('Sudah Bayar') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-error-container text-on-error-container">
                                        {{ __('Belum Bayar') }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="p-huge text-center text-on-surface-variant bg-surface-container-lowest rounded-xl border border-dashed border-outline-variant">
                {{ __('Tidak ada tagihan yang ditemukan.') }}
            </div>
        @endforelse
    </div>

    <!-- Quick Summary Section -->
    @if($totalUnpaidBalance > 0)
        <div class="bg-primary-container text-on-primary-container rounded-2xl p-large flex flex-col gap-normal shadow-xl sticky bottom-2 z-10 mx-1 border border-white/10">
            <div class="flex justify-between items-start px-1">
                <div>
                    <p class="font-label-bold text-xs font-semibold opacity-80 uppercase tracking-wider text-on-primary-container">{{ __('Total Belum Dibayar') }}</p>
                    <h4 class="font-display-lg text-2xl font-semibold text-white mt-1">Rp {{ number_format($totalUnpaidBalance, 0, ',', '.') }}</h4>
                </div>
                <div class="bg-white/10 p-2.5 rounded-xl text-white">
                    <span class="material-symbols-outlined">account_balance_wallet</span>
                </div>
            </div>
            <p class="text-[10px] text-white/70 italic px-1">{{ __('* Silakan klik pada kartu tagihan di atas untuk melihat detail dan melakukan pembayaran.') }}</p>
        </div>
    @endif
</div>
