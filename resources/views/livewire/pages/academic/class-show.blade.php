<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('academic.hub') }}" wire:navigate>{{ __('Akademik') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('academic.hub', ['tab' => 'classes']) }}" wire:navigate>{{ __('Manajemen Kelas') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $schoolClass->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:spacer />
        <div class="hidden md:block font-bold text-sm text-slate-700 dark:text-zinc-300">
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
    </flux:header>

    <flux:main>
        <div class="space-y-6">
            <div class="flex justify-between items-start">
                <div>
                    <flux:heading size="xl">{{ $schoolClass->name }}</flux:heading>
                    <flux:text>{{ __('Tahun Ajaran :year', ['year' => $schoolClass->academicYear->name]) }}</flux:text>
                </div>
                <div class="flex gap-2">
                    <flux:button :href="route('academic.classes.edit', $schoolClass->id)" icon="pencil" wire:navigate>{{ __('Edit Kelas') }}</flux:button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Class Info -->
                <div class="md:col-span-1 space-y-6">
                    <flux:card>
                        <flux:heading size="lg" class="mb-4">{{ __('Informasi Kelas') }}</flux:heading>
                        <div class="space-y-4">
                            <div>
                                <flux:text size="sm" class="text-zinc-500">{{ __('Tingkat / Grade') }}</flux:text>
                                <flux:heading size="sm">{{ $schoolClass->grade }}</flux:heading>
                            </div>
                            <div>
                                <flux:text size="sm" class="text-zinc-500">{{ __('Jurusan') }}</flux:text>
                                <flux:heading size="sm">{{ $schoolClass->major ?? '-' }}</flux:heading>
                            </div>
                            <div>
                                <flux:text size="sm" class="text-zinc-500">{{ __('Wali Kelas') }}</flux:text>
                                <flux:heading size="sm">{{ $schoolClass->homeroomTeacher?->name ?? __('Belum ditentukan') }}</flux:heading>
                            </div>
                            <div>
                                <flux:text size="sm" class="text-zinc-500">{{ __('Kapasitas') }}</flux:text>
                                <flux:heading size="sm">{{ $schoolClass->students->count() }} / {{ $schoolClass->capacity }}</flux:heading>
                            </div>
                        </div>
                    </flux:card>

                    <flux:card>
                        <flux:heading size="lg" class="mb-4">{{ __('Statistik') }}</flux:heading>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <flux:text>{{ __('Laki-laki') }}</flux:text>
                                <flux:badge color="blue">{{ $students->where('gender', 'L')->count() }}</flux:badge>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:text>{{ __('Perempuan') }}</flux:text>
                                <flux:badge color="pink">{{ $students->where('gender', 'P')->count() }}</flux:badge>
                            </div>
                        </div>
                    </flux:card>
                </div>

                <!-- Student Roster -->
                <div class="md:col-span-2">
                    <flux:card>
                        <div class="flex justify-between items-center mb-4">
                            <flux:heading size="lg">{{ __('Daftar Siswa') }}</flux:heading>
                        </div>

                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('NIS') }}</flux:table.column>
                                <flux:table.column>{{ __('Nama') }}</flux:table.column>
                                <flux:table.column>{{ __('L/P') }}</flux:table.column>
                                <flux:table.column></flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @forelse($students as $student)
                                    <flux:table.row :key="'student-'.$student->id">
                                        <flux:table.cell>{{ $student->nis }}</flux:table.cell>
                                        <flux:table.cell font-weight="medium">{{ $student->name }}</flux:table.cell>
                                        <flux:table.cell>{{ $student->gender }}</flux:table.cell>
                                        <flux:table.cell>
                                            <div class="flex gap-2 justify-end">
                                                <flux:button variant="ghost" size="sm" icon="eye" :href="route('academic.students.show', $student->id)" wire:navigate />
                                            </div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @empty
                                    <flux:table.row>
                                        <flux:table.cell colspan="4" class="text-center py-4 text-zinc-500">
                                            {{ __('Tidak ada siswa di kelas ini.') }}
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforelse
                            </flux:table.rows>
                        </flux:table>
                    </flux:card>
                </div>
            </div>
        </div>
    </flux:main>
</div>
