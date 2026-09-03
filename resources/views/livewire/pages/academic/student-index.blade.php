<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('academic.dashboard') }}" wire:navigate>{{ __('Akademik') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Data Siswa') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </flux:header>

    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <flux:heading size="xl">{{ __('Daftar Siswa') }}</flux:heading>
            @hasanyrole(['Super Admin', 'Admin Akademik'])
            <div class="flex gap-2">
                <flux:button :href="route('academic.import', ['type' => 'students'])" variant="subtle" icon="document-arrow-up" wire:navigate>{{ __('Import Excel') }}</flux:button>
                <flux:button :href="route('academic.students.create')" variant="primary" icon="plus" wire:navigate>{{ __('Tambah') }}</flux:button>
            </div>
            @endhasanyrole
        </div>

        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari nama atau NIS...') }}" icon="magnifying-glass" clearable />
            </div>
            <div class="w-full md:w-64">
                <flux:select wire:model.live="classFilter" placeholder="{{ __('Semua Kelas') }}" clearable>
                    <flux:select.option value="">{{ __('Semua Kelas') }}</flux:select.option>
                    @foreach($classesByGrade as $grade => $classes)
                        <flux:select.option value="grade:{{ $grade }}">{{ __('Semua Kelas (Grade :grade)', ['grade' => $grade]) }}</flux:select.option>
                        @foreach($classes as $class)
                            <flux:select.option :value="$class->id">&nbsp;&nbsp;&nbsp;&nbsp;{{ $class->name }}</flux:select.option>
                        @endforeach
                    @endforeach
                </flux:select>
            </div>
        </div>

        <flux:card class="p-0 overflow-hidden">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('NIS') }}</flux:table.column>
                    <flux:table.column>{{ __('Nama') }}</flux:table.column>
                    <flux:table.column>{{ __('Kelas') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($students as $student)
                        <flux:table.row :key="'student-'.$student->id">
                            <flux:table.cell>{{ $student->nis }}</flux:table.cell>
                            <flux:table.cell font-weight="medium">{{ $student->name }}</flux:table.cell>
                            <flux:table.cell>{{ $student->schoolClass?->name ?? '-' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$student->status === 'aktif' ? 'green' : 'gray'">
                                    {{ ucfirst($student->status) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2 justify-end">
                                    <flux:button variant="ghost" size="sm" icon="eye" :href="route('academic.students.show', $student->id)" wire:navigate />
                                    @hasanyrole(['Super Admin', 'Admin Akademik'])
                                    <flux:button variant="ghost" size="sm" icon="pencil" :href="route('academic.students.edit', $student->id)" wire:navigate />
                                    <flux:button variant="ghost" size="sm" icon="trash" wire:click="deleteStudent({{ $student->id }})" wire:confirm="{{ __('Hapus data siswa ini?') }}" />
                                    @endhasanyrole
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-6 text-zinc-500">
                                {{ __('Tidak ada data siswa yang ditemukan.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>

        <div class="mt-4">
            {{ $students->links() }}
        </div>
    </div>
</div>
