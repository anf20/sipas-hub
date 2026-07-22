<div class="flex flex-col gap-huge">
    <!-- Greeting Section -->
    <section class="flex flex-col gap-tiny px-1">
        <h2 class="font-headline-md text-2xl font-semibold text-primary">{{ __('Halo, :name', ['name' => $user->name]) }}</h2>
        <p class="font-body-md text-sm text-on-surface-variant">{{ __('Berikut adalah dashboard Anda untuk bulan :month.', ['month' => now()->translatedFormat('F Y')]) }}</p>
    </section>

    <!-- Summary Card (Total Unpaid Bills) -->
    <section class="bg-primary-container text-on-primary-container p-huge rounded-2xl shadow-md flex flex-col gap-normal relative overflow-hidden mx-1">
        <!-- Decorative Gradient Background -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-secondary/10 rounded-full -mr-12 -mt-12"></div>
        <div class="flex flex-col gap-tiny">
            <span class="font-label-bold text-xs uppercase tracking-wider opacity-80 text-on-primary-container">{{ __('Total Tagihan Belum Bayar') }}</span>
            <span class="font-display-lg text-3xl font-semibold text-white">Rp {{ number_format($totalUnpaidBalance, 0, ',', '.') }}</span>
        </div>
        <div class="flex gap-3 items-center mt-4">
            <flux:button :href="route('parent.invoices')" variant="primary" class="w-1/2 justify-center bg-secondary text-white border-none hover:bg-secondary/90 h-[52px]" wire:navigate>
                <flux:icon.credit-card variant="outline" class="mr-2 size-5" />
                {{ __('Bayar') }}
            </flux:button>
            <flux:button :href="route('parent.invoices')" class="w-1/2 justify-center bg-white/20 text-white border-none hover:bg-white/30 h-[52px]" wire:navigate>
                {{ __('Detail') }}
            </flux:button>
        </div>
    </section>

    <!-- Students Status Summary -->
    <section class="flex flex-col gap-normal">
        <h3 class="font-title-sm text-lg font-medium text-primary px-1">{{ __('Ringkasan Siswa') }}</h3>
        <div class="grid grid-cols-2 gap-normal">
            @foreach($students as $student)
                <div class="bg-surface-container-lowest p-normal rounded-xl border border-outline-variant shadow-sm flex flex-col gap-3 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-surface-container-highest flex items-center justify-center">
                            <flux:icon.user variant="outline" class="text-primary size-5" />
                        </div>
                        <span class="font-label-bold text-sm font-semibold text-on-surface truncate">{{ $student->name }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-1">
                        <span class="font-caption text-xs text-on-surface-variant">{{ __('Status') }}</span>
                        @php
                            $pendingCount = $student->invoices()->where('status', 'unpaid')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="font-label-bold text-[10px] text-error bg-error-container px-2 py-0.5 rounded-full">{{ $pendingCount }} {{ __('Pending') }}</span>
                        @else
                            <span class="font-label-bold text-[10px] text-secondary bg-secondary-container px-2 py-0.5 rounded-full">{{ __('Lunas') }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Upcoming Due Dates -->
    <section class="flex flex-col gap-normal">
        <div class="flex justify-between items-center px-1">
            <h3 class="font-title-sm text-lg font-medium text-primary">{{ __('Jatuh Tempo Mendatang') }}</h3>
            <a href="{{ route('parent.invoices') }}" class="font-label-bold text-xs text-primary hover:underline cursor-pointer" wire:navigate>{{ __('Lihat Semua') }}</a>
        </div>
        <div class="flex flex-col gap-3">
            @forelse($upcomingInvoices as $invoice)
                <div class="bg-surface-container-lowest p-normal rounded-xl border border-outline-variant shadow-sm flex items-center justify-between hover:bg-surface-container transition-colors group">
                    <div class="flex items-center gap-normal">
                        <div class="w-12 h-12 rounded-xl bg-surface-container-high flex flex-col items-center justify-center text-primary group-hover:bg-surface-container-highest transition-colors">
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
                        <div class="flex flex-col">
                            <span class="font-label-bold text-sm font-semibold text-on-surface">{{ $invoice->billing_detail }}</span>
                            <span class="font-caption text-xs text-on-surface-variant">{{ $invoice->student->name }} • {{ $invoice->due_date->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="font-label-bold text-sm font-semibold text-primary">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
                        @php
                            $daysLeft = (int) now()->startOfDay()->diffInDays($invoice->due_date->copy()->startOfDay(), false);
                        @endphp
                        @if($daysLeft >= 0)
                            <span class="font-caption text-xs text-error">{{ $daysLeft }} {{ __('hari lagi') }}</span>
                        @else
                            <span class="font-caption text-xs text-error">{{ __('Terlambat') }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-huge text-center text-on-surface-variant bg-surface-container-lowest rounded-xl border border-dashed border-outline-variant">
                    {{ __('Tidak ada tagihan mendatang.') }}
                </div>
            @endforelse
        </div>
    </section>
</div>
