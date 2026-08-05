<div>
    <flux:breadcrumbs class="mb-4">
        <flux:breadcrumbs.item href="{{ route('finance.hub') }}" wire:navigate>{{ __('Keuangan') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('finance.hub', ['tab' => $feeType->category === 'SPP' ? 'spp' : 'fees']) }}" wire:navigate>{{ $feeType->category === 'SPP' ? __('Manajemen SPP') : __('Tagihan Lainnya') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ __('Blast Tagihan: ' . $feeType->name) }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:card class="mb-6">
        <flux:heading size="lg">Notifikasi Tagihan: {{ $feeType->name }}</flux:heading>
        <flux:subheading>Kirim pesan peringatan khusus untuk tagihan ini ke semua wali murid yang belum melunasi.</flux:subheading>
        
        <div class="mt-6 flex gap-4">
            <div class="bg-surface-container p-4 rounded-xl flex-1 border border-outline-variant text-center">
                <div class="text-sm text-on-surface-variant mb-1">Target Wali Murid (Tertunggak)</div>
                <div class="text-2xl font-bold text-primary">{{ $summary->total_invoices ?? 0 }}</div>
            </div>
            <div class="bg-surface-container p-4 rounded-xl flex-1 border border-outline-variant text-center">
                <div class="text-sm text-on-surface-variant mb-1">Total Nominal Tunggakan</div>
                <div class="text-2xl font-bold text-primary">Rp {{ number_format($summary->total_amount ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
        
        <div class="mt-6">
            <div class="mb-2 flex items-center justify-between">
                <flux:heading size="sm">Template Pesan WhatsApp</flux:heading>
                <span class="inline-flex items-center gap-1.5 py-1 px-2 rounded-md text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                    <flux:icon.exclamation-triangle class="size-3" /> Penting: Jangan ubah teks berawalan {
                </span>
            </div>
            
            <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900/20 dark:border-blue-800">
                <flux:text size="sm" class="text-blue-800 dark:text-blue-200">
                    <strong>Peringatan!</strong> Anda bebas mengubah kalimat sapaan, penutup, maupun menyisipkan emoji. Namun, biarkan variabel ini tetap utuh agar sistem bisa menyisipkan data secara otomatis:
                    <ul class="list-disc ml-5 mt-1 font-mono text-xs">
                        <li>{fee_name} : Nama Tagihan (otomatis menjadi "{{ $feeType->name }}")</li>
                        @if($feeType->category === 'SPP' && $monthName)
                        <li>{month_name} : Nama Bulan Tagihan (otomatis menjadi "{{ $monthName }}")</li>
                        @endif
                        <li>{student_details} : Daftar nama anak beserta nominal per anak</li>
                        <li>{total_amount} : Total tunggakan dari semua anak</li>
                    </ul>
                </flux:text>
            </div>

            <flux:textarea wire:model="customMessage" rows="8" class="font-mono text-sm" />
            <flux:error name="customMessage" />
        </div>

        <div class="mt-6 flex justify-end gap-3">
            @if($batchId && $progress < 100 && !$failedJobs)
                <flux:button wire:click="cancelBatch" variant="danger">Hentikan Proses (Cancel)</flux:button>
            @endif
            <flux:button wire:click="startBlast" wire:loading.attr="disabled" variant="primary">
                <span wire:loading.remove wire:target="startBlast">Kirim {{ $summary->total_invoices ?? 0 }} Pesan Sekarang</span>
                <span wire:loading wire:target="startBlast">Mempersiapkan...</span>
            </flux:button>
        </div>
    </flux:card>

    @if($batchId)
        <flux:card class="mb-6" wire:poll.1s="updateBatchStatus">
            <flux:heading size="md" class="mb-4">Status Pengiriman (Live)</flux:heading>
            
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-semibold text-on-surface">Progres Keseluruhan</span>
                <span class="text-sm font-bold text-primary">{{ $progress }}%</span>
            </div>
            <div class="w-full bg-surface-container-highest rounded-full h-3 mb-6 overflow-hidden border border-outline-variant">
                <div class="bg-primary h-3 rounded-full transition-all duration-500 ease-out" style="width: {{ $progress }}%"></div>
            </div>

            <div class="grid grid-cols-4 gap-4 text-center">
                <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-sm">
                    <div class="text-xs text-on-surface-variant">Total Antrean</div>
                    <div class="text-xl font-bold mt-1">{{ $totalJobs }}</div>
                </div>
                <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-sm">
                    <div class="text-xs text-on-surface-variant">Sedang Proses</div>
                    <div class="text-xl font-bold mt-1 text-yellow-600">{{ $pendingJobs }}</div>
                </div>
                <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-sm">
                    <div class="text-xs text-on-surface-variant">Berhasil (Terkirim)</div>
                    <div class="text-xl font-bold mt-1 text-green-600">{{ max(0, $processedJobs - $failedJobs) }}</div>
                </div>
                <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-sm">
                    <div class="text-xs text-on-surface-variant">Gagal</div>
                    <div class="text-xl font-bold mt-1 text-red-600">{{ $failedJobs }}</div>
                </div>
            </div>
        </flux:card>
    @endif

    <flux:card class="p-0 overflow-hidden">
        <div class="p-6 pb-0">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <flux:heading size="md">Log Pengiriman (Global)</flux:heading>
                <div class="flex gap-2 w-full md:w-auto">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama / WA..." icon="magnifying-glass" class="w-full md:w-64" />
                    <flux:select wire:model.live="statusFilter" placeholder="Semua Status" class="w-full md:w-40">
                        <flux:select.option value="">Semua Status</flux:select.option>
                        <flux:select.option value="sent">Terkirim</flux:select.option>
                        <flux:select.option value="failed">Gagal</flux:select.option>
                        <flux:select.option value="pending">Tertunda</flux:select.option>
                    </flux:select>
                </div>
            </div>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Waktu') }}</flux:table.column>
                <flux:table.column>{{ __('Wali Murid') }}</flux:table.column>
                <flux:table.column>{{ __('Nomor Tujuan') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column>{{ __('Keterangan') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($recentLogs as $log)
                    <flux:table.row :key="'log-'.$log->id">
                        <flux:table.cell class="whitespace-nowrap">{{ $log->created_at->format('d M H:i:s') }}</flux:table.cell>
                        <flux:table.cell font-weight="semibold">{{ $log->user->name ?? 'Unknown' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-zinc-500">{{ $log->phone }}</flux:table.cell>
                        <flux:table.cell>
                            @if($log->status === 'sent')
                                <flux:badge color="green" size="sm" inset="top bottom">Terkirim</flux:badge>
                            @elseif($log->status === 'failed')
                                <flux:badge color="red" size="sm" inset="top bottom">Gagal</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm" inset="top bottom">{{ ucfirst($log->status) }}</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-xs text-red-600 max-w-[200px] truncate" title="{{ $log->error_message }}">
                            {{ $log->error_message ?? '-' }}
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center text-zinc-500 py-8">
                            {{ __('Belum ada riwayat pengiriman yang cocok.') }}
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $recentLogs->links() }}
        </div>
    </flux:card>
</div>
