<div class="flex flex-col gap-huge">
    <!-- Page Title & Summary -->
    <section class="flex flex-col gap-tiny px-1">
        <h2 class="font-headline-md text-2xl font-semibold text-primary">{{ __('Riwayat Pembayaran') }}</h2>
        <p class="font-body-md text-sm text-on-surface-variant">{{ __('Lihat semua transaksi pembayaran yang telah berhasil dilakukan.') }}</p>
    </section>

    <!-- Filters Section -->
    <div class="flex flex-col gap-3 px-1 -mt-2">
        <!-- Row 1: Filter Icon + Scrollable Horizontal Month Pills -->
        <div class="flex items-center gap-2 w-full">
            <!-- Filter Icon on the left -->
            <button 
                type="button" 
                wire:click="$toggle('showAdvancedFilters')" 
                class="p-2.5 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 focus:outline-none cursor-pointer flex items-center justify-center shrink-0 transition-colors shadow-xs"
                title="{{ __('Filter Lanjutan') }}"
            >
                <flux:icon.adjustments-horizontal class="size-5" />
            </button>

            <!-- Scrollable Horizontal Month Pills -->
            <div class="flex-1 overflow-x-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] py-1 flex items-center gap-2">
                @foreach($monthsList as $num => $name)
                    <button 
                        type="button" 
                        wire:click="$set('selectedMonth', '{{ $num }}')"
                        class="px-4 py-1.5 rounded-full border text-xs font-semibold whitespace-nowrap transition-all cursor-pointer {{ $selectedMonth == $num ? 'bg-primary border-primary text-white shadow-xs' : 'border-zinc-200 dark:border-zinc-750 bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700' }}"
                    >
                        {{ $name }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Row 2: Advanced Filters (Category & Year) placed below Month Pills -->
        @if($showAdvancedFilters)
            <div x-transition class="grid grid-cols-2 gap-2 p-3 bg-zinc-50 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-inner">
                <flux:select wire:model.live="selectedCategory" placeholder="{{ __('Kategori') }}">
                    <flux:select.option value="">{{ __('Semua Kategori') }}</flux:select.option>
                    @foreach($categories as $cat)
                        <flux:select.option :value="$cat">{{ strtoupper($cat) }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model.live="selectedYear" placeholder="{{ __('Tahun') }}">
                    <flux:select.option value="">{{ __('Semua Tahun') }}</flux:select.option>
                    @foreach($years as $yr)
                        <flux:select.option :value="$yr">{{ $yr }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        @endif
    </div>

    <!-- History List -->
    <div class="flex flex-col gap-3">
        @forelse($paidInvoices as $invoice)
            <div class="bg-surface-container-lowest p-normal rounded-xl border border-outline-variant shadow-sm flex items-center gap-3 hover:bg-surface-container transition-colors group text-left">
                <!-- Kolom 1 (Kiri): Logo -->
                <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center text-secondary group-hover:bg-secondary-container/20 transition-colors shrink-0">
                    @php
                        $category = strtolower($invoice->feeType->category);
                    @endphp
                    @if($category === 'spp') 
                        <flux:icon.book-open variant="outline" class="size-5" />
                    @elseif($category === 'seragam') 
                        <flux:icon.briefcase variant="outline" class="size-5" />
                    @elseif($category === 'kegiatan')
                        <flux:icon.calendar-days variant="outline" class="size-5" />
                    @else 
                        <flux:icon.banknotes variant="outline" class="size-5" />
                    @endif
                </div>

                <!-- Kolom 2 (Kanan): Detail Tagihan -->
                <div class="flex-1 flex flex-col min-w-0">
                    <!-- Baris 1: Judul Tagihan -->
                    <p class="font-label-bold text-sm font-semibold text-on-surface leading-tight truncate">{{ $invoice->billing_detail }}</p>
                    
                    <!-- Baris 2: Detail (Siswa, Periode & Tanggal Bayar) -->
                    <div class="text-xs text-on-surface-variant/80 truncate mt-0.5 flex flex-wrap items-center gap-x-1.5 gap-y-0.5">
                        <span>{{ $invoice->student->name }}</span>
                        @if($invoice->period_month && $invoice->period_year)
                            <span class="text-[10px] opacity-80">
                                • {{ __('Periode: :month :year', [
                                    'month' => Carbon\Carbon::create()->month($invoice->period_month)->translatedFormat('F'),
                                    'year' => $invoice->period_year
                                ]) }}
                            </span>
                        @endif
                        <span class="text-[10px] opacity-75">
                            • {{ __('Tanggal Bayar: :date', ['date' => $invoice->updated_at->translatedFormat('d M Y')]) }}
                        </span>
                    </div>

                    <!-- Baris 3: Nominal & Badge/Kwitansi (Masing-masing di pojok) -->
                    <div class="flex justify-between items-center mt-2 w-full">
                        <!-- Nominal (Kiri Bawah) -->
                        <span class="font-display-lg text-sm font-bold text-secondary">
                            Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                        </span>

                        <!-- Badge & Kwitansi (Kanan Bawah) -->
                        <div class="flex items-center gap-1.5 shrink-0">
                            @if($invoice->payments->first())
                                <flux:button 
                                    as="a" 
                                    :href="route('parent.payments.receipt', $invoice->payments->first())" 
                                    target="_blank" 
                                    size="xs" 
                                    variant="ghost" 
                                    icon="document-arrow-down"
                                    class="!text-[10px] !py-0.5"
                                >
                                    {{ __('Kwitansi') }}
                                </flux:button>
                            @endif
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-secondary-container text-on-secondary-container">
                                {{ __('Lunas') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-huge text-center text-on-surface-variant bg-surface-container-lowest rounded-xl border border-dashed border-outline-variant">
                {{ __('Belum ada riwayat pembayaran.') }}
            </div>
        @endforelse
    </div>
</div>
