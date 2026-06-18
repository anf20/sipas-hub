<div class="flex flex-col gap-huge">
    <!-- Page Title & Summary -->
    <section class="flex flex-col gap-tiny px-1">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-headline-md text-2xl font-semibold text-primary">{{ __('Tagihan Aktif') }}</h2>
                <p class="font-body-md text-sm text-on-surface-variant">{{ __('Tinjau dan selesaikan biaya pendidikan putra-putri Anda.') }}</p>
            </div>
        </div>
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
                        <flux:icon.user variant="outline" class="text-primary size-5" />
                    </div>
                    <h3 class="font-title-sm text-lg font-medium text-primary">{{ $studentName }}</h3>
                    <div class="h-[1px] flex-1 bg-outline-variant opacity-50 ml-2"></div>
                </div>
                <div class="flex flex-col gap-3">
                    @foreach($studentInvoices as $invoice)
                        <div class="flex items-center gap-3">
                            @if($isSelectMode && $invoice->status === 'unpaid')
                                <flux:checkbox wire:model.live="selectedInvoices" value="{{ $invoice->id }}" class="shrink-0" />
                            @endif
                            
                            <a 
                                @if(!$isSelectMode) href="{{ route('parent.invoices.show', $invoice) }}" wire:navigate @endif
                                class="bg-surface-container-lowest p-normal rounded-xl border border-outline-variant shadow-sm flex items-center justify-between hover:bg-surface-container transition-colors group cursor-pointer text-left flex-1"
                            >
                                <div class="flex items-center gap-normal text-left">
                                    <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center group-hover:bg-surface-container-highest transition-colors shrink-0">
                                        @php
                                            $category = strtolower($invoice->feeType->category);
                                        @endphp
                                        @if($category === 'spp') 
                                            <flux:icon.book-open variant="outline" class="size-5 text-primary" />
                                        @elseif($category === 'seragam') 
                                            <flux:icon.briefcase variant="outline" class="size-5 text-primary" />
                                        @elseif($category === 'kegiatan')
                                            <flux:icon.calendar-days variant="outline" class="size-5 text-primary" />
                                        @else 
                                            <flux:icon.banknotes variant="outline" class="size-5 text-primary" />
                                        @endif
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
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="p-huge text-center text-on-surface-variant bg-surface-container-lowest rounded-xl border border-dashed border-outline-variant">
                {{ __('Tidak ada tagihan yang ditemukan.') }}
            </div>
        @endforelse

        <!-- Move "Bayar Sekaligus" Button here -->
        @if($unpaidCount > 1 && ($filter === 'all' || $filter === 'unpaid'))
            <div class="flex justify-center mt-4">
                <flux:button 
                    variant="{{ $isSelectMode ? 'primary' : 'outline' }}" 
                    wire:click="toggleSelectMode"
                    class="w-full max-w-xs shadow-sm !rounded-xl h-[48px]"
                    icon="{{ $isSelectMode ? 'x-mark' : 'check-badge' }}"
                >
                    {{ $isSelectMode ? __('Batal Pilih') : __('Pilih Tagihan (Bayar Massal)') }}
                </flux:button>
            </div>
        @endif
    </div>

    <!-- Quick Summary Section -->
    @if($totalUnpaidBalance > 0)
        <div class="bg-primary-container text-on-primary-container rounded-2xl p-large flex flex-col gap-normal shadow-xl sticky bottom-24 z-40 mx-1 border border-white/10">
            <div class="flex justify-between items-start px-1">
                <div>
                    @if($isSelectMode)
                        <p class="font-label-bold text-xs font-semibold opacity-80 uppercase tracking-wider text-on-primary-container">{{ __('Total Terpilih (:count)', ['count' => count($selectedInvoices)]) }}</p>
                        <h4 class="font-display-lg text-2xl font-semibold text-white mt-1">Rp {{ number_format($invoicesTotal, 0, ',', '.') }}</h4>
                    @else
                        <p class="font-label-bold text-xs font-semibold opacity-80 uppercase tracking-wider text-on-primary-container">{{ __('Total Belum Dibayar') }}</p>
                        <h4 class="font-display-lg text-2xl font-semibold text-white mt-1">Rp {{ number_format($totalUnpaidBalance, 0, ',', '.') }}</h4>
                    @endif
                </div>
                <div class="bg-white/10 p-2.5 rounded-xl text-white">
                    <flux:icon.credit-card variant="outline" class="size-6" />
                </div>
            </div>

            @if($isSelectMode)
                <flux:button 
                    wire:click="initiatePayment" 
                    variant="primary" 
                    icon="banknotes"
                    class="w-full !bg-secondary !text-white border-none mt-2 h-[52px] !rounded-xl"
                    :disabled="empty($selectedInvoices)"
                >
                    {{ __('Bayar Sekarang') }}
                </flux:button>
            @else
                <p class="text-[10px] text-white/70 italic px-1">{{ __('* Silakan klik pada kartu tagihan di atas untuk melihat detail dan melakukan pembayaran.') }}</p>
            @endif
        </div>
    @endif

    <!-- Confirmation Modal (Invoice Summary) -->
    <flux:modal wire:model="showConfirmationModal" class="min-w-[350px] max-w-[500px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Ringkasan Pembayaran') }}</flux:heading>
                <flux:subheading>{{ __('Pilih metode pembayaran dan tinjau rincian biaya.') }}</flux:subheading>
            </div>

            <div class="space-y-3">
                <flux:label>{{ __('Metode Pembayaran') }}</flux:label>
                <flux:radio.group wire:model.live="paymentMethod" variant="cards" class="flex flex-col gap-2">
                    <flux:radio value="bca_va" label="BCA Virtual Account" description="Biaya Flat Rp 4.500" />
                    <flux:radio value="bri_va" label="BRI Virtual Account" description="Biaya Flat Rp 4.500" />
                    <flux:radio value="echannel" label="Mandiri Bill Payment" description="Biaya Flat Rp 4.500" />
                    <flux:radio value="qris" label="QRIS (Gopay/Dana/OVO)" description="Biaya Layanan 0.7%" />
                    <flux:radio value="dana" label="Dana (Direct)" description="Biaya Layanan 1.5%" />
                </flux:radio.group>
            </div>

            <div class="space-y-4 max-h-[30vh] overflow-y-auto pr-2">
                <div class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ __('Rincian Tagihan') }}</div>
                @foreach($selectedInvoicesData as $item)
                    <div class="flex justify-between items-start border-b border-zinc-100 dark:border-zinc-800 pb-2">
                        <div class="flex flex-col gap-0.5">
                            <span class="font-semibold text-sm">{{ $item->feeType->name }}</span>
                            <span class="text-xs text-zinc-500">{{ $item->student->name }}</span>
                        </div>
                        <span class="font-medium text-sm">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                    </div>
                @endforeach
                
                <div class="flex justify-between items-center pt-2">
                    <span class="text-sm text-zinc-500">{{ __('Biaya Layanan') }}</span>
                    <span class="font-medium text-sm">Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-xl flex justify-between items-center border border-zinc-200 dark:border-zinc-700">
                <span class="font-bold text-zinc-600 dark:text-zinc-400">{{ __('Total Bayar') }}</span>
                <span class="font-display-lg text-xl font-bold text-primary">Rp {{ number_format($totalToPay, 0, ',', '.') }}</span>
            </div>

            <div class="flex gap-3">
                <flux:button class="flex-1" variant="ghost" wire:click="$set('showConfirmationModal', false)">{{ __('Batal') }}</flux:button>
                <flux:button class="flex-1" variant="primary" icon="banknotes" wire:click="paySelected">{{ __('Bayar Sekarang') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
