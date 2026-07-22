<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('academic.dashboard') }}" wire:navigate>{{ __('Akademik') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Manajemen Kelas') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </flux:header>

    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <flux:heading size="xl">{{ __('Daftar Kelas') }}</flux:heading>
            <flux:button :href="route('academic.classes.create')" variant="primary" icon="plus" wire:navigate>{{ __('Tambah') }}</flux:button>
        </div>

        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari nama kelas...') }}" icon="magnifying-glass" clearable />
            </div>
            <div class="w-full md:w-64">
                <flux:select wire:model.live="yearFilter" placeholder="{{ __('Semua Tahun Ajaran') }}" clearable>
                    @foreach($years as $year)
                        <flux:select.option :value="$year->id">{{ $year->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        <flux:card class="p-0 overflow-hidden">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Nama Kelas') }}</flux:table.column>
                    <flux:table.column>{{ __('Tahun Ajaran') }}</flux:table.column>
                    <flux:table.column>{{ __('Siswa') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($classes as $class)
                        <flux:table.row :key="'class-'.$class->id">
                            <flux:table.cell font-weight="medium">{{ $class->name }}</flux:table.cell>
                            <flux:table.cell>{{ $class->academicYear->name }}</flux:table.cell>
                            <flux:table.cell>{{ $class->students_count }} / {{ $class->capacity }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2 justify-end">
                                    <flux:button variant="ghost" size="sm" icon="eye" :href="route('academic.classes.show', $class->id)" wire:navigate />
                                    <flux:button variant="ghost" size="sm" icon="pencil" :href="route('academic.classes.edit', $class->id)" wire:navigate />
                                    <flux:button variant="ghost" size="sm" icon="trash" wire:click="deleteClass({{ $class->id }})" wire:confirm="{{ __('Hapus kelas ini?') }}" />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="4" class="text-center py-6 text-zinc-500">
                                {{ __('Tidak ada data kelas yang ditemukan.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>

        <div class="mt-4">
            {{ $classes->links() }}
        </div>
    </div>
</div>
