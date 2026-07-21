<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('academic.hub') }}" wire:navigate>{{ __('Akademik') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('academic.hub', ['tab' => 'students']) }}" wire:navigate>{{ __('Data Siswa') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Detail Siswa') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:spacer />
        <div class="hidden md:block font-bold text-sm text-slate-700 dark:text-zinc-300">
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
    </flux:header>

    <flux:main>
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <flux:heading size="xl">{{ __('Profil Siswa') }}</flux:heading>
                <div class="flex gap-2">
                    <flux:button :href="route('academic.students.edit', $student->id)" icon="pencil" wire:navigate>{{ __('Edit') }}</flux:button>
                    <flux:button :href="route('academic.hub', ['tab' => 'students'])" variant="ghost" wire:navigate>{{ __('Kembali') }}</flux:button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Student Info Card -->
                <flux:card class="space-y-4 lg:col-span-1">
                    <div class="flex flex-col items-center text-center space-y-2 pb-4 border-b border-zinc-200 dark:border-zinc-700">
                        <div class="size-24 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-3xl font-bold text-zinc-400">
                            {{ substr($student->name, 0, 1) }}
                        </div>
                        <flux:heading size="lg">{{ $student->name }}</flux:heading>
                        <flux:badge :color="$student->status === 'aktif' ? 'green' : 'gray'">{{ ucfirst($student->status) }}</flux:badge>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <flux:text class="text-zinc-500" size="sm">{{ __('NIS') }}</flux:text>
                            <flux:text font-weight="medium">{{ $student->nis }}</flux:text>
                        </div>
                        <div class="flex justify-between">
                            <flux:text class="text-zinc-500" size="sm">{{ __('NISN') }}</flux:text>
                            <flux:text font-weight="medium">{{ $student->nisn ?? '-' }}</flux:text>
                        </div>
                        <div class="flex justify-between">
                            <flux:text class="text-zinc-500" size="sm">{{ __('Jenis Kelamin') }}</flux:text>
                            <flux:text font-weight="medium">{{ $student->gender === 'L' ? __('Laki-laki') : __('Perempuan') }}</flux:text>
                        </div>
                        <div class="flex justify-between">
                            <flux:text class="text-zinc-500" size="sm">{{ __('Jenjang') }}</flux:text>
                            <flux:text font-weight="medium">{{ $student->current_grade }}</flux:text>
                        </div>
                        <div class="flex justify-between">
                            <flux:text class="text-zinc-500" size="sm">{{ __('Kelas') }}</flux:text>
                            <flux:text font-weight="medium">{{ $student->schoolClass?->name ?? '-' }}</flux:text>
                        </div>
                        <div class="flex justify-between">
                            <flux:text class="text-zinc-500" size="sm">{{ __('Tahun Ajaran') }}</flux:text>
                            <flux:text font-weight="medium">{{ $student->schoolClass?->academicYear?->name ?? '-' }}</flux:text>
                        </div>
                    </div>
                </flux:card>

                <div class="lg:col-span-2 space-y-6">
                    <!-- Parent Info -->
                    <flux:card class="space-y-4">
                        <flux:heading size="lg">{{ __('Informasi Orang Tua / Wali') }}</flux:heading>
                        @if($parent)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <flux:text class="text-zinc-500" size="sm">{{ __('Nama Wali') }}</flux:text>
                                    <flux:text font-weight="medium" size="lg">{{ $parent->name }}</flux:text>
                                </div>
                                <div class="space-y-1">
                                    <flux:text class="text-zinc-500" size="sm">{{ __('Email Akun') }}</flux:text>
                                    <flux:text font-weight="medium">{{ $parent->email }}</flux:text>
                                </div>
                            </div>
                        @else
                            <div class="py-4 text-center">
                                <flux:text color="gray">{{ __('Siswa ini belum dihubungkan dengan akun orang tua.') }}</flux:text>
                            </div>
                        @endif
                    </flux:card>

                    <!-- Invoices -->
                    <flux:card class="space-y-4">
                        <div class="flex justify-between items-center">
                            <flux:heading size="lg">{{ __('Riwayat Tagihan') }}</flux:heading>
                        </div>
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('Tagihan') }}</flux:table.column>
                                <flux:table.column>{{ __('Periode') }}</flux:table.column>
                                <flux:table.column>{{ __('Nominal') }}</flux:table.column>
                                <flux:table.column>{{ __('Status') }}</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @forelse($invoices as $invoice)
                                    <flux:table.row :key="'inv-'.$invoice->id">
                                        <flux:table.cell font-weight="medium">{{ $invoice->feeType->name }}</flux:table.cell>
                                        <flux:table.cell>{{ $invoice->period_month }}/{{ $invoice->period_year }}</flux:table.cell>
                                        <flux:table.cell>Rp {{ number_format($invoice->amount, 0, ',', '.') }}</flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge :color="$invoice->status === 'paid' ? 'green' : 'red'">
                                                {{ ucfirst($invoice->status) }}
                                            </flux:badge>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @empty
                                    <flux:table.row>
                                        <flux:table.cell colspan="4" class="text-center py-4 text-zinc-500">
                                            {{ __('Belum ada tagihan untuk siswa ini.') }}
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
