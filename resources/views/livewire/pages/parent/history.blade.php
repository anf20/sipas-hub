<div class="flex flex-col gap-huge">
    <!-- Page Title & Summary -->
    <section class="flex flex-col gap-tiny px-1">
        <h2 class="font-headline-md text-2xl font-semibold text-primary">{{ __('Riwayat Pembayaran') }}</h2>
        <p class="font-body-md text-sm text-on-surface-variant">{{ __('Lihat semua transaksi pembayaran yang telah berhasil dilakukan.') }}</p>
    </section>

    <!-- Filters Section -->
    <div class="grid grid-cols-3 gap-2 px-1 -mt-2">
        <flux:select wire:model.live="selectedCategory" placeholder="{{ __('Kategori') }}">
            <flux:select.option value="">{{ __('Semua') }}</flux:select.option>
            @foreach($categories as $cat)
                <flux:select.option :value="$cat">{{ strtoupper($cat) }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:select wire:model.live="selectedMonth" placeholder="{{ __('Bulan') }}">
            <flux:select.option value="">{{ __('Semua') }}</flux:select.option>
            <flux:select.option value="1">{{ __('Jan') }}</flux:select.option>
            <flux:select.option value="2">{{ __('Feb') }}</flux:select.option>
            <flux:select.option value="3">{{ __('Mar') }}</flux:select.option>
            <flux:select.option value="4">{{ __('Apr') }}</flux:select.option>
            <flux:select.option value="5">{{ __('Mei') }}</flux:select.option>
            <flux:select.option value="6">{{ __('Jun') }}</flux:select.option>
            <flux:select.option value="7">{{ __('Jul') }}</flux:select.option>
            <flux:select.option value="8">{{ __('Agu') }}</flux:select.option>
            <flux:select.option value="9">{{ __('Sep') }}</flux:select.option>
            <flux:select.option value="10">{{ __('Okt') }}</flux:select.option>
            <flux:select.option value="11">{{ __('Nov') }}</flux:select.option>
            <flux:select.option value="12">{{ __('Des') }}</flux:select.option>
        </flux:select>
        <flux:select wire:model.live="selectedYear" placeholder="{{ __('Tahun') }}">
            <flux:select.option value="">{{ __('Semua') }}</flux:select.option>
            @foreach($years as $yr)
                <flux:select.option :value="$yr">{{ $yr }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <!-- History List -->
    <div class="flex flex-col gap-3">
        @forelse($paidInvoices as $invoice)
            <div class="bg-surface-container-lowest p-normal rounded-xl border border-outline-variant shadow-sm flex items-stretch justify-between hover:bg-surface-container transition-colors group text-left">
                <div class="flex items-center gap-normal text-left">
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
                    <div class="flex flex-col text-left">
                        <p class="font-label-bold text-sm font-semibold text-on-surface leading-tight">{{ $invoice->billing_detail }}</p>
                        <div class="flex flex-col gap-0.5">
                            <p class="font-caption text-xs text-on-surface-variant">
                                {{ $invoice->student->name }} • {{ $invoice->updated_at->translatedFormat('d M Y') }}
                            </p>
                            @if($invoice->period_month && $invoice->period_year)
                                <p class="font-caption text-[10px] text-on-surface-variant/80">
                                    {{ __('Periode: :month :year', [
                                        'month' => Carbon\Carbon::create()->month($invoice->period_month)->translatedFormat('F'),
                                        'year' => $invoice->period_year
                                    ]) }}
                                </p>
                            @endif
                            <p class="font-display-lg text-sm font-bold text-secondary mt-1">
                                Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="text-right flex flex-col items-end justify-end gap-2">
                    <div class="flex gap-2">
                        @if($invoice->payments->first())
                            <flux:button 
                                as="a" 
                                :href="route('parent.payments.receipt', $invoice->payments->first())" 
                                target="_blank" 
                                size="xs" 
                                variant="ghost" 
                                icon="document-arrow-down"
                                class="!text-[10px] flex justify-center"
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
        @empty
            <div class="p-huge text-center text-on-surface-variant bg-surface-container-lowest rounded-xl border border-dashed border-outline-variant">
                {{ __('Belum ada riwayat pembayaran.') }}
            </div>
        @endforelse
    </div>
</div>
