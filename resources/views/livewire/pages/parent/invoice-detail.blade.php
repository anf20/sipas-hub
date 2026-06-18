<div class="flex flex-col gap-large">
    <!-- Back Button & Page Title -->
    <div class="flex items-center gap-small">
        <flux:button variant="ghost" icon="chevron-left" href="{{ route('parent.invoices') }}" wire:navigate />
        <h2 class="font-headline-md text-2xl font-semibold text-primary">{{ __('Detail Tagihan') }}</h2>
    </div>

    <!-- Invoice Card Details -->
    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant shadow-sm overflow-hidden flex flex-col">
        <!-- Status Header -->
        <div class="px-normal py-small flex items-center justify-between {{ $invoice->status === 'paid' ? 'bg-secondary-container/20' : 'bg-error-container/10' }}">
            <div class="flex items-center gap-2">
                @if($invoice->status === 'paid')
                    <flux:icon.check-circle variant="solid" class="size-5 text-on-secondary-container" />
                @else
                    <flux:icon.clock variant="solid" class="size-5 text-on-error-container" />
                @endif
                <span class="font-label-bold text-xs uppercase tracking-wider {{ $invoice->status === 'paid' ? 'text-on-secondary-container' : 'text-on-error-container' }}">
                    {{ $invoice->status === 'paid' ? __('Lunas') : __('Belum Bayar') }}
                </span>
            </div>
            <span class="font-label-md text-xs text-on-surface-variant">#INV-{{ $invoice->id }}</span>
        </div>

        <div class="p-normal flex flex-col gap-large">
            <!-- Amount Section -->
            <div class="flex flex-col items-center justify-center py-large border-b border-outline-variant/50">
                <p class="font-label-bold text-sm text-on-surface-variant mb-1">{{ __('Total Tagihan') }}</p>
                <h3 class="font-display-lg text-4xl font-bold text-primary">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</h3>
            </div>

            <!-- Student & Fee Info -->
            <div class="grid grid-cols-1 gap-normal">
                <div class="flex flex-col gap-0.5">
                    <p class="font-label-bold text-xs text-on-surface-variant uppercase tracking-tight">{{ __('Untuk Siswa') }}</p>
                    <p class="font-body-lg text-lg font-medium text-on-surface">{{ $invoice->student->name }}</p>
                    <p class="font-caption text-xs text-on-surface-variant italic">
                        @if($invoice->student->schoolClass)
                            {{ $invoice->student->schoolClass->name }}
                        @else
                            {{ __('Tingkat') }} {{ $invoice->student->current_grade }} ({{ __('Belum Ada Kelas') }})
                        @endif
                    </p>
                </div>

                <div class="flex flex-col gap-0.5">
                    <p class="font-label-bold text-xs text-on-surface-variant uppercase tracking-tight">{{ __('Jenis Tagihan') }}</p>
                    <p class="font-body-lg text-lg font-medium text-on-surface">{{ $invoice->feeType->name }}</p>
                </div>

                <div class="flex flex-col gap-0.5">
                    <p class="font-label-bold text-xs text-on-surface-variant uppercase tracking-tight">{{ __('Periode Tagihan') }}</p>
                    <p class="font-body-lg text-lg font-medium text-on-surface">
                        {{ $invoice->period_month ? Carbon\Carbon::create()->month($invoice->period_month)->translatedFormat('F') : '-' }} {{ $invoice->period_year }}
                    </p>
                </div>

                <div class="flex flex-col gap-0.5">
                    <p class="font-label-bold text-xs text-on-surface-variant uppercase tracking-tight">{{ __('Jatuh Tempo') }}</p>
                    <p class="font-body-lg text-lg font-medium text-on-surface text-error">
                        {{ $invoice->due_date->translatedFormat('d F Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Button Section -->
        @if($invoice->status === 'unpaid')
            <div class="p-normal bg-surface-container-low border-t border-outline-variant">
                <flux:button 
                    wire:click="initiatePayment" 
                    variant="primary" 
                    icon="banknotes"
                    class="w-full !rounded-2xl !py-4 shadow-lg active:scale-[0.98] transition-all"
                >
                    {{ __('Bayar Sekarang') }}
                </flux:button>
            </div>
        @else
             <div class="p-normal bg-surface-container-low border-t border-outline-variant">
                <div class="flex flex-col items-center justify-center gap-3 py-2">
                    <div class="flex items-center gap-2 text-on-secondary-container">
                        <flux:icon.check-badge variant="solid" class="text-secondary size-5" />
                        <span class="font-label-bold">{{ __('Tagihan ini sudah dibayar lunas.') }}</span>
                    </div>
                    @if($invoice->payments->first())
                        <flux:button 
                            as="a" 
                            :href="route('parent.payments.receipt', $invoice->payments->first())" 
                            target="_blank" 
                            variant="primary" 
                            size="sm"
                            icon="document-arrow-down"
                            class="!rounded-xl"
                        >
                            {{ __('Download Kwitansi (PDF)') }}
                        </flux:button>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Payment History (If any) -->
    @if($invoice->payments->count() > 0)
        <div class="flex flex-col gap-normal">
            <h3 class="font-title-sm text-lg font-semibold text-primary px-1">{{ __('Riwayat Pembayaran') }}</h3>
            <div class="flex flex-col gap-small">
                @foreach($invoice->payments as $payment)
                    <div class="bg-surface-container-lowest p-normal rounded-2xl border border-outline-variant shadow-sm flex items-center justify-between">
                        <div class="flex items-center gap-normal">
                            <div class="w-10 h-10 rounded-full bg-secondary-container/10 flex items-center justify-center">
                                <flux:icon.document-text variant="outline" class="text-on-secondary-container size-5" />
                            </div>
                            <div>
                                <p class="font-label-bold text-sm font-semibold text-on-surface">{{ __('Pembayaran Berhasil') }}</p>
                                <p class="font-caption text-xs text-on-surface-variant">{{ $payment->paid_at->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        <p class="font-title-md font-bold text-on-secondary-container">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
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

            <div class="space-y-4">
                <div class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ __('Rincian Tagihan') }}</div>
                <div class="flex justify-between items-start border-b border-zinc-100 dark:border-zinc-800 pb-2">
                    <div class="flex flex-col gap-0.5">
                        <span class="font-semibold text-sm">{{ $invoice->feeType->name }}</span>
                        <span class="text-xs text-zinc-500">{{ $invoice->student->name }}</span>
                    </div>
                    <span class="font-medium text-sm">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
                </div>
                
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
                <flux:button class="flex-1" variant="primary" icon="banknotes" wire:click="pay">{{ __('Bayar Sekarang') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
