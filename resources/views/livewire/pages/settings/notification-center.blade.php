<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('settings.notifications') }}" wire:navigate>{{ __('Pengaturan') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Pusat Notifikasi') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </flux:header>

    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <flux:heading size="xl">{{ __('Pusat Notifikasi Global') }}</flux:heading>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <flux:card class="flex flex-col gap-2">
                <flux:text size="sm" class="text-zinc-500">{{ __('Total Pesan') }}</flux:text>
                <flux:heading size="xl">{{ number_format($totalLogs) }}</flux:heading>
            </flux:card>
            <flux:card class="flex flex-col gap-2">
                <flux:text size="sm" class="text-zinc-500">{{ __('Berhasil Terkirim') }}</flux:text>
                <flux:heading size="xl" class="text-emerald-600">{{ number_format($sentLogs) }}</flux:heading>
            </flux:card>
            <flux:card class="flex flex-col gap-2">
                <flux:text size="sm" class="text-zinc-500">{{ __('Gagal / Error') }}</flux:text>
                <flux:heading size="xl" class="text-red-600">{{ number_format($failedLogs) }}</flux:heading>
            </flux:card>
        </div>

        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-4 mt-4">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari nomor atau nama...') }}" icon="magnifying-glass" clearable />
            </div>
            <div class="w-full md:w-48">
                <flux:select wire:model.live="statusFilter" placeholder="{{ __('Semua Status') }}" clearable>
                    <flux:select.option value="sent">{{ __('Sent') }}</flux:select.option>
                    <flux:select.option value="failed">{{ __('Failed') }}</flux:select.option>
                    <flux:select.option value="pending">{{ __('Pending') }}</flux:select.option>
                </flux:select>
            </div>
            <div class="w-full md:w-64">
                <flux:select wire:model.live="feeTypeFilter" placeholder="{{ __('Semua Kategori') }}" clearable>
                    <flux:select.option value="null">{{ __('Lainnya (Tanpa Kategori)') }}</flux:select.option>
                    @foreach($feeTypes as $fee)
                        <flux:select.option :value="$fee->id">{{ $fee->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        <flux:card class="p-0 overflow-hidden">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Waktu') }}</flux:table.column>
                    <flux:table.column>{{ __('Penerima') }}</flux:table.column>
                    <flux:table.column>{{ __('Kategori') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column>{{ __('Keterangan') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($logs as $log)
                        <flux:table.row :key="'log-'.$log->id">
                            <flux:table.cell class="whitespace-nowrap">{{ $log->created_at->format('d M Y, H:i') }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="font-medium">{{ $log->user->name ?? 'Unknown' }}</div>
                                <div class="text-sm text-zinc-500">{{ $log->phone }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($log->feeType)
                                    <flux:badge size="sm" color="blue">{{ $log->feeType->name }}</flux:badge>
                                @else
                                    <flux:badge size="sm" color="zinc">{{ __('Lainnya') }}</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$log->status === 'sent' ? 'green' : ($log->status === 'failed' ? 'red' : 'yellow')">
                                    {{ ucfirst($log->status) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($log->status === 'failed')
                                    <span class="text-red-500 text-xs">{{ $log->error_message }}</span>
                                @else
                                    <span class="text-zinc-500 text-xs">-</span>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-6 text-zinc-500">
                                {{ __('Belum ada riwayat notifikasi.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
