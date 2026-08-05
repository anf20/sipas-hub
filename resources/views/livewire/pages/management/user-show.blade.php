<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('management.users.index') }}" wire:navigate>{{ __('Manajemen Pengguna') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Detail Pengguna') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:spacer />
        <div class="hidden md:block font-bold text-sm text-slate-700 dark:text-zinc-300">
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
    </flux:header>

    <flux:main>
        <div class="space-y-6">
            <div class="flex justify-between items-start">
                <div class="flex items-center gap-4">
                    <div class="size-16 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-2xl font-bold text-zinc-400">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <flux:heading size="xl">{{ $user->name }}</flux:heading>
                            @foreach($user->roles as $role)
                                <flux:badge size="sm" color="gray">{{ $role->name }}</flux:badge>
                            @endforeach
                        </div>
                        <flux:text class="text-zinc-500">{{ $user->email }}</flux:text>
                    </div>
                </div>
                <div class="flex gap-2">
                    <flux:button :href="route('management.users.edit', $user->id)" icon="pencil" wire:navigate>{{ __('Edit Pengguna') }}</flux:button>
                    <flux:button :href="route('management.users.index')" variant="ghost" wire:navigate>{{ __('Kembali') }}</flux:button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- User Info Card -->
                <div class="lg:col-span-1 space-y-6">
                    <flux:card>
                        <flux:heading size="lg" class="mb-4">{{ __('Informasi Akun') }}</flux:heading>
                        <div class="space-y-4">
                            <div>
                                <flux:text size="sm" class="text-zinc-500">{{ __('Nama Lengkap') }}</flux:text>
                                <flux:heading size="sm">{{ $user->name }}</flux:heading>
                            </div>
                            <div>
                                <flux:text size="sm" class="text-zinc-500">{{ __('Email') }}</flux:text>
                                <flux:heading size="sm">{{ $user->email }}</flux:heading>
                            </div>
                            <div>
                                <flux:text size="sm" class="text-zinc-500">{{ __('Nomor Telepon/WA') }}</flux:text>
                                <flux:heading size="sm">{{ $user->phone ?? '-' }}</flux:heading>
                            </div>
                            <div>
                                <flux:text size="sm" class="text-zinc-500">{{ __('Bergabung Sejak') }}</flux:text>
                                <flux:heading size="sm">{{ $user->created_at->format('d M Y, H:i') }}</flux:heading>
                            </div>
                        </div>
                    </flux:card>
                </div>

                <!-- Linked Students (For Parents) -->
                <div class="lg:col-span-2 space-y-6">
                    @if($user->hasRole('Orang Tua'))
                        <flux:card class="p-0 overflow-hidden">
                            <div class="p-6 pb-0">
                                <div class="flex justify-between items-center mb-4">
                                    <div>
                                        <flux:heading size="lg">{{ __('Siswa yang Ditautkan') }}</flux:heading>
                                        <flux:subheading>{{ __('Daftar anak yang berada di bawah perwalian akun ini.') }}</flux:subheading>
                                    </div>
                                </div>
                            </div>

                            <flux:table>
                                <flux:table.columns>
                                    <flux:table.column>{{ __('NIS') }}</flux:table.column>
                                    <flux:table.column>{{ __('Nama Siswa') }}</flux:table.column>
                                    <flux:table.column>{{ __('Kelas') }}</flux:table.column>
                                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                                    <flux:table.column></flux:table.column>
                                </flux:table.columns>
                                <flux:table.rows>
                                    @forelse($user->students as $student)
                                        <flux:table.row :key="'student-'.$student->id">
                                            <flux:table.cell>{{ $student->nis }}</flux:table.cell>
                                            <flux:table.cell font-weight="medium">{{ $student->name }}</flux:table.cell>
                                            <flux:table.cell>
                                                @if($student->schoolClass)
                                                    {{ $student->schoolClass->name }} ({{ $student->schoolClass->grade }})
                                                @else
                                                    <span class="italic text-zinc-500">{{ __('Tingkat') }} {{ $student->current_grade }}</span>
                                                @endif
                                            </flux:table.cell>
                                            <flux:table.cell>
                                                <flux:badge :color="$student->status === 'aktif' ? 'green' : 'gray'" size="sm">
                                                    {{ ucfirst($student->status) }}
                                                </flux:badge>
                                            </flux:table.cell>
                                            <flux:table.cell>
                                                <div class="flex justify-end gap-2">
                                                    <flux:button variant="ghost" size="sm" icon="eye" :href="route('academic.students.show', $student->id)" wire:navigate />
                                                </div>
                                            </flux:table.cell>
                                        </flux:table.row>
                                    @empty
                                        <flux:table.row>
                                            <flux:table.cell colspan="5" class="text-center py-6 text-zinc-500">
                                                {{ __('Tidak ada siswa yang ditautkan ke akun ini.') }}
                                            </flux:table.cell>
                                        </flux:table.row>
                                    @endforelse
                                </flux:table.rows>
                            </flux:table>
                        </flux:card>
                    @endif
                </div>
            </div>
        </div>
    </flux:main>
</div>
