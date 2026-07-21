<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:heading size="xl">{{ __('Data Siswa') }}</flux:heading>
        <flux:spacer />
        <div class="hidden md:block font-bold text-sm text-slate-700 dark:text-zinc-300">
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
    </flux:header>

    <flux:main>
        <div class="space-y-4">
             <flux:button :href="route('academic.students.create')" variant="primary" icon="plus" wire:navigate>{{ __('Tambah Siswa') }}</flux:button>
             
             <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('NIS') }}</flux:table.column>
                    <flux:table.column>{{ __('Nama') }}</flux:table.column>
                    <flux:table.column>{{ __('Tingkat / Kelas') }}</flux:table.column>
                    <flux:table.column>{{ __('Gender') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($students as $student)
                        <flux:table.row :key="$student->id">
                            <flux:table.cell>{{ $student->nis }}</flux:table.cell>
                            <flux:table.cell font-weight="medium">{{ $student->name }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="font-medium">{{ __('Tingkat') }} {{ $student->current_grade }}</div>
                                <div class="text-xs text-zinc-500">
                                    {{ $student->school_class_id ? $student->schoolClass->name : __('Belum Ada Rombel') }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>{{ $student->gender }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$student->status === 'aktif' ? 'green' : 'gray'">
                                    {{ ucfirst($student->status) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2">
                                    <flux:button variant="ghost" size="sm" icon="pencil" :href="route('academic.students.edit', $student->id)" wire:navigate />
                                    
                                    <flux:modal.trigger name="delete-student-{{ $student->id }}">
                                        <flux:button variant="ghost" size="sm" icon="trash" />
                                    </flux:modal.trigger>

                                    <flux:modal name="delete-student-{{ $student->id }}" class="min-w-[22rem]">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">{{ __('Hapus Data Siswa?') }}</flux:heading>
                                                <flux:subheading>{{ __('Siswa akan dinonaktifkan (Soft Delete).') }}</flux:subheading>
                                            </div>
                                            <div class="flex gap-2">
                                                <flux:spacer />
                                                <flux:modal.close>
                                                    <flux:button variant="ghost">{{ __('Batal') }}</flux:button>
                                                </flux:modal.close>
                                                <flux:button variant="danger" wire:click="delete({{ $student->id }})" wire:loading.attr="disabled">{{ __('Hapus') }}</flux:button>
                                            </div>
                                        </div>
                                    </flux:modal>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
             </flux:table>

             @if($students->isEmpty())
                <flux:text class="text-center py-8">{{ __('Belum ada data siswa.') }}</flux:text>
             @endif
        </div>
    </flux:main>
</div>
