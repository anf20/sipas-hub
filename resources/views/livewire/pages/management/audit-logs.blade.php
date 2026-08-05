<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <div>
            <flux:heading size="xl">{{ __('Audit Trail & Log Aktivitas') }}</flux:heading>
            <flux:subheading>{{ __('Daftar seluruh riwayat perubahan data pada sistem keuangan sekolah.') }}</flux:subheading>
        </div>
        <flux:spacer />
        <div class="hidden md:block font-bold text-sm text-slate-700 dark:text-zinc-300">
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
    </flux:header>

    <flux:main>
        <div class="space-y-4">
            <!-- Filter Section -->
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                    <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="{{ __('Cari user, IP, atau target...') }}" class="w-full sm:max-w-xs" />
                    
                    <flux:select wire:model.live="actionFilter" class="w-full sm:w-40" placeholder="{{ __('Semua Aksi') }}">
                        <flux:select.option value="">{{ __('Semua Aksi') }}</flux:select.option>
                        <flux:select.option value="created">{{ __('Created') }}</flux:select.option>
                        <flux:select.option value="updated">{{ __('Updated') }}</flux:select.option>
                        <flux:select.option value="deleted">{{ __('Deleted') }}</flux:select.option>
                    </flux:select>

                    @if($search || $actionFilter)
                        <flux:button variant="ghost" icon="x-mark" wire:click="clearFilters">{{ __('Hapus Filter') }}</flux:button>
                    @endif
                </div>
            </div>

            <!-- Table Section -->
            <flux:card class="p-0 overflow-hidden">
                <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Waktu') }}</flux:table.column>
                    <flux:table.column>{{ __('Pengguna') }}</flux:table.column>
                    <flux:table.column>{{ __('Aksi') }}</flux:table.column>
                    <flux:table.column>{{ __('Target') }}</flux:table.column>
                    <flux:table.column>{{ __('IP Address') }}</flux:table.column>
                    <flux:table.column class="text-right"></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($logs as $log)
                        @php
                            $badgeColor = match($log->action) {
                                'created' => 'green',
                                'updated' => 'blue',
                                'deleted' => 'red',
                                default => 'zinc',
                            };
                            $targetName = class_basename($log->model_type);
                        @endphp
                        <flux:table.row :key="$log->id">
                            <flux:table.cell class="whitespace-nowrap font-medium">
                                {{ $log->created_at->translatedFormat('d M Y H:i:s') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($log->user)
                                    <div>
                                        <div class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $log->user->name }}</div>
                                        <div class="text-xs text-zinc-500">{{ $log->user->email }}</div>
                                    </div>
                                @else
                                    <span class="text-zinc-400 italic font-normal">{{ __('System / Callback') }}</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" :color="$badgeColor" inset="top bottom">
                                    {{ strtoupper($log->action) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="font-semibold">{{ $targetName }}</div>
                                <div class="text-xs text-zinc-500">ID: {{ $log->model_id }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <code class="text-xs font-mono bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded text-zinc-600 dark:text-zinc-400">
                                    {{ $log->ip ?? '-' }}
                                </code>
                            </flux:table.cell>
                            <flux:table.cell class="text-right">
                                <flux:modal.trigger name="view-log-{{ $log->id }}">
                                    <flux:button variant="ghost" size="sm" icon="magnifying-glass" />
                                </flux:modal.trigger>

                                <!-- Audit Detail Modal -->
                                <flux:modal name="view-log-{{ $log->id }}" class="md:w-175">
                                    <div class="space-y-6 text-left">
                                        <div>
                                            <flux:heading size="lg">{{ __('Rincian Log Audit') }} #{{ $log->id }}</flux:heading>
                                            <flux:subheading>{{ __('Detail informasi perubahan data database.') }}</flux:subheading>
                                        </div>

                                        <!-- Metadata Grid -->
                                        <div class="grid grid-cols-2 gap-4 text-sm bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-xl border border-zinc-200/50 dark:border-zinc-800">
                                            <div>
                                                <div class="text-zinc-500 text-xs uppercase font-bold">{{ __('Waktu') }}</div>
                                                <div class="font-semibold mt-1">{{ $log->created_at->translatedFormat('d F Y - H:i:s') }}</div>
                                            </div>
                                            <div>
                                                <div class="text-zinc-500 text-xs uppercase font-bold">{{ __('Pengguna') }}</div>
                                                <div class="font-semibold mt-1">{{ $log->user->name ?? __('Sistem / Webhook') }}</div>
                                            </div>
                                            <div>
                                                <div class="text-zinc-500 text-xs uppercase font-bold">{{ __('Aksi & Target') }}</div>
                                                <div class="font-semibold mt-1">
                                                    <flux:badge size="sm" :color="$badgeColor">{{ strtoupper($log->action) }}</flux:badge>
                                                    {{ $targetName }} (ID: {{ $log->model_id }})
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-zinc-500 text-xs uppercase font-bold">{{ __('IP Address') }}</div>
                                                <div class="font-semibold mt-1 font-mono text-zinc-600 dark:text-zinc-400">{{ $log->ip ?? '-' }}</div>
                                            </div>
                                        </div>

                                        <!-- Comparison Box -->
                                        <div class="space-y-4">
                                            @if($log->action === 'updated')
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <flux:heading size="sm" class="mb-2 text-red-600 dark:text-red-400 font-semibold">{{ __('Sebelum Perubahan') }}</flux:heading>
                                                        <pre class="p-3 bg-zinc-900 dark:bg-black text-zinc-100 rounded-xl overflow-x-auto text-xs font-mono max-h-60 leading-relaxed">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                    </div>
                                                    <div>
                                                        <flux:heading size="sm" class="mb-2 text-green-600 dark:text-green-400 font-semibold">{{ __('Setelah Perubahan') }}</flux:heading>
                                                        <pre class="p-3 bg-zinc-900 dark:bg-black text-zinc-100 rounded-xl overflow-x-auto text-xs font-mono max-h-60 leading-relaxed">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                    </div>
                                                </div>
                                            @else
                                                <div>
                                                    <flux:heading size="sm" class="mb-2 text-zinc-700 dark:text-zinc-300 font-semibold">
                                                        {{ $log->action === 'created' ? __('Data yang Ditambahkan') : __('Data yang Dihapus') }}
                                                    </flux:heading>
                                                    <pre class="p-3 bg-zinc-900 dark:bg-black text-zinc-100 rounded-xl overflow-x-auto text-xs font-mono max-h-60 leading-relaxed">{{ json_encode($log->action === 'created' ? $log->new_values : $log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex justify-end">
                                            <flux:modal.close>
                                                <flux:button>{{ __('Tutup') }}</flux:button>
                                            </flux:modal.close>
                                        </div>
                                    </div>
                                </flux:modal>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center py-12 text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <flux:icon name="magnifying-glass" class="w-8 h-8 text-zinc-300 dark:text-zinc-600" />
                                    <span>{{ __('Tidak ada log audit ditemukan.') }}</span>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
            </flux:card>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        </div>
    </flux:main>
</div>
