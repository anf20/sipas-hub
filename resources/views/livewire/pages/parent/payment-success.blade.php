<div class="flex flex-col gap-huge items-center justify-center min-h-[70vh] px-4">
    <!-- Success Animation/Icon -->
    <div class="flex flex-col items-center gap-normal animate-in fade-in zoom-in duration-500">
        <div class="w-24 h-24 rounded-full bg-secondary-container/20 flex items-center justify-center">
            <flux:icon.check-circle variant="solid" class="text-secondary size-16" />
        </div>
        <div class="text-center space-y-2">
            <h2 class="font-headline-md text-3xl font-bold text-primary">{{ __('Pembayaran Berhasil!') }}</h2>
            <p class="font-body-md text-on-surface-variant max-w-xs mx-auto">
                {{ __('Terima kasih. Pembayaran Anda untuk :fee telah kami terima.', ['fee' => $payment->invoice->feeType->name]) }}
            </p>
        </div>
    </div>

    <!-- Payment Summary Card -->
    <section class="w-full bg-surface-container-lowest rounded-3xl border border-outline-variant shadow-sm overflow-hidden flex flex-col animate-in slide-in-from-bottom-4 duration-700 delay-200">
        <div class="p-normal space-y-4">
            <div class="flex justify-between items-center border-b border-outline-variant/50 pb-3">
                <span class="text-xs text-on-surface-variant uppercase font-bold tracking-wider">{{ __('Nomor Resi') }}</span>
                <span class="font-label-bold text-sm font-semibold text-primary">{{ $payment->receipt_number }}</span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-sm text-on-surface-variant">{{ __('Siswa') }}</span>
                <span class="text-sm font-medium text-on-surface">{{ $payment->invoice->student->name }}</span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-sm text-on-surface-variant">{{ __('Metode') }}</span>
                <span class="text-sm font-medium text-on-surface uppercase">{{ $payment->method }}</span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-sm text-on-surface-variant">{{ __('Waktu') }}</span>
                <span class="text-sm font-medium text-on-surface">{{ $payment->paid_at->translatedFormat('d M Y, H:i') }}</span>
            </div>

            <div class="pt-2 mt-2 border-t border-dashed border-outline-variant flex justify-between items-center">
                <span class="font-bold text-on-surface">{{ __('Total Bayar') }}</span>
                <span class="font-display-lg text-xl font-bold text-primary">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
            </div>
        </div>
    </section>

    <!-- Action Buttons -->
    <div class="w-full flex flex-col gap-small">
        <flux:button 
            as="a" 
            :href="route('parent.payments.receipt', $payment)" 
            target="_blank" 
            variant="primary" 
            class="w-full !rounded-2xl !py-4 shadow-lg flex justify-center items-center gap-2"
        >
            <flux:icon.document-text variant="outline" class="size-5" />
            {{ __('Download Kwitansi (PDF)') }}
        </flux:button>
        
        <flux:button 
            as="a" 
            :href="route('parent.dashboard')" 
            variant="ghost" 
            class="w-full !rounded-2xl !py-3 flex justify-center items-center"
            wire:navigate
        >
            {{ __('Kembali ke Dashboard') }}
        </flux:button>
    </div>
</div>
