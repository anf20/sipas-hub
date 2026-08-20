<div>
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item href="{{ route('dashboard') }}">{{ __('Dashboard') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('academic.dashboard') }}">{{ __('Akademik') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ __('Import Data') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl">{{ __('Import Data Akademik') }}</flux:heading>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sidebar Setup -->
        <div class="col-span-1 space-y-6">
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Pengaturan Import') }}</flux:heading>

                <form wire:submit="processPreview" class="space-y-6">
                    <flux:radio.group wire:model.live="importType" label="{{ __('Tipe Data') }}">
                        <flux:radio value="students" label="{{ __('Data Siswa') }}" />
                        <flux:radio value="classes" label="{{ __('Data Kelas') }}" />
                    </flux:radio.group>

                    @if($importType === 'students')
                        <flux:select wire:model.live="school_class_id" label="{{ __('Pilih Kelas Tujuan') }}" placeholder="Pilih Kelas...">
                            @foreach($classes as $class)
                                <flux:select.option value="{{ $class->id }}">{{ $class->name }} ({{ $class->academicYear->name }})</flux:select.option>
                            @endforeach
                        </flux:select>
                    @endif

                    @if($importType === 'classes')
                        <flux:select wire:model.live="academic_year_id" label="{{ __('Pilih Tahun Ajaran') }}" placeholder="Pilih Tahun Ajaran...">
                            @foreach($academicYears as $year)
                                <flux:select.option value="{{ $year->id }}">{{ $year->name }} ({{ $year->is_active ? 'Aktif' : 'Tidak Aktif' }})</flux:select.option>
                            @endforeach
                        </flux:select>
                    @endif

                    <div class="space-y-2">
                        <flux:heading size="sm">{{ __('File Excel/CSV') }}</flux:heading>
                        <input type="file" wire:model="file" class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-semibold
                            file:bg-indigo-50 file:text-indigo-700
                            hover:file:bg-indigo-100
                            dark:file:bg-indigo-900/50 dark:file:text-indigo-300
                        " accept=".xlsx,.xls,.csv" />
                        @error('file') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 flex justify-between items-center border-t border-zinc-200 dark:border-zinc-700">
                        <flux:button wire:click="processPreview" wire:loading.attr="disabled" variant="primary" icon="eye">
                            {{ __('Preview Data') }}
                        </flux:button>
                        
                        <a href="{{ route('academic.import.template', ['type' => $importType]) }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-500 flex items-center">
                            <flux:icon.arrow-down-tray class="w-4 h-4 mr-1"/> Template
                        </a>
                    </div>
                </form>
            </flux:card>

            <!-- Instructions -->
            <flux:card>
                <flux:heading size="md" class="mb-2 text-indigo-600 flex items-center gap-2">
                    <flux:icon.information-circle class="w-5 h-5"/>
                    {{ __('Petunjuk Penting') }}
                </flux:heading>
                <div class="text-sm text-zinc-600 dark:text-zinc-400 space-y-2">
                    @if($importType === 'students')
                        <ul class="list-disc pl-4 space-y-1">
                            <li>Pilih <strong>Kelas Tujuan</strong> terlebih dahulu sebelum mengunggah file. Seluruh data pada file ini akan dimasukkan ke kelas tersebut.</li>
                            <li>Kolom <strong>NIS</strong> dapat dikosongkan. Sistem akan membuatkan otomatis jika kosong.</li>
                            <li>Jika Anda mengisi <strong>Email Wali</strong> dan <strong>Nama Wali</strong>, sistem akan membuatkan akun Wali Murid baru secara otomatis dan menautkannya dengan siswa ini.</li>
                            <li>Setelah klik <strong>Preview Data</strong>, Anda dapat mengecek dan mengedit langsung data yang dirasa salah di tabel sebelah kanan sebelum di-save.</li>
                        </ul>
                    @else
                        <ul class="list-disc pl-4 space-y-1">
                            <li>Pilih <strong>Tahun Ajaran</strong> tujuan. Seluruh kelas di file akan dibuat untuk tahun ajaran tersebut.</li>
                            <li>Kolom <strong>Email Wali Kelas</strong> dapat dikosongkan. Jika diisi, pastikan email sudah terdaftar sebagai Guru.</li>
                            <li>Setelah Preview, Anda dapat mengedit langsung data di tabel sebelah kanan sebelum di-simpan.</li>
                        </ul>
                    @endif
                </div>
            </flux:card>
        </div>

        <!-- Preview Area -->
        <div class="col-span-1 md:col-span-2 space-y-6">
            <flux:card>
                <div class="flex justify-between items-center mb-4">
                    <flux:heading size="lg">{{ __('Preview Data (Bisa Diedit)') }}</flux:heading>
                    @if($isPreviewing && count($previewData) > 0)
                        <flux:button wire:click="importData" variant="primary" icon="check-circle" wire:loading.attr="disabled">
                            {{ __('Simpan Data (' . count($previewData) . ')') }}
                        </flux:button>
                    @endif
                </div>

                @if($isPreviewing)
                    @if(count($previewData) > 0)
                        <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-800 dark:text-zinc-400">
                                    @if($importType === 'students')
                                        <tr>
                                            <th class="px-4 py-3">No</th>
                                            <th class="px-4 py-3">NIS</th>
                                            <th class="px-4 py-3">Nama Siswa</th>
                                            <th class="px-4 py-3">L/P</th>
                                            <th class="px-4 py-3">Nama Wali</th>
                                            <th class="px-4 py-3">Email Wali</th>
                                        </tr>
                                    @else
                                        <tr>
                                            <th class="px-4 py-3">No</th>
                                            <th class="px-4 py-3">Nama Kelas</th>
                                            <th class="px-4 py-3">Tingkat</th>
                                            <th class="px-4 py-3">Email Wali Kelas</th>
                                        </tr>
                                    @endif
                                </thead>
                                <tbody>
                                    @foreach($previewData as $index => $row)
                                        <tr class="bg-white border-b dark:bg-zinc-900 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                            <td class="px-4 py-2">{{ $index + 1 }}</td>
                                            
                                            @if($importType === 'students')
                                                <td class="px-4 py-2">
                                                    <flux:input wire:model="previewData.{{ $index }}.nis" placeholder="Otomatis" size="sm" />
                                                </td>
                                                <td class="px-4 py-2">
                                                    <flux:input wire:model="previewData.{{ $index }}.name" size="sm" />
                                                </td>
                                                <td class="px-4 py-2">
                                                    <flux:select wire:model="previewData.{{ $index }}.gender" size="sm">
                                                        <flux:select.option value="L">L</flux:select.option>
                                                        <flux:select.option value="P">P</flux:select.option>
                                                    </flux:select>
                                                </td>
                                                <td class="px-4 py-2">
                                                    <flux:input wire:model="previewData.{{ $index }}.parent_name" size="sm" />
                                                </td>
                                                <td class="px-4 py-2">
                                                    <flux:input wire:model="previewData.{{ $index }}.parent_email" type="email" size="sm" />
                                                </td>
                                            @else
                                                <td class="px-4 py-2">
                                                    <flux:input wire:model="previewData.{{ $index }}.name" size="sm" />
                                                </td>
                                                <td class="px-4 py-2">
                                                    <flux:input wire:model="previewData.{{ $index }}.level" size="sm" />
                                                </td>
                                                <td class="px-4 py-2">
                                                    <flux:input wire:model="previewData.{{ $index }}.homeroom_email" type="email" size="sm" />
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-zinc-500">
                            <flux:icon.document-text class="w-12 h-12 mx-auto mb-3 opacity-50" />
                            <p>Data kosong. Pastikan file Excel tidak kosong dan sesuai format template.</p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-12 text-zinc-500 border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-lg">
                        <flux:icon.table-cells class="w-12 h-12 mx-auto mb-3 opacity-30" />
                        <p>Silakan upload file dan klik "Preview Data" untuk melihat dan mengedit isinya di sini sebelum disimpan.</p>
                    </div>
                @endif
            </flux:card>
        </div>
    </div>
</div>
