<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('academic.hub') }}" wire:navigate>{{ __('Akademik') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('academic.hub', ['tab' => 'classes']) }}" wire:navigate>{{ __('Manajemen Kelas') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Tambah Kelas') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:spacer />
        <div class="hidden md:block font-bold text-sm text-slate-700 dark:text-zinc-300">
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
    </flux:header>

    <flux:main>
        <form wire:submit="save" class="space-y-6 max-w-2xl">
            <flux:card>
                <div class="space-y-6">
                    <flux:heading size="lg">{{ __('Detail Kelas') }}</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input wire:model="name" label="{{ __('Nama Kelas') }}" placeholder="Contoh: X IPA 1" required />
                        <flux:input wire:model="grade" label="{{ __('Jenjang') }}" placeholder="Contoh: 10" required />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input wire:model="major" label="{{ __('Jurusan') }}" placeholder="Contoh: IPA" />
                        <flux:input type="number" wire:model="capacity" label="{{ __('Kapasitas') }}" required />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:select wire:model="homeroom_id" label="{{ __('Wali Kelas') }}" placeholder="{{ __('Pilih Wali Kelas') }}">
                            @foreach($teachers as $teacher)
                                <flux:select.option value="{{ $teacher->id }}">{{ $teacher->name }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="academic_year_id" label="{{ __('Tahun Ajaran') }}" placeholder="{{ __('Pilih Tahun Ajaran') }}" required>
                            @foreach($academicYears as $year)
                                <flux:select.option value="{{ $year->id }}">{{ $year->name }} {{ $year->is_active ? '(Aktif)' : '' }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="flex items-center gap-4">
                        <flux:button type="submit" variant="primary">{{ __('Simpan') }}</flux:button>
                        <flux:button :href="route('academic.classes.index')" variant="ghost" wire:navigate>{{ __('Batal') }}</flux:button>
                    </div>
                </div>
            </flux:card>
        </form>
    </flux:main>
</div>
