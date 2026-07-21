<div class="space-y-8">
    <!-- Download Template Section -->
    <flux:card class="bg-zinc-50/50 dark:bg-zinc-900/50 border-zinc-200 dark:border-zinc-800 space-y-4">
        <div>
            <flux:heading size="lg">{{ __('1. Download Template Mutasi') }}</flux:heading>
            <flux:subheading>{{ __('Pilih kelas asal dan tahun ajaran tujuan untuk mengunduh template Excel yang sudah berisi data siswa saat ini.') }}</flux:subheading>
        </div>

        <form wire:submit.prevent="downloadTemplate" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <flux:select wire:model="downloadClassId" label="{{ __('Kelas Asal (Saat Ini)') }}" placeholder="Pilih Kelas...">
                    @foreach($classes as $class)
                        <flux:select.option :value="$class->id">{{ $class->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <div>
                <flux:select wire:model="downloadYearId" label="{{ __('Tahun Ajaran Tujuan') }}" placeholder="Pilih Tahun Ajaran...">
                    @foreach($years as $year)
                        <flux:select.option :value="$year->id">{{ $year->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <div class="pb-1">
                <flux:button type="submit" variant="primary" icon="arrow-down-tray" class="w-full">
                    {{ __('Download Template') }}
                </flux:button>
            </div>
        </form>
    </flux:card>

    <!-- Upload Template Section -->
    <flux:card class="space-y-4 border-zinc-200 dark:border-zinc-800">
        <div>
            <flux:heading size="lg">{{ __('2. Upload Data Kenaikan Kelas') }}</flux:heading>
            <flux:subheading>{{ __('Unggah file template Excel yang sudah Anda isi ID Kelas Tujuan-nya di sini.') }}</flux:subheading>
        </div>

        @if ($successMessage)
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ $successMessage }}</span>
            </div>
        @endif

        @if (!empty($errorLogs))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <strong>Gagal memproses file! Ditemukan kesalahan berikut:</strong>
                <ul class="list-disc mt-2 ml-5">
                    @foreach ($errorLogs as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form wire:submit.prevent="import" class="space-y-4">
            <div class="border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-lg p-6 text-center hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                <flux:icon icon="document-arrow-up" class="w-12 h-12 text-zinc-400 mx-auto mb-2" />
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2 cursor-pointer">
                    <span class="text-blue-600 hover:text-blue-500">{{ __('Pilih file Excel (.xlsx, .xls)') }}</span>
                    <input type="file" wire:model="file" accept=".xlsx, .xls" class="hidden">
                </label>
                <div wire:loading wire:target="file" class="text-blue-600 text-sm font-medium mt-2">{{ __('Sedang mengunggah file ke server...') }}</div>
                @if($file)
                    <div class="text-sm text-green-600 mt-2 font-medium">File terpilih: {{ $file->getClientOriginalName() }}</div>
                @endif
                @error('file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <flux:button type="submit" variant="primary" icon="arrow-up-tray" wire:loading.attr="disabled" wire:target="import" class="w-full">
                <span wire:loading.remove wire:target="import">{{ __('Proses Mutasi Kelas') }}</span>
                <span wire:loading wire:target="import">{{ __('Memvalidasi dan Memproses Data...') }}</span>
            </flux:button>
        </form>
    </flux:card>
</div>
