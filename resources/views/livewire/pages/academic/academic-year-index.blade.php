<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('academic.dashboard') }}" wire:navigate>{{ __('Akademik') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Tahun Ajaran') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </flux:header>

    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <flux:heading size="xl">{{ __('Daftar Tahun Ajaran') }}</flux:heading>
            <flux:button :href="route('academic.years.create')" variant="primary" icon="plus" wire:navigate>{{ __('Tambah') }}</flux:button>
        </div>
        
        <flux:card class="p-0 overflow-hidden">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Nama') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column>{{ __('Kelas Aktif') }}</flux:table.column>
                    <flux:table.column>{{ __('Siswa Aktif') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($years as $year)
                        <flux:table.row :key="'year-'.$year->id">
                            <flux:table.cell font-weight="medium">{{ $year->name }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$year->is_active ? 'green' : 'gray'">
                                    {{ $year->is_active ? __('Aktif') : __('Nonaktif') }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $year->school_classes_count }}</flux:table.cell>
                            <flux:table.cell>{{ $year->students_count }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2 justify-end">
                                    <flux:button variant="ghost" size="sm" wire:click="toggleYearStatus({{ $year->id }})" :icon="$year->is_active ? 'x-circle' : 'check-circle'" />
                                    <flux:button variant="ghost" size="sm" icon="pencil" :href="route('academic.years.edit', $year->id)" wire:navigate />
                                    <flux:button variant="ghost" size="sm" icon="trash" wire:click="deleteYear({{ $year->id }})" wire:confirm="{{ __('Hapus tahun ajaran ini?') }}" />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</div>
