<div>
    <flux:breadcrumbs class="mb-4">
        <flux:breadcrumbs.item href="{{ route('finance.hub') }}" wire:navigate>{{ __('Keuangan') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ __('Broadcast Pengumuman') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:card class="mb-6">
        <flux:heading size="lg">{{ __('Broadcast Pengumuman Umum WhatsApp') }}</flux:heading>
        <flux:subheading>{{ __('Kirim informasi umum (undangan rapat, pengumuman libur, maklumat penting) ke seluruh wali santri secara massal.') }}</flux:subheading>
        
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Target Selection -->
            <div>
                <flux:select wire:model.live="target" label="{{ __('Target Penerima') }}" placeholder="{{ __('Pilih target...') }}">
                    <flux:select.option value="all">{{ __('Semua Wali Santri') }}</flux:select.option>
                    <flux:select.option value="class">{{ __('Filter Berdasarkan Kelas') }}</flux:select.option>
                </flux:select>
            </div>

            <!-- Class Filter (Conditional) -->
            @if($target === 'class')
                <div class="transition-all duration-300">
                    <flux:select wire:model="classId" label="{{ __('Pilih Kelas') }}" placeholder="{{ __('Pilih kelas target...') }}">
                        @foreach($classes as $class)
                            <flux:select.option value="{{ $class->id }}">{{ $class->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="classId" />
                </div>
            @endif
        </div>

        <div class="mt-6">
            <flux:heading size="sm" class="mb-2">{{ __('Isi Pesan Pengumuman') }}</flux:heading>
            <flux:textarea wire:model="messageText" rows="8" class="font-mono text-sm" placeholder="{{ __('Tuliskan pesan pengumuman di sini...') }}" />
            <flux:error name="messageText" />
        </div>

        <div class="mt-6 flex justify-end gap-3">
            @if($batchId && $progress < 100 && !$failedJobs)
                <flux:button wire:click="cancelBatch" variant="danger">{{ __('Hentikan Proses (Cancel)') }}</flux:button>
            @endif
            <flux:button wire:click="startBroadcast" wire:loading.attr="disabled" variant="primary">
                <span wire:loading.remove wire:target="startBroadcast">{{ __('Kirim Broadcast Sekarang') }}</span>
                <span wire:loading wire:target="startBroadcast">{{ __('Mempersiapkan...') }}</span>
            </flux:button>
        </div>
    </flux:card>

    <!-- Progress Card (Visible when batch is active) -->
    @if($batchId)
        <flux:card class="mb-6" wire:poll.1s="updateBatchStatus">
            <flux:heading size="md" class="mb-4">{{ __('Status Pengiriman (Live)') }}</flux:heading>
            
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-semibold text-on-surface">{{ __('Progres Keseluruhan') }}</span>
                <span class="text-sm font-bold text-primary">{{ $progress }}%</span>
            </div>
            <div class="w-full bg-surface-container-highest rounded-full h-3 mb-6 overflow-hidden border border-outline-variant">
                <div class="bg-primary h-3 rounded-full transition-all duration-500 ease-out" style="width: {{ $progress }}%"></div>
            </div>

            <div class="grid grid-cols-4 gap-4 text-center">
                <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-sm">
                    <div class="text-xs text-on-surface-variant">{{ __('Total Antrean') }}</div>
                    <div class="text-xl font-bold mt-1">{{ $totalJobs }}</div>
                </div>
                <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-sm">
                    <div class="text-xs text-on-surface-variant">{{ __('Sedang Proses') }}</div>
                    <div class="text-xl font-bold mt-1 text-yellow-600">{{ $pendingJobs }}</div>
                </div>
                <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-sm">
                    <div class="text-xs text-on-surface-variant">{{ __('Berhasil (Terkirim)') }}</div>
                    <div class="text-xl font-bold mt-1 text-green-600">{{ max(0, $processedJobs - $failedJobs) }}</div>
                </div>
                <div class="bg-surface-container-lowest p-3 rounded-lg border border-outline-variant shadow-sm">
                    <div class="text-xs text-on-surface-variant">{{ __('Gagal') }}</div>
                    <div class="text-xl font-bold mt-1 text-red-600">{{ $failedJobs }}</div>
                </div>
            </div>
        </flux:card>
    @endif

    <!-- Logs Table -->
    <flux:card>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <flux:heading size="md">{{ __('Riwayat Pengumuman') }}</flux:heading>
            <div class="flex gap-2 w-full md:w-auto">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari nama / WA...') }}" icon="magnifying-glass" class="w-full md:w-64" />
                <flux:select wire:model.live="statusFilter" placeholder="{{ __('Semua Status') }}" class="w-full md:w-40">
                    <flux:select.option value="">{{ __('Semua Status') }}</flux:select.option>
                    <flux:select.option value="sent">{{ __('Terkirim') }}</flux:select.option>
                    <flux:select.option value="failed">{{ __('Gagal') }}</flux:select.option>
                    <flux:select.option value="pending">{{ __('Tertunda') }}</flux:select.option>
                </flux:select>
            </div>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Waktu') }}</flux:table.column>
                <flux:table.column>{{ __('Wali Murid') }}</flux:table.column>
                <flux:table.column>{{ __('Nomor Tujuan') }}</flux:table.column>
                <flux:table.column>{{ __('Isi Pesan') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column>{{ __('Keterangan') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($recentLogs as $log)
                    <flux:table.row :key="'log-'.$log->id">
                        <flux:table.cell class="whitespace-nowrap">{{ $log->created_at->format('d M H:i:s') }}</flux:table.cell>
                        <flux:table.cell font-weight="semibold">{{ $log->user->name ?? 'Unknown' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-zinc-500">{{ $log->phone }}</flux:table.cell>
                        <flux:table.cell class="max-w-62.5 truncate" title="{{ $log->payload['message'] ?? '-' }}">
                            {{ $log->payload['message'] ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($log->status === 'sent')
                                <flux:badge color="green" size="sm" inset="top bottom">{{ __('Terkirim') }}</flux:badge>
                            @elseif($log->status === 'failed')
                                <flux:badge color="red" size="sm" inset="top bottom">{{ __('Gagal') }}</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm" inset="top bottom">{{ ucfirst($log->status) }}</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-xs text-red-600 max-w-37.5 truncate" title="{{ $log->error_message }}">
                            {{ $log->error_message ?? '-' }}
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center text-zinc-500 py-8">
                            {{ __('Belum ada riwayat pengumuman yang cocok.') }}
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
