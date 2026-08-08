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

    @php
        $hasPayable = $invoices->whereIn('status', ['unpaid', 'inactive'])->count() > 0;
    @endphp
    <!-- Quick Summary Section (Highest Priority) -->
    @if($hasPayable)
        <div class="bg-forest-dark text-white rounded-2xl p-6 shadow-lg sticky top-19 z-40 mx-1 mb-2 border border-white/10 overflow-hidden flex flex-col gap-4 relative">
            <!-- Decorative Pattern / Stitch aesthetics -->
            <div class="absolute top-0 right-0 w-40 h-40 bg-forest-accent/30 rounded-full -mr-16 -mt-16 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-forest-sage/10 rounded-full -ml-8 -mb-8 pointer-events-none"></div>

            <div class="flex justify-between items-start relative z-10">
                <div class="flex flex-col gap-1">
                    @if($isSelectMode)
                        <span class="font-semibold text-xs uppercase tracking-wider text-forest-light-sage/80">{{ __('TOTAL TERPILIH (:count)', ['count' => count($selectedInvoices)]) }}</span>
                        <span class="font-display text-3xl font-extrabold text-white mt-1">Rp {{ number_format($invoicesTotal, 0, ',', '.') }}</span>
                    @else
                        <span class="font-semibold text-xs uppercase tracking-wider text-forest-light-sage/80">{{ __('TOTAL TUNGGAKAN') }}</span>
                        <span class="font-display text-3xl font-extrabold text-white mt-1">Rp {{ number_format($totalUnpaidBalance, 0, ',', '.') }}</span>
                    @endif
                </div>
                <div class="bg-white/10 p-2.5 rounded-xl text-white shrink-0">
                    <flux:icon.credit-card variant="solid" class="size-6" />
                </div>
            </div>

            <!-- Divider -->
            <hr class="border-white/10 my-1 relative z-10" />

            @if($isSelectMode)
                <div class="flex gap-3 items-center relative z-10">
                    <flux:button 
                        wire:click="initiatePayment" 
                        variant="primary" 
                        icon="banknotes"
                        class="w-1/2 justify-center !bg-white !text-forest-dark border-none hover:!bg-white/95 h-11 rounded-xl cursor-pointer font-bold shadow-sm"
                        :disabled="empty($selectedInvoices)"
                    >
                        {{ __('Bayar Sekarang') }}
                    </flux:button>
                    <flux:button 
                        wire:click="toggleSelectMode" 
                        class="w-1/2 justify-center !bg-white/10 !text-white border-none hover:!bg-white/20 h-11 rounded-xl cursor-pointer font-semibold"
                    >
                        {{ __('Batal Pilih') }}
                    </flux:button>
                </div>
            @else
                <div class="flex justify-between items-center relative z-10">
                    <span class="text-sm font-semibold text-forest-light-sage/90">
                        {{ __(':count Tagihan Belum Lunas', ['count' => $unpaidCount]) }}
                    </span>
                    <flux:button 
                        wire:click="toggleSelectMode" 
                        icon="check-badge"
                        class="!bg-white/10 !text-white hover:!bg-white/20 border border-white/20 h-11 px-5 rounded-xl font-semibold cursor-pointer shadow-sm"
                    >
                        {{ __('Bayar Massal') }}
                    </flux:button>
                </div>
            @endif
        </div>
    @endif

    <!-- Category Filters & Mass Action -->
    <nav class="flex gap-2 overflow-x-auto hide-scrollbar py-1 -mx-md px-normal -mt-4">
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
    <div class="flex flex-col gap-large -mt-4">
        @forelse($groupedInvoices as $studentName => $studentInvoices)
            <div class="flex flex-col gap-normal">
                <div class="flex items-center gap-2 px-1">
                    <div class="w-8 h-8 rounded-full bg-primary-container/10 flex items-center justify-center">
                        <flux:icon.user variant="outline" class="text-primary size-5" />
                    </div>
                    <h3 class="font-title-sm text-lg font-medium text-primary">{{ $studentName }}</h3>
                    <div class="h-px flex-1 bg-outline-variant opacity-50 ml-2"></div>
                </div>
                @php
                    $activeInvoices = $studentInvoices->where('status', '!=', 'inactive');
                    $futureInvoices = $studentInvoices->where('status', 'inactive');
                @endphp
                <div class="flex flex-col gap-3">
                    @foreach($activeInvoices as $invoice)
                        @php
                            $isOverdue = $invoice->status === 'unpaid' && $invoice->due_date->isPast();
                        @endphp
                        <div class="flex items-center gap-3">
                            @if($isSelectMode && in_array($invoice->status, ['unpaid', 'inactive']))
                                <flux:checkbox wire:model.live="selectedInvoices" value="{{ $invoice->id }}" class="shrink-0" />
                            @endif
                            
                            <a 
                                @if(!$isSelectMode) href="{{ route('parent.invoices.show', $invoice) }}" wire:navigate @endif
                                class="{{ $invoice->status === 'pending' ? 'bg-amber-50/20 border-amber-200 dark:border-amber-900/20 hover:bg-amber-50/40' : ($isOverdue ? 'bg-red-50/70 dark:bg-red-950/10 border-red-200 dark:border-red-900/30 hover:bg-red-100/50 dark:hover:bg-red-950/20' : 'bg-surface-container-lowest border-outline-variant hover:bg-surface-container') }} p-normal rounded-xl border shadow-sm flex items-center gap-3 transition-colors group cursor-pointer text-left flex-1"
                            >
                                <div class="w-12 h-12 rounded-xl {{ $isOverdue ? 'bg-red-100/60 dark:bg-red-900/20' : 'bg-surface-container group-hover:bg-surface-container-highest' }} flex items-center justify-center transition-colors shrink-0">
                                    @php
                                        $category = strtolower($invoice->feeType->category);
                                    @endphp
                                    @if($category === 'spp') 
                                        <flux:icon.book-open variant="outline" class="size-5 {{ $isOverdue ? 'text-red-700 dark:text-red-400' : 'text-primary' }}" />
                                    @elseif($category === 'seragam') 
                                        <flux:icon.briefcase variant="outline" class="size-5 {{ $isOverdue ? 'text-red-700 dark:text-red-400' : 'text-primary' }}" />
                                    @elseif($category === 'kegiatan')
                                        <flux:icon.calendar-days variant="outline" class="size-5 {{ $isOverdue ? 'text-red-700 dark:text-red-400' : 'text-primary' }}" />
                                    @else 
                                        <flux:icon.banknotes variant="outline" class="size-5 {{ $isOverdue ? 'text-red-700 dark:text-red-400' : 'text-primary' }}" />
                                    @endif
                                </div>
                                <div class="flex-1 flex flex-col min-w-0 text-left">
                                    <p class="font-label-bold text-sm font-semibold text-on-surface leading-tight truncate">{{ $invoice->billing_detail }}</p>
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
                                            • {{ __('Jatuh Tempo: :date', ['date' => $invoice->due_date->translatedFormat('d M Y')]) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center mt-2 w-full">
                                        <span class="font-display-lg text-sm font-bold {{ $isOverdue ? 'text-red-700 dark:text-red-400' : 'text-primary' }}">
                                            Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                                        </span>
                                        <div class="flex items-center shrink-0">
                                            @if($invoice->status === 'paid')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-secondary-container text-on-secondary-container">
                                                    {{ __('Sudah Bayar') }}
                                                </span>
                                            @elseif($invoice->status === 'pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                                    {{ __('Menunggu Verifikasi') }}
                                                </span>
                                            @elseif($invoice->status === 'inactive')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-200 text-zinc-700">
                                                    {{ __('Bulan Depan') }}
                                                </span>
                                            @else
                                                @if($isOverdue)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                                        {{ __('Jatuh Tempo') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-error-container text-on-error-container">
                                                        {{ __('Belum Bayar') }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach

                    @if($futureInvoices->isNotEmpty() && $isSelectMode)
                        @php
                            $studentId = $futureInvoices->first()->student_id;
                            $maxCount = $futureInvoices->count();
                            $currentCount = $advanceCount[$studentId] ?? 0;
                            $showingInvoices = $futureInvoices->take($currentCount);
                        @endphp
                        
                        <div class="mt-4 flex flex-col gap-3">
                            <div class="flex items-center justify-between p-3 bg-surface-container-low rounded-xl border border-outline-variant">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <flux:icon.clock variant="outline" class="size-4 text-on-surface-variant" />
                                        <span class="font-label-bold text-sm text-on-surface-variant">{{ __('Bayar Tagihan Bulan Depan') }}</span>
                                    </div>
                                    <span class="text-xs text-on-surface-variant/70">{{ __('Maksimal: :max bulan', ['max' => $maxCount]) }}</span>
                                </div>
                                
                                <div class="flex items-center gap-3 bg-surface-container-lowest p-1 rounded-lg border border-outline-variant shadow-sm">
                                    <button wire:click="decrementAdvance({{ $studentId }})" class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-surface-container-high transition-colors disabled:opacity-50 text-on-surface" {{ $currentCount <= 0 ? 'disabled' : '' }}>
                                        <flux:icon.minus variant="mini" class="size-4" />
                                    </button>
                                    <span class="font-title-sm text-base font-semibold w-4 text-center">{{ $currentCount }}</span>
                                    <button wire:click="incrementAdvance({{ $studentId }})" class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-surface-container-high transition-colors disabled:opacity-50 text-on-surface" {{ $currentCount >= $maxCount ? 'disabled' : '' }}>
                                        <flux:icon.plus variant="mini" class="size-4" />
                                    </button>
                                </div>
                            </div>
                            
                            @if($currentCount > 0)
                                <div class="flex flex-col gap-3 pl-2 sm:pl-4 border-l-2 border-outline-variant/30 ml-2">
                                    @foreach($showingInvoices as $invoice)
                                        <div class="flex items-center gap-3 relative">
                                            <!-- Fake disabled checkbox for UI consistency -->
                                            <div class="shrink-0 flex items-center justify-center w-4.5 h-4.5 rounded-sm bg-primary text-white">
                                                <flux:icon.check variant="mini" class="size-3.5" />
                                            </div>
                                            
                                            <div class="bg-surface-container-lowest p-normal rounded-xl border border-outline-variant shadow-sm flex items-center gap-3 opacity-80 flex-1">
                                                <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center shrink-0">
                                                    <flux:icon.book-open variant="outline" class="size-5 text-primary" />
                                                </div>
                                                <div class="flex-1 flex flex-col min-w-0 text-left">
                                                    <p class="font-label-bold text-sm font-semibold text-on-surface leading-tight truncate">{{ $invoice->billing_detail }}</p>
                                                    <div class="text-xs text-on-surface-variant/80 truncate mt-0.5 flex flex-wrap items-center gap-x-1.5 gap-y-0.5">
                                                        @if($invoice->period_month && $invoice->period_year)
                                                            <span class="text-[10px] opacity-80">
                                                                {{ __('Periode: :month :year', [
                                                                    'month' => Carbon\Carbon::create()->month($invoice->period_month)->translatedFormat('F'),
                                                                    'year' => $invoice->period_year
                                                                ]) }}
                                                            </span>
                                                        @endif
                                                        <span class="text-[10px] opacity-75">
                                                            • {{ __('Jatuh Tempo: :date', ['date' => $invoice->due_date->translatedFormat('d M Y')]) }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between items-center mt-2 w-full">
                                                        <span class="font-display-lg text-sm font-bold text-primary">
                                                            Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                                                        </span>
                                                        <div class="flex items-center shrink-0">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-200 text-zinc-700">
                                                                {{ __('Bulan Depan') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-huge text-center text-on-surface-variant bg-surface-container-lowest rounded-xl border border-dashed border-outline-variant">
                {{ __('Tidak ada tagihan yang ditemukan.') }}
            </div>
        @endforelse
    </div>

    <!-- Confirmation Modal (Invoice Summary) -->
    <flux:modal wire:model="showConfirmationModal" class="min-w-87.5 max-w-125">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Ringkasan Pembayaran') }}</flux:heading>
                <flux:subheading>{{ __('Pilih metode pembayaran dan tinjau rincian biaya.') }}</flux:subheading>
            </div>

            <div class="space-y-3">
                <flux:label>{{ __('Metode Pembayaran') }}</flux:label>
                <flux:radio.group wire:model.live="paymentMethod" variant="cards" class="flex flex-col gap-2">
                    <flux:radio value="manual_transfer" label="Transfer Manual (Rekening Sekolah)" description="Biaya Flat Rp 0 - Verifikasi Manual" />
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
                <flux:button class="flex-1" variant="primary" icon="banknotes" wire:click="startPayment" wire:loading.attr="disabled">
                    {{ __('Bayar Sekarang') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Manual Transfer Details & Upload Proof Modal -->
    <flux:modal wire:model="showManualTransferModal" class="min-w-[350px] max-w-[500px]">
        <form wire:submit.prevent="paySelected" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Upload Bukti Transfer') }}</flux:heading>
                <flux:subheading>{{ __('Silakan transfer ke rekening sekolah di bawah dan unggah bukti transaksi Anda.') }}</flux:subheading>
            </div>

            <div class="p-4 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/30 rounded-xl space-y-3">
                <div class="text-xs font-bold text-amber-800 dark:text-amber-400 uppercase tracking-wider">{{ __('Rekening Transfer Sekolah') }}</div>
                <div class="text-sm text-zinc-700 dark:text-zinc-300 space-y-1">
                    <p><strong>Bank Syariah Indonesia (BSI)</strong></p>
                    <div x-data="{ copied: false }" class="flex items-center gap-2">
                        <p>No. Rekening: <span class="font-mono text-base font-bold text-primary">7711223344</span></p>
                        <button 
                            type="button"
                            x-on:click="navigator.clipboard.writeText('7711223344'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="inline-flex items-center gap-1 text-xs text-primary hover:text-primary-hover focus:outline-none cursor-pointer font-medium bg-primary/10 hover:bg-primary/20 px-2 py-0.5 rounded-lg transition-all"
                        >
                            <flux:icon.document-duplicate variant="mini" class="size-3" x-show="!copied" />
                            <flux:icon.check variant="mini" class="size-3 text-green-600" x-show="copied" />
                            <span x-text="copied ? '{{ __('Tersalin') }}' : '{{ __('Salin') }}'"></span>
                        </button>
                    </div>
                    <p>Atas Nama: <strong>SIPAS Hub Yayasan</strong></p>
                </div>
                <div class="pt-2 border-t border-amber-200 dark:border-amber-900/20">
                    <flux:label class="text-amber-900 dark:text-amber-300 font-semibold mb-1">{{ __('Upload Bukti Transfer (Foto/Gambar)') }}</flux:label>
                    <flux:input type="file" wire:model="proofFile" accept="image/*" class="!bg-white dark:!bg-zinc-800" required />
                    @error('proofFile') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
                    
                    @if($proofFile && !$errors->has('proofFile'))
                        <div class="mt-2 text-xs text-green-600 flex items-center gap-1 font-medium">
                            <flux:icon.check-circle variant="mini" class="size-4" />
                            {{ __('Gambar berhasil dipilih.') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-xl flex justify-between items-center border border-zinc-200 dark:border-zinc-700">
                <span class="font-bold text-zinc-600 dark:text-zinc-400">{{ __('Total Tagihan') }}</span>
                <span class="font-display-lg text-xl font-bold text-primary">Rp {{ number_format($totalToPay, 0, ',', '.') }}</span>
            </div>

            <div class="flex gap-3">
                <flux:button class="flex-1" variant="ghost" wire:click="$set('showManualTransferModal', false)">{{ __('Batal') }}</flux:button>
                <flux:button class="flex-1" type="submit" variant="primary" icon="paper-airplane" wire:loading.attr="disabled">
                    {{ __('Kirim Bukti & WA') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
