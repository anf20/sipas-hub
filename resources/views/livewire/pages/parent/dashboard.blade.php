<div class="flex flex-col gap-6">
    <!-- Greeting Section -->
    <section class="flex flex-col gap-1 px-1">
        <h2 class="text-2xl font-bold text-forest-text-main font-display">{{ __('Halo, :name', ['name' => $user->name]) }}</h2>
        <p class="text-sm text-forest-text-muted">{{ __('Berikut adalah dashboard Anda untuk bulan :month.', ['month' => now()->translatedFormat('F Y')]) }}</p>
    </section>

    <!-- Summary Card (Total Unpaid Bills) -->
    <section class="bg-forest-dark text-white p-6 rounded-2xl shadow-lg flex flex-col gap-4 relative overflow-hidden mx-1">
        <!-- Decorative Pattern / Stitch aesthetics -->
        <div class="absolute top-0 right-0 w-40 h-40 bg-forest-accent/30 rounded-full -mr-16 -mt-16 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-forest-sage/10 rounded-full -ml-8 -mb-8 pointer-events-none"></div>
        
        <div class="flex justify-between items-start relative z-10">
            <div class="flex flex-col gap-1">
                <span class="font-semibold text-xs uppercase tracking-wider text-forest-light-sage/80">{{ __('TOTAL TUNGGAKAN') }}</span>
                <span class="font-display text-3xl font-extrabold text-white mt-1">Rp {{ number_format($totalUnpaidBalance, 0, ',', '.') }}</span>
            </div>
            <div class="bg-white/10 p-2.5 rounded-xl text-white shrink-0">
                <flux:icon.credit-card variant="solid" class="size-6" />
            </div>
        </div>
        
        <!-- Divider -->
        <hr class="border-white/10 my-1 relative z-10" />

        <div class="flex justify-between items-center mt-2 relative z-10">
            <span class="text-sm font-semibold text-forest-light-sage/90">{{ __(':count Tagihan Belum Lunas', ['count' => $unpaidCount]) }}</span>
            <flux:button :href="route('parent.invoices')" class="!bg-white !text-forest-dark border-none hover:!bg-white/95 h-11 px-6 rounded-xl font-bold text-sm cursor-pointer" wire:navigate>
                {{ __('Bayar Sekarang') }}
            </flux:button>
        </div>
    </section>

    <!-- Students Status Summary -->
    <section class="flex flex-col gap-3">
        <h3 class="text-base font-bold text-forest-text-main px-1">{{ __('Ringkasan Siswa') }}</h3>
        <div class="grid grid-cols-2 gap-4">
            @foreach($students as $student)
                <div class="bg-white p-4 rounded-2xl border border-forest-light-sage/20 shadow-sm flex flex-col gap-4 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-forest-surface text-forest-dark flex items-center justify-center shrink-0">
                            <flux:icon.user variant="solid" class="text-forest-dark size-5" />
                        </div>
                        <span class="font-semibold text-sm text-forest-text-main truncate" title="{{ $student->name }}">{{ $student->name }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-1 pt-2 border-t border-forest-surface">
                        <span class="text-xs text-forest-text-muted">{{ __('Status') }}</span>
                        @php
                            $pendingCount = $student->invoices()->where('status', 'unpaid')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="text-[10px] font-bold text-forest-danger bg-forest-danger/10 px-2.5 py-0.5 rounded-full">{{ $pendingCount }} {{ __('Pending') }}</span>
                        @else
                            <span class="text-[10px] font-bold text-forest-success bg-forest-success/10 px-2.5 py-0.5 rounded-full">{{ __('Lunas') }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Upcoming Due Dates -->
    <section class="flex flex-col gap-3">
        <div class="flex justify-between items-center px-1">
            <h3 class="text-base font-bold text-forest-text-main">{{ __('Jatuh Tempo Mendatang') }}</h3>
            <a href="{{ route('parent.invoices') }}" class="text-xs font-semibold text-forest-sage hover:text-forest-dark hover:underline cursor-pointer" wire:navigate>{{ __('Lihat Semua') }}</a>
        </div>
        <div class="flex flex-col gap-3">
            @forelse($upcomingInvoices as $invoice)
                @php
                    $isOverdue = $invoice->status === 'unpaid' && $invoice->due_date->isPast();
                @endphp
                <div class="{{ $isOverdue ? 'bg-red-50/50 border-red-200 hover:bg-red-50' : 'bg-white border-forest-light-sage/20 hover:bg-forest-surface/30' }} p-4 rounded-2xl border shadow-sm flex items-center justify-between transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl {{ $isOverdue ? 'bg-red-100 text-red-700' : 'bg-forest-surface text-forest-dark' }} flex items-center justify-center shrink-0">
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
                        <div class="flex flex-col min-w-0">
                            <span class="font-semibold text-sm text-forest-text-main truncate">{{ $invoice->billing_detail }}</span>
                            <span class="text-xs text-forest-text-muted mt-0.5 truncate">{{ $invoice->student->name }} • {{ $invoice->due_date->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col items-end shrink-0 ml-2">
                        <span class="font-bold text-sm {{ $isOverdue ? 'text-red-700' : 'text-forest-text-main' }}">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
                        @php
                            $daysLeft = (int) now()->startOfDay()->diffInDays($invoice->due_date->copy()->startOfDay(), false);
                        @endphp
                        @if($daysLeft >= 0)
                            <span class="text-[10px] font-semibold text-forest-success bg-forest-success/10 px-2 py-0.5 rounded-full mt-1">{{ $daysLeft }} {{ __('hari lagi') }}</span>
                        @else
                            <span class="text-[10px] font-bold text-red-600 bg-red-100/60 px-2 py-0.5 rounded-full mt-1">{{ __('Terlambat') }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-forest-text-muted bg-white rounded-2xl border border-dashed border-forest-light-sage/30">
                    {{ __('Tidak ada tagihan mendatang.') }}
                </div>
            @endforelse
        </div>
    </section>
</div>
