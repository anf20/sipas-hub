<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('academic.dashboard') }}" wire:navigate>{{ __('Akademik') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('academic.students.index') }}" wire:navigate>{{ __('Data Siswa') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Edit Siswa') }}</flux:breadcrumbs.item>
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
                    <flux:heading size="lg">{{ __('Edit Informasi Siswa') }}</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input wire:model="nis" label="{{ __('NIS') }}" placeholder="12345678" disabled />
                        <flux:input wire:model="name" label="{{ __('Nama Lengkap') }}" placeholder="Contoh: Budi Santoso" required />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:select wire:model="school_class_id" label="{{ __('Kelas') }}" placeholder="{{ __('Pilih Kelas') }}" required>
                            @foreach($classes as $class)
                                <flux:select.option value="{{ $class->id }}">{{ $class->name }} ({{ $class->grade }}) - {{ $class->academicYear->name }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:radio.group wire:model="gender" label="{{ __('Jenis Kelamin') }}" variant="segmented">
                            <flux:radio value="L" label="{{ __('Laki-laki') }}" />
                            <flux:radio value="P" label="{{ __('Perempuan') }}" />
                        </flux:radio.group>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input type="date" wire:model="birth_date" label="{{ __('Tanggal Lahir') }}" />
                        <flux:input type="number" wire:model="entry_year" label="{{ __('Tahun Masuk') }}" required />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         <flux:select wire:model="status" label="{{ __('Status') }}" required>
                            <flux:select.option value="aktif">{{ __('Aktif') }}</flux:select.option>
                            <flux:select.option value="lulus">{{ __('Lulus') }}</flux:select.option>
                            <flux:select.option value="keluar">{{ __('Keluar') }}</flux:select.option>
                            <flux:select.option value="pindah">{{ __('Pindah') }}</flux:select.option>
                        </flux:select>

                        <flux:select wire:model="parent_user_id" label="{{ __('Akun Wali Murid') }}" placeholder="{{ __('Pilih Akun Wali') }}">
                            <flux:select.option value="">{{ __('Belum Ada / Tanpa Akun') }}</flux:select.option>
                            @foreach($parents as $parent)
                                <flux:select.option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->email }})</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <flux:textarea wire:model="address" label="{{ __('Alamat') }}" placeholder="{{ __('Alamat lengkap rumah...') }}" />

                    <flux:field>
                        <flux:label>{{ __('Foto Siswa') }}</flux:label>
                        <flux:input type="file" wire:model="photo" accept="image/*" />
                        <flux:error name="photo" />
                        
                        @if ($photo)
                            <div class="mt-2">
                                <img src="{{ $photo->temporaryUrl() }}" class="w-32 h-32 object-cover rounded-lg border">
                            </div>
                        @elseif ($student->photo)
                            <div class="mt-2">
                                <img src="{{ Storage::url($student->photo) }}" class="w-32 h-32 object-cover rounded-lg border">
                            </div>
                        @endif
                    </flux:field>

                    <div class="flex items-center gap-4">
                        <flux:button type="submit" variant="primary">{{ __('Simpan Perubahan') }}</flux:button>
                        <flux:button :href="route('academic.students.index')" variant="ghost" wire:navigate>{{ __('Batal') }}</flux:button>
                    </div>
                </div>
            </flux:card>
        </form>
    </flux:main>
</div>
