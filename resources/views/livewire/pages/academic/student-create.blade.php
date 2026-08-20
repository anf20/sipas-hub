<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('academic.dashboard') }}" wire:navigate>{{ __('Akademik') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('academic.students.index') }}" wire:navigate>{{ __('Data Siswa') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Tambah Siswa') }}</flux:breadcrumbs.item>
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
                    <div class="flex justify-between items-center mb-2">
                        <flux:heading size="lg">{{ __('Informasi Pribadi Siswa') }}</flux:heading>
                        <flux:button :href="route('academic.import', ['type' => 'students'])" variant="subtle" size="sm" icon="document-arrow-up" wire:navigate>
                            {{ __('Import dari Excel') }}
                        </flux:button>
                    </div>

                    <div class="space-y-2">
                        <flux:input wire:model="name" label="{{ __('Nama Lengkap') }}" placeholder="Contoh: Budi Santoso" required />
                        <flux:error name="name" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <flux:select wire:model="school_class_id" label="{{ __('Kelas') }}" placeholder="{{ __('Pilih Kelas') }}" required>
                                @foreach($classes as $class)
                                    <flux:select.option value="{{ $class->id }}">{{ $class->name }} ({{ $class->academicYear->name }})</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="school_class_id" />
                        </div>

                        <div class="space-y-2">
                            <flux:radio.group wire:model="gender" label="{{ __('Jenis Kelamin') }}" variant="segmented">
                                <flux:radio value="L" label="{{ __('Laki-laki') }}" />
                                <flux:radio value="P" label="{{ __('Perempuan') }}" />
                            </flux:radio.group>
                            <flux:error name="gender" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <flux:input type="number" wire:model="entry_year" label="{{ __('Tahun Masuk') }}" required />
                            <flux:error name="entry_year" />
                        </div>

                        <div class="space-y-2">
                            <flux:input type="date" wire:model="birth_date" label="{{ __('Tanggal Lahir') }}" />
                            <flux:error name="birth_date" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-end gap-2">
                            <div class="flex-1 space-y-2">
                                <flux:select wire:model="parent_user_id" label="{{ __('Akun Wali Murid') }}" placeholder="{{ __('Pilih Akun Wali') }}">
                                    <flux:select.option value="">{{ __('Belum Ada / Tanpa Akun') }}</flux:select.option>
                                    @foreach($parents as $parent)
                                        <flux:select.option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->email }})</flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="parent_user_id" />
                            </div>
                            <flux:modal.trigger name="create-parent-modal">
                                <flux:button icon="plus" class="mb-0.5">{{ __('Baru') }}</flux:button>
                            </flux:modal.trigger>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <flux:textarea wire:model="address" label="{{ __('Alamat') }}" placeholder="{{ __('Alamat lengkap rumah...') }}" />
                        <flux:error name="address" />
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Foto Siswa') }}</flux:label>
                        <flux:input type="file" wire:model="photo" accept="image/*" />
                        <flux:error name="photo" />
                        
                        @if ($photo)
                            <div class="mt-2">
                                <img src="{{ $photo->temporaryUrl() }}" class="w-32 h-32 object-cover rounded-lg border">
                            </div>
                        @endif
                    </flux:field>

                    <div class="flex items-center gap-4">
                        <flux:button type="submit" variant="primary">{{ __('Simpan Data Siswa') }}</flux:button>
                        <flux:button :href="route('academic.students.index')" variant="ghost" wire:navigate>{{ __('Batal') }}</flux:button>
                    </div>
                </div>
            </flux:card>
        </form>

        <!-- Modal Tambah Wali Murid Baru -->
        <flux:modal name="create-parent-modal" class="min-w-[24rem]">
            <form wire:submit.prevent="saveNewParent" class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Buat Akun Wali Murid') }}</flux:heading>
                    <flux:subheading>{{ __('Akun akan dibuat dengan password standar (password123).') }}</flux:subheading>
                </div>

                <div class="space-y-4">
                    <div class="space-y-2">
                        <flux:input wire:model="newParentName" label="{{ __('Nama Wali Murid') }}" required />
                        <flux:error name="newParentName" />
                    </div>
                    <div class="space-y-2">
                        <flux:input type="email" wire:model="newParentEmail" label="{{ __('Email Login') }}" required />
                        <flux:error name="newParentEmail" />
                    </div>
                    <div class="space-y-2">
                        <flux:input wire:model="newParentPhone" label="{{ __('Nomor Telepon/WA') }}" />
                        <flux:error name="newParentPhone" />
                    </div>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Batal') }}</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">{{ __('Simpan & Pilih') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    </flux:main>
</div>
