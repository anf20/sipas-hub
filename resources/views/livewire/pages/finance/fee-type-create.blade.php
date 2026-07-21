<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('finance.hub') }}" wire:navigate>{{ __('Keuangan') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('finance.hub', ['tab' => 'fees']) }}" wire:navigate>{{ __('Tagihan Lainnya') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Tambah Baru') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:spacer />
        <div class="hidden md:block font-bold text-sm text-slate-700 dark:text-zinc-300">
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
    </flux:header>

    <flux:main>
        <div class="flex flex-col gap-6">
            <!-- Section 1: Konfigurasi Tagihan (Form di Atas) -->
            <div>
                <form wire:submit.prevent="save" class="space-y-6">
                    <flux:card>
                        <div class="space-y-6">
                            <flux:heading size="lg">{{ __('Tambah Tagihan Non-SPP') }}</flux:heading>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                <!-- Kolom 1 & 2: Detail Konfigurasi -->
                                <div class="lg:col-span-2 space-y-6">
                                    <div class="space-y-2">
                                        <flux:input wire:model="name" label="{{ __('Nama Tagihan') }}" placeholder="Contoh: Sumbangan Gedung 2026 atau Ekskul Basket Mei" />
                                        <flux:error name="name" />
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <flux:select wire:model="category" label="{{ __('Kategori') }}">
                                                <flux:select.option value="kegiatan">{{ __('Kegiatan') }}</flux:select.option>
                                                <flux:select.option value="seragam">{{ __('Seragam') }}</flux:select.option>
                                                <flux:select.option value="lain">{{ __('Lain-lain') }}</flux:select.option>
                                            </flux:select>
                                            <flux:error name="category" />
                                        </div>

                                        <div class="space-y-2">
                                            <flux:input type="number" wire:model="default_amount" label="{{ __('Nominal') }}" prefix="Rp" />
                                            <flux:error name="default_amount" />
                                        </div>
                                    </div>

                                    <div class="p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-xl border border-zinc-200 dark:border-zinc-700 space-y-4">
                                        <div class="flex items-center gap-4">
                                            <flux:label>{{ __('Tipe Tagihan:') }}</flux:label>
                                            <flux:radio.group wire:model.live="is_recurring" variant="segmented" size="sm">
                                                <flux:radio value="sekali" label="{{ __('Sekali Saja (Tahunan/Insidental)') }}" />
                                                <flux:radio value="rutin" label="{{ __('Rutin (Bulanan)') }}" />
                                            </flux:radio.group>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            @if($is_recurring === 'rutin')
                                                <div class="space-y-2">
                                                    <flux:select wire:model="month" label="{{ __('Bulan') }}">
                                                        @foreach($months as $num => $name)
                                                            <flux:select.option value="{{ $num }}">{{ $name }}</flux:select.option>
                                                        @endforeach
                                                    </flux:select>
                                                    <flux:error name="month" />
                                                </div>
                                            @endif

                                            <div class="space-y-2 {{ $is_recurring === 'sekali' ? 'md:col-span-1' : '' }}">
                                                <flux:input type="number" wire:model="year" label="{{ __('Tahun') }}" />
                                                <flux:error name="year" />
                                            </div>

                                            <div class="space-y-2 {{ $is_recurring === 'sekali' ? 'md:col-span-2' : '' }}">
                                                <flux:input type="date" wire:model="due_date" label="{{ __('Tanggal Jatuh Tempo') }}" />
                                                <flux:error name="due_date" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kolom 3: Target & Aksi (Sidebar Style) -->
                                <div class="space-y-4">
                                    <div class="p-4 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl space-y-4 shadow-sm">
                                        <flux:label>{{ __('Target Tagihan') }}</flux:label>
                                        <flux:radio.group wire:model.live="target_type" variant="segmented" size="sm" class="flex-wrap">
                                            <flux:radio value="all" label="{{ __('Semua') }}" />
                                            <flux:radio value="grade" label="{{ __('Tingkat') }}" />
                                            <flux:radio value="class" label="{{ __('Kelas') }}" />
                                            <flux:radio value="student" label="{{ __('Siswa') }}" />
                                        </flux:radio.group>
                                        <flux:error name="target_type" />

                                        <div class="pt-2">
                                            @if($target_type === 'grade')
                                                <flux:select wire:model.live="target_grade" label="{{ __('Pilih Tingkat') }}" placeholder="{{ __('Pilih Grade') }}">
                                                    @foreach($grades as $grade)
                                                        <flux:select.option value="{{ $grade }}">{{ __('Grade') }} {{ $grade }}</flux:select.option>
                                                    @endforeach
                                                </flux:select>
                                                <flux:error name="target_grade" />
                                            @endif

                                            @if($target_type === 'class')
                                                <flux:select wire:model.live="target_class" label="{{ __('Pilih Kelas') }}" placeholder="{{ __('Pilih Kelas') }}">
                                                    @foreach($classes as $class)
                                                        <flux:select.option value="{{ $class->id }}">{{ $class->name }} ({{ $class->academicYear->name }})</flux:select.option>
                                                    @endforeach
                                                </flux:select>
                                                <flux:error name="target_class" />
                                            @endif

                                            @if($target_type === 'student')
                                                <flux:select wire:model.live="target_students" multiple label="{{ __('Pilih Siswa') }}" placeholder="{{ __('Cari Nama Siswa...') }}" searchable>
                                                    @foreach($all_students as $student)
                                                        <flux:select.option value="{{ $student->id }}">{{ $student->name }} ({{ $student->nis }} • {{ $student->schoolClass->name }})</flux:select.option>
                                                    @endforeach
                                                </flux:select>
                                                <flux:error name="target_students" />
                                            @endif
                                        </div>
                                    </div>

                                    <flux:button type="submit" variant="primary" class="w-full py-6 text-lg font-bold" wire:loading.attr="disabled">
                                        {{ __('Simpan & Buat Tagihan') }}
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    </flux:card>
                </form>
            </div>

            <!-- Section 2: Preview Siswa (Tabel di Bawah) -->
            <div>
                <flux:card>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <flux:heading size="lg">{{ __('Preview Siswa Target') }}</flux:heading>
                                <flux:subheading>{{ __('Daftar siswa yang akan menerima tagihan ini.') }}</flux:subheading>
                            </div>
                            @if(!empty($preview_students))
                                <flux:badge color="blue" size="md" variant="pill">{{ count($preview_students) }} {{ __('Siswa Terpilih') }}</flux:badge>
                            @endif
                        </div>

                        @if(!empty($preview_students))
                            <flux:table>
                                <flux:table.columns>
                                    <flux:table.column>{{ __('NIS') }}</flux:table.column>
                                    <flux:table.column>{{ __('Nama Lengkap') }}</flux:table.column>
                                    <flux:table.column>{{ __('Kelas') }}</flux:table.column>
                                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                                </flux:table.columns>

                                <flux:table.rows>
                                    @foreach($preview_students as $student)
                                        <flux:table.row :key="$student->id">
                                            <flux:table.cell>{{ $student->nis }}</flux:table.cell>
                                            <flux:table.cell font-weight="medium">{{ $student->name }}</flux:table.cell>
                                            <flux:table.cell>{{ $student->schoolClass->name }}</flux:table.cell>
                                            <flux:table.cell>
                                                <flux:badge size="sm" color="green" variant="pill" class="animate-pulse">{{ __('Akan Dibuat') }}</flux:badge>
                                            </flux:table.cell>
                                        </flux:table.row>
                                    @endforeach
                                </flux:table.rows>
                            </flux:table>
                        @else
                            <div class="py-20 text-center border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-2xl bg-zinc-50/50 dark:bg-zinc-900/50">
                                <flux:icon.users class="mx-auto mb-4 text-zinc-300 dark:text-zinc-600" size="xl" />
                                <flux:text class="text-zinc-500">{{ __('Belum ada target terpilih. Silakan sesuaikan target untuk melihat preview.') }}</flux:text>
                            </div>
                        @endif
                    </div>
                </flux:card>
            </div>
        </div>
    </flux:main>
</div>
