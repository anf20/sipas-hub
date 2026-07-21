<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('academic.hub') }}" wire:navigate>{{ __('Akademik') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>
                @if($tab === 'overview') {{ __('Ringkasan') }}
                @elseif($tab === 'students') {{ __('Data Siswa') }}
                @elseif($tab === 'classes') {{ __('Manajemen Kelas') }}
                @elseif($tab === 'years') {{ __('Tahun Ajaran') }}
                @elseif($tab === 'promotion') {{ __('Kenaikan Kelas') }}
                @endif
            </flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:spacer />
        <div class="hidden md:block font-bold text-sm text-slate-700 dark:text-zinc-300">
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
    </flux:header>

    <flux:main>
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <flux:heading size="xl">{{ __('Manajemen Akademik') }}</flux:heading>
            </div>

            <flux:navlist variant="outline" class="flex-row gap-2 border-b border-zinc-200 dark:border-zinc-700 pb-0">
                <flux:navlist.item wire:click="$set('tab', 'overview')" :current="$tab === 'overview'" class="cursor-pointer">{{ __('Ringkasan') }}</flux:navlist.item>
                <flux:navlist.item wire:click="$set('tab', 'years')" :current="$tab === 'years'" class="cursor-pointer">{{ __('Tahun Ajaran') }}</flux:navlist.item>
                <flux:navlist.item wire:click="$set('tab', 'classes')" :current="$tab === 'classes'" class="cursor-pointer">{{ __('Kelas') }}</flux:navlist.item>
                <flux:navlist.item wire:click="$set('tab', 'students')" :current="$tab === 'students'" class="cursor-pointer">{{ __('Siswa') }}</flux:navlist.item>
                <flux:navlist.item wire:click="$set('tab', 'promotion')" :current="$tab === 'promotion'" class="cursor-pointer">{{ __('Kenaikan Kelas') }}</flux:navlist.item>
            </flux:navlist>

            <div class="mt-4">
                @if($tab === 'overview')
                    <div class="space-y-6">
                        <!-- Overview Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:card class="flex flex-col gap-2">
                                <flux:text size="sm" class="text-zinc-500">{{ __('Total Siswa Aktif') }}</flux:text>
                                <flux:heading size="xl">{{ number_format($totalStudents) }}</flux:heading>
                            </flux:card>
                            <flux:card class="flex flex-col gap-2">
                                <flux:text size="sm" class="text-zinc-500">{{ __('Total Kelas') }}</flux:text>
                                <flux:heading size="xl">{{ number_format($totalClasses) }}</flux:heading>
                            </flux:card>
                            <flux:card class="flex flex-col gap-2">
                                <flux:text size="sm" class="text-zinc-500">{{ __('Tahun Ajaran Aktif') }}</flux:text>
                                <flux:heading size="xl">{{ $activeYear?->name ?? '-' }}</flux:heading>
                            </flux:card>
                        </div>

                        <!-- Charts -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <flux:card>
                                <flux:heading size="lg" class="mb-4">{{ __('Distribusi Jenis Kelamin') }}</flux:heading>
                                <div id="genderChart" style="min-height: 300px;"></div>
                            </flux:card>
                            <flux:card>
                                <flux:heading size="lg" class="mb-4">{{ __('Siswa per Jenjang Kelas') }}</flux:heading>
                                <div id="gradeChart" style="min-height: 300px;"></div>
                            </flux:card>
                        </div>
                    </div>

                    @push('scripts')
                    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
                    <script>
                        document.addEventListener('livewire:navigated', () => {
                            if (window.location.search.includes('tab=overview') || !window.location.search.includes('tab=')) {
                                renderCharts();
                            }
                        });

                        function renderCharts() {
                            const genderElement = document.querySelector("#genderChart");
                            if (genderElement) {
                                genderElement.innerHTML = ''; // Prevent duplicate charts
                                new ApexCharts(genderElement, {
                                    chart: { type: 'pie', height: 300 },
                                    series: @json($genderValues),
                                    labels: @json($genderLabels),
                                    theme: { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' },
                                    colors: ['#2563eb', '#db2777']
                                }).render();
                            }

                            const gradeElement = document.querySelector("#gradeChart");
                            if (gradeElement) {
                                gradeElement.innerHTML = ''; // Prevent duplicate charts
                                new ApexCharts(gradeElement, {
                                    chart: { type: 'bar', height: 300 },
                                    series: [{ name: '{{ __("Siswa") }}', data: @json($gradeValues) }],
                                    xaxis: { categories: @json($gradeLabels) },
                                    theme: { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' },
                                    colors: ['#2563eb']
                                }).render();
                            }
                        }
                    </script>
                    @endpush
                @endif

                @if($tab === 'years')
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <flux:heading size="lg">{{ __('Daftar Tahun Ajaran') }}</flux:heading>
                            <flux:button :href="route('academic.years.create')" variant="primary" icon="plus" wire:navigate>{{ __('Tambah') }}</flux:button>
                        </div>
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
                    </div>
                @endif

                @if($tab === 'classes')
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <flux:heading size="lg">{{ __('Daftar Kelas') }}</flux:heading>
                            <flux:button :href="route('academic.classes.create')" variant="primary" icon="plus" wire:navigate>{{ __('Tambah') }}</flux:button>
                        </div>

                        <!-- Filters -->
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <flux:input wire:model.live.debounce.300ms="classSearch" placeholder="{{ __('Cari nama kelas...') }}" icon="magnifying-glass" clearable />
                            </div>
                            <div class="w-full md:w-64">
                                <flux:select wire:model.live="classYearFilter" placeholder="{{ __('Semua Tahun Ajaran') }}" clearable>
                                    @foreach($years as $year)
                                        <flux:select.option :value="$year->id">{{ $year->name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                        </div>

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
                                        <flux:table.cell colspan="4" class="text-center py-4 text-zinc-500">
                                            {{ __('Tidak ada data kelas yang ditemukan.') }}
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforelse
                            </flux:table.rows>
                        </flux:table>

                        <div class="mt-4">
                            {{ $classes->links() }}
                        </div>
                    </div>
                @endif

                @if($tab === 'students')
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <flux:heading size="lg">{{ __('Daftar Siswa') }}</flux:heading>
                            <flux:button :href="route('academic.students.create')" variant="primary" icon="plus" wire:navigate>{{ __('Tambah') }}</flux:button>
                        </div>

                        <!-- Filters -->
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <flux:input wire:model.live.debounce.300ms="studentSearch" placeholder="{{ __('Cari nama atau NIS...') }}" icon="magnifying-glass" clearable />
                            </div>
                            <div class="w-full md:w-64">
                                <flux:select wire:model.live="studentClassFilter" placeholder="{{ __('Semua Kelas') }}" clearable>
                                    @foreach($allClasses as $class)
                                        <flux:select.option :value="$class->id">{{ $class->name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                        </div>

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
                                                <flux:button variant="ghost" size="sm" icon="pencil" :href="route('academic.students.edit', $student->id)" wire:navigate />
                                                <flux:button variant="ghost" size="sm" icon="trash" wire:click="deleteStudent({{ $student->id }})" wire:confirm="{{ __('Hapus data siswa ini?') }}" />
                                            </div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @empty
                                    <flux:table.row>
                                        <flux:table.cell colspan="5" class="text-center py-4 text-zinc-500">
                                            {{ __('Tidak ada data siswa yang ditemukan.') }}
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforelse
                            </flux:table.rows>
                        </flux:table>

                        <div class="mt-4">
                            {{ $students->links() }}
                        </div>
                    </div>
                @endif

                @if($tab === 'promotion')
                    <livewire:student-promotion-manager />
                @endif
            </div>
        </div>
    </flux:main>
</div>
