<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('academic.hub') }}" wire:navigate>{{ __('Akademik') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('academic.hub', ['tab' => 'years']) }}" wire:navigate>{{ __('Tahun Ajaran') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:spacer />
        <div class="hidden md:block font-bold text-sm text-slate-700 dark:text-zinc-300">
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
    </flux:header>

    <flux:main>
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <flux:heading size="xl">{{ __('Daftar Tahun Ajaran') }}</flux:heading>
                <flux:button :href="route('academic.years.create')" variant="primary" icon="plus" wire:navigate>{{ __('Tambah Tahun Ajaran') }}</flux:button>
            </div>

            @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    {{ session('error') }}
                </div>
            @endif
             
             <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Tahun Ajaran') }}</flux:table.column>
                    <flux:table.column>{{ __('Tanggal Mulai') }}</flux:table.column>
                    <flux:table.column>{{ __('Tanggal Selesai') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($years as $year)
                        <flux:table.row :key="$year->id">
                            <flux:table.cell font-weight="medium">{{ $year->name }}</flux:table.cell>
                            <flux:table.cell>{{ \Carbon\Carbon::parse($year->start_date)->format('d M Y') }}</flux:table.cell>
                            <flux:table.cell>{{ \Carbon\Carbon::parse($year->end_date)->format('d M Y') }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$year->is_active ? 'green' : 'gray'">
                                    {{ $year->is_active ? __('Aktif') : __('Nonaktif') }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2">
                                    <flux:button variant="ghost" size="sm" wire:click="toggleStatus({{ $year->id }})" 
                                        :icon="$year->is_active ? 'x-circle' : 'check-circle'" 
                                        :tooltip="$year->is_active ? __('Nonaktifkan') : __('Aktifkan')" />
                                    
                                    <flux:button variant="ghost" size="sm" icon="pencil" :href="route('academic.years.edit', $year->id)" wire:navigate />
                                    
                                    <flux:modal.trigger name="delete-year-{{ $year->id }}">
                                        <flux:button variant="ghost" size="sm" icon="trash" />
                                    </flux:modal.trigger>

                                    <flux:modal name="delete-year-{{ $year->id }}" class="min-w-[22rem]">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">{{ __('Hapus Tahun Ajaran?') }}</flux:heading>
                                                <flux:subheading>{{ __('Tindakan ini tidak dapat dibatalkan.') }}</flux:subheading>
                                            </div>
                                            <div class="flex gap-2">
                                                <flux:spacer />
                                                <flux:modal.close>
                                                    <flux:button variant="ghost">{{ __('Batal') }}</flux:button>
                                                </flux:modal.close>
                                                <flux:button variant="danger" wire:click="delete({{ $year->id }})" wire:loading.attr="disabled">{{ __('Hapus') }}</flux:button>
                                            </div>
                                        </div>
                                    </flux:modal>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
             </flux:table>

             @if($years->isEmpty())
                <flux:text class="text-center py-8">{{ __('Belum ada data tahun ajaran.') }}</flux:text>
             @endif
        </div>
    </flux:main>
</div>
