<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <flux:header>
        <flux:heading size="xl">{{ __('Verifikasi Pembayaran Manual') }}</flux:heading>
    </flux:header>

    <flux:main>
        <div class="space-y-6">
            
            <!-- Filter & Search Card -->
            <flux:card class="border-amber-100 dark:border-amber-900/50 bg-amber-50/50 dark:bg-amber-900/10 shadow-sm">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="w-full md:w-1/2">
                        <flux:heading size="lg" class="mb-2">{{ __('Pencarian Pending') }}</flux:heading>
                        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Ketik Nama Siswa atau NIS...') }}" size="lg" />
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">{{ __('Total Menunggu Verifikasi') }}</span>
                        <span class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $payments->total() }} {{ __('Transaksi') }}</span>
                    </div>
                </div>
            </flux:card>

            <!-- Pending Payments Table -->
            <flux:card class="p-0 overflow-hidden shadow-sm">
                @if($payments->isEmpty())
                    <div class="p-12 text-center">
                        <div class="flex justify-center mb-4 text-zinc-400">
                            <flux:icon.check-circle size="lg" class="size-12" />
                        </div>
                        <flux:heading size="lg" class="mb-1">{{ __('Bersih! Tidak Ada Antrean') }}</flux:heading>
                        <flux:subheading>{{ __('Semua bukti transfer manual telah selesai diverifikasi.') }}</flux:subheading>
                    </div>
                @else
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>{{ __('Tanggal') }}</flux:table.column>
                            <flux:table.column>{{ __('Siswa & Kelas') }}</flux:table.column>
                            <flux:table.column>{{ __('Detail Tagihan') }}</flux:table.column>
                            <flux:table.column align="end">{{ __('Nominal') }}</flux:table.column>
                            <flux:table.column align="center">{{ __('Bukti') }}</flux:table.column>
                            <flux:table.column align="end">{{ __('Aksi') }}</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach($payments as $payment)
                                <flux:table.row :key="$payment->id">
                                    <flux:table.cell class="text-xs text-zinc-500">
                                        {{ $payment->created_at->translatedFormat('d M Y, H:i') }}
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $payment->invoice->student->name }}</span>
                                            <span class="text-xs text-zinc-500">{{ $payment->invoice->student->nis }} • {{ $payment->invoice->student->schoolClass->name ?? '-' }}</span>
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $payment->invoice->billing_detail }}</span>
                                            <span class="text-xs text-zinc-400 italic">No. Draf: {{ $payment->receipt_number }}</span>
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell align="end" class="font-bold text-zinc-900 dark:text-zinc-100">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </flux:table.cell>

                                    <flux:table.cell align="center">
                                        @if($payment->proof_file)
                                            <button wire:click="viewProof({{ $payment->id }})" class="group relative cursor-pointer block w-14 h-10 rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-700 shadow-xs hover:border-amber-400 transition-colors">
                                                <img src="{{ asset('storage/' . $payment->proof_file) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" alt="Bukti Transfer" />
                                                <div class="absolute inset-0 bg-black/35 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white transition-opacity duration-200">
                                                    <flux:icon.magnifying-glass variant="mini" class="size-3.5" />
                                                </div>
                                            </button>
                                        @else
                                            <span class="text-xs text-red-500 italic">{{ __('Tidak ada file') }}</span>
                                        @endif
                                    </flux:table.cell>

                                    <flux:table.cell align="end">
                                        <div class="flex justify-end gap-2">
                                            <flux:button 
                                                wire:click="approve({{ $payment->id }})" 
                                                variant="primary" 
                                                size="sm" 
                                                icon="check"
                                                class="!bg-emerald-600 hover:!bg-emerald-700 border-none cursor-pointer"
                                                wire:loading.attr="disabled"
                                            >
                                                {{ __('Setujui') }}
                                            </flux:button>
                                            <flux:button 
                                                wire:click="openRejectionModal({{ $payment->id }})" 
                                                variant="danger" 
                                                size="sm" 
                                                icon="x-mark"
                                                class="cursor-pointer"
                                                wire:loading.attr="disabled"
                                            >
                                                {{ __('Tolak') }}
                                            </flux:button>
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>

                    <div class="p-4 border-t border-zinc-100 dark:border-zinc-800">
                        {{ $payments->links() }}
                    </div>
                @endif
            </flux:card>
        </div>
    </flux:main>

    <!-- Modal 1: View Proof Image -->
    <flux:modal wire:model="showProofModal" class="max-w-2xl">
        <div class="space-y-6">
            <div class="flex justify-between items-center pb-3 border-b border-zinc-100 dark:border-zinc-800">
                <flux:heading size="lg">{{ __('Foto Bukti Transfer') }}</flux:heading>
            </div>
            
            @if($proofFileUrl)
                <div class="flex justify-center bg-zinc-50 dark:bg-zinc-950 p-2 rounded-2xl border border-zinc-150 dark:border-zinc-800 overflow-hidden max-h-[70vh]">
                    <img src="{{ $proofFileUrl }}" class="max-w-full max-h-[60vh] object-contain rounded-xl shadow-xs" alt="Bukti Transfer Berkas" />
                </div>
            @endif

            <div class="flex justify-between gap-3 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                @if($proofFileUrl)
                    <flux:button as="a" :href="$proofFileUrl" download target="_blank" icon="arrow-down-tray" variant="ghost">
                        {{ __('Download File') }}
                    </flux:button>
                @endif
                <flux:button class="px-6" variant="primary" wire:click="$set('showProofModal', false)">{{ __('Tutup') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal 2: Rejection Reason -->
    <flux:modal wire:model="showRejectionModal" class="min-w-[350px] max-w-[500px]">
        <form wire:submit.prevent="reject" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Tolak Bukti Pembayaran') }}</flux:heading>
                <flux:subheading>{{ __('Masukkan alasan penolakan. Pesan ini akan dikirimkan otomatis ke nomor WhatsApp orang tua.') }}</flux:subheading>
            </div>

            <div class="space-y-3">
                <flux:label>{{ __('Alasan Penolakan') }}</flux:label>
                <flux:input 
                    type="text" 
                    wire:model="rejectionReason" 
                    placeholder="{{ __('Contoh: Gambar bukti transfer buram / tidak terlihat jelas') }}" 
                    required 
                    autofocus
                />
                @error('rejectionReason') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-3 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                <flux:button class="flex-1" variant="ghost" wire:click="$set('showRejectionModal', false)">{{ __('Batal') }}</flux:button>
                <flux:button class="flex-1" type="submit" variant="danger" icon="x-mark" wire:loading.attr="disabled">
                    {{ __('Tolak Pembayaran') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
