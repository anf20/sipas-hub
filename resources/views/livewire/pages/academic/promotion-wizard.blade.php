<div class="space-y-6">
    <flux:heading size="lg">{{ __('Wizard Kenaikan / Perpindahan Kelas') }}</flux:heading>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Source Selection -->
        <flux:card class="space-y-4">
            <flux:heading size="sm">{{ __('Asal (Tahun Lama / Kelas Asal)') }}</flux:heading>
            <flux:select wire:model.live="sourceYearId" label="{{ __('Tahun Ajaran') }}" placeholder="{{ __('Pilih Tahun') }}">
                @foreach($years as $year)
                    <flux:select.option :value="$year->id">{{ $year->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="sourceClassId" label="{{ __('Kelas') }}" placeholder="{{ __('Pilih Kelas') }}" :disabled="!$sourceYearId">
                @foreach($sourceClasses as $class)
                    <flux:select.option :value="$class->id">{{ $class->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:card>

        <!-- Destination Selection -->
        <flux:card class="space-y-4">
            <flux:heading size="sm">{{ __('Tujuan (Tahun Baru / Kelas Tujuan)') }}</flux:heading>
            <flux:select wire:model.live="targetYearId" label="{{ __('Tahun Ajaran') }}" placeholder="{{ __('Pilih Tahun') }}">
                @foreach($years as $year)
                    <flux:select.option :value="$year->id">{{ $year->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="targetClassId" label="{{ __('Kelas Tujuan') }}" placeholder="{{ __('Pilih Kelas') }}" :disabled="!$targetYearId">
                @foreach($targetClasses as $class)
                    @php
                        $isFull = $class->students()->count() >= $class->capacity;
                    @endphp
                    <flux:select.option :value="$class->id" :disabled="$isFull">
                        {{ $class->name }} ({{ $class->students()->count() }}/{{ $class->capacity }})
                    </flux:select.option>
                @endforeach
            </flux:select>
        </flux:card>
    </div>

    @if($sourceClassId)
        <flux:card class="p-0 overflow-hidden">
            <div class="p-6 pb-0">
                <div class="flex justify-between items-center">
                    <flux:heading size="sm">{{ __('Daftar Siswa') }} ({{ count($studentsInSource) }})</flux:heading>
                    <flux:button variant="primary" wire:click="promote" :disabled="empty($selectedStudents) || !$targetClassId">
                        {{ __('Pindahkan / Naikkan :count Siswa', ['count' => count($selectedStudents)]) }}
                    </flux:button>
                </div>
            </div>

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>
                        <flux:checkbox wire:model.live="selectAll" x-on:click="$el.checked ? $wire.set('selectedStudents', @js($studentsInSource->pluck('id')->toArray())) : $wire.set('selectedStudents', [])" />
                    </flux:table.column>
                    <flux:table.column>{{ __('NIS') }}</flux:table.column>
                    <flux:table.column>{{ __('Nama Siswa') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($studentsInSource as $student)
                        <flux:table.row :key="'promote-'.$student->id">
                            <flux:table.cell>
                                <flux:checkbox wire:model.live="selectedStudents" :value="$student->id" />
                            </flux:table.cell>
                            <flux:table.cell>{{ $student->nis }}</flux:table.cell>
                            <flux:table.cell font-weight="medium">{{ $student->name }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    @endif
</div>
