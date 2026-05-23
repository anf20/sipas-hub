<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:heading size="xl">{{ __('Data Kelas') }}</flux:heading>
    </flux:header>

    <flux:main>
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <flux:button :href="route('academic.classes.create')" variant="primary" icon="plus" wire:navigate>{{ __('Tambah Kelas') }}</flux:button>
                <flux:button :href="route('academic.years.index')" variant="ghost" icon="calendar" wire:navigate>{{ __('Kelola Tahun Ajaran') }}</flux:button>
            </div>

            @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    {{ session('error') }}
                </div>
            @endif
             
             <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Nama Kelas') }}</flux:table.column>
                    <flux:table.column>{{ __('Jenjang') }}</flux:table.column>
                    <flux:table.column>{{ __('Tahun Ajaran') }}</flux:table.column>
                    <flux:table.column>{{ __('Wali Kelas') }}</flux:table.column>
                    <flux:table.column>{{ __('Kapasitas') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($classes as $class)
                        <flux:table.row :key="$class->id">
                            <flux:table.cell font-weight="medium">{{ $class->name }}</flux:table.cell>
                            <flux:table.cell>{{ $class->grade }}</flux:table.cell>
                            <flux:table.cell>{{ $class->academicYear->name }}</flux:table.cell>
                            <flux:table.cell>{{ $class->homeroomTeacher?->name ?? '-' }}</flux:table.cell>
                            <flux:table.cell>{{ $class->students_count }} / {{ $class->capacity }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2">
                                    <flux:button variant="ghost" size="sm" icon="pencil" :href="route('academic.classes.edit', $class->id)" wire:navigate />
                                    
                                    <flux:modal.trigger name="delete-class-{{ $class->id }}">
                                        <flux:button variant="ghost" size="sm" icon="trash" />
                                    </flux:modal.trigger>

                                    <flux:modal name="delete-class-{{ $class->id }}" class="min-w-[22rem]">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">{{ __('Hapus Kelas?') }}</flux:heading>
                                                <flux:subheading>{{ __('Tindakan ini tidak dapat dibatalkan.') }}</flux:subheading>
                                            </div>
                                            <div class="flex gap-2">
                                                <flux:spacer />
                                                <flux:modal.close>
                                                    <flux:button variant="ghost">{{ __('Batal') }}</flux:button>
                                                </flux:modal.close>
                                                <flux:button variant="danger" wire:click="delete({{ $class->id }})" wire:loading.attr="disabled">{{ __('Hapus') }}</flux:button>
                                            </div>
                                        </div>
                                    </flux:modal>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
             </flux:table>

             @if($classes->isEmpty())
                <flux:text class="text-center py-8">{{ __('Belum ada data kelas.') }}</flux:text>
             @endif
        </div>
    </flux:main>
</div>
