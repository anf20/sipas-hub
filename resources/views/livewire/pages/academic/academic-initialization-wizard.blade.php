<x-slot:title>Wizard Inisialisasi Tahun Ajaran</x-slot:title>

<div class="max-w-6xl mx-auto py-8 px-4 sm:px-6">
    {{-- ============================================================ --}}
    {{-- STEP INDICATOR --}}
    {{-- ============================================================ --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            @foreach ([
                1 => ['icon' => 'calendar-days', 'label' => 'Tahun Ajaran & Kelas'],
                2 => ['icon' => 'user-group', 'label' => 'Akun Pengelola'],
                3 => ['icon' => 'users', 'label' => 'Santri & Wali'],
                4 => ['icon' => 'banknotes', 'label' => 'Tagihan Non-SPP'],
                5 => ['icon' => 'check-circle', 'label' => 'Ringkasan'],
            ] as $step => $info)
                <div class="flex flex-col items-center flex-1 {{ $step < 5 ? 'relative' : '' }}">
                    <button
                        wire:click="goToStep({{ $step }})"
                        @class([
                            'w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-200',
                            'bg-emerald-600 text-white shadow-md' => $currentStep === $step,
                            'bg-emerald-100 text-emerald-700' => $currentStep > $step,
                            'bg-zinc-100 text-zinc-400' => $currentStep < $step,
                            'cursor-pointer hover:scale-110' => $step <= $currentStep,
                            'cursor-not-allowed' => $step > $currentStep,
                        ])
                        {{ $step > $currentStep ? 'disabled' : '' }}
                    >
                        @if($currentStep > $step)
                            <flux:icon.check class="size-5" />
                        @else
                            {{ $step }}
                        @endif
                    </button>
                    <span @class([
                        'mt-2 text-xs font-medium text-center hidden sm:block',
                        'text-emerald-700' => $currentStep >= $step,
                        'text-zinc-400' => $currentStep < $step,
                    ])>{{ $info['label'] }}</span>

                    {{-- Connector line --}}
                    @if($step < 5)
                        <div @class([
                            'absolute top-5 left-[calc(50%+24px)] w-[calc(100%-48px)] h-0.5',
                            'bg-emerald-400' => $currentStep > $step,
                            'bg-zinc-200' => $currentStep <= $step,
                        ])></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- STEP 1: TAHUN AJARAN & KELAS --}}
    {{-- ============================================================ --}}
    @if($currentStep === 1)
    <div class="space-y-6">
        <flux:heading size="lg">📅 Step 1: Tahun Ajaran & Master Kelas</flux:heading>
        <flux:text>Masukkan data tahun ajaran baru dan daftar kelas yang akan dibuka.</flux:text>

        {{-- Academic Year Form --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-white rounded-xl border border-zinc-200">
            <flux:field>
                <flux:label>Nama Tahun Ajaran</flux:label>
                <flux:input wire:model="academicYearName" placeholder="2026/2027" />
            </flux:field>
            <flux:field>
                <flux:label>Tanggal Mulai</flux:label>
                <flux:input type="date" wire:model="startDate" />
            </flux:field>
            <flux:field>
                <flux:label>Tanggal Selesai</flux:label>
                <flux:input type="date" wire:model="endDate" />
            </flux:field>
        </div>

        <flux:separator />

        {{-- Import / Manual Toggle --}}
        @include('livewire.pages.academic.partials.wizard-import-section', ['stepLabel' => 'Kelas'])

        {{-- Manual Entry --}}
        <div class="p-4 bg-white rounded-xl border border-zinc-200 space-y-4">
            <flux:heading size="sm">Tambah Kelas Manual</flux:heading>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                <flux:field>
                    <flux:label>Nama Kelas</flux:label>
                    <flux:input wire:model="newClassName" placeholder="7A" />
                </flux:field>
                <flux:field>
                    <flux:label>Tingkat</flux:label>
                    <flux:input wire:model="newClassGrade" placeholder="7" />
                </flux:field>
                <flux:field>
                    <flux:label>Kapasitas</flux:label>
                    <flux:input type="number" wire:model="newClassCapacity" />
                </flux:field>
                <flux:button variant="primary" wire:click="addClassManually" icon="plus">Tambah</flux:button>
            </div>
        </div>

        {{-- Invalid Data Table --}}
        @if(count($classesInvalid) > 0)
            @include('livewire.pages.academic.partials.wizard-invalid-table', [
                'invalidData' => $classesInvalid,
                'columns' => ['name' => 'Nama Kelas', 'grade' => 'Tingkat', 'capacity' => 'Kapasitas'],
                'type' => 'classes',
            ])
        @endif

        {{-- Valid Data Preview --}}
        @if(count($classesValid) > 0)
            @include('livewire.pages.academic.partials.wizard-valid-table', [
                'validData' => $classesValid,
                'columns' => ['name' => 'Nama Kelas', 'grade' => 'Tingkat', 'capacity' => 'Kapasitas'],
                'removeMethod' => 'removeClassValid',
            ])
        @endif
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- STEP 2: AKUN PENGELOLA --}}
    {{-- ============================================================ --}}
    @if($currentStep === 2)
    <div class="space-y-6">
        <flux:heading size="lg">👥 Step 2: Akun Pengelola (Admin & Asatidz)</flux:heading>
        <flux:text>Tambahkan akun untuk Admin dan Asatidz. Password default: <code class="bg-zinc-100 px-2 py-0.5 rounded text-sm">password123</code></flux:text>

        @include('livewire.pages.academic.partials.wizard-import-section', ['stepLabel' => 'Pengelola'])

        {{-- Manual Entry --}}
        <div class="p-4 bg-white rounded-xl border border-zinc-200 space-y-4">
            <flux:heading size="sm">Tambah Pengelola Manual</flux:heading>
            <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 items-end">
                <flux:field>
                    <flux:label>Nama Lengkap</flux:label>
                    <flux:input wire:model="newStaffName" placeholder="Ustadz Ahmad" />
                </flux:field>
                <flux:field>
                    <flux:label>Email</flux:label>
                    <flux:input type="email" wire:model="newStaffEmail" placeholder="ahmad@email.com" />
                </flux:field>
                <flux:field>
                    <flux:label>No. WhatsApp</flux:label>
                    <flux:input wire:model="newStaffPhone" placeholder="08123456789" />
                </flux:field>
                <flux:field>
                    <flux:label>Role</flux:label>
                    <flux:select wire:model="newStaffRole">
                        <flux:select.option value="Asatidz">Asatidz</flux:select.option>
                        <flux:select.option value="Admin Akademik">Admin Akademik</flux:select.option>
                        <flux:select.option value="Admin Keuangan">Admin Keuangan</flux:select.option>
                        <flux:select.option value="Super Admin">Super Admin</flux:select.option>
                    </flux:select>
                </flux:field>
                <flux:button variant="primary" wire:click="addStaffManually" icon="plus">Tambah</flux:button>
            </div>
        </div>

        @if(count($staffInvalid) > 0)
            @include('livewire.pages.academic.partials.wizard-invalid-table', [
                'invalidData' => $staffInvalid,
                'columns' => ['name' => 'Nama', 'email' => 'Email', 'phone' => 'No. WA', 'role' => 'Role'],
                'type' => 'staff',
            ])
        @endif

        @if(count($staffValid) > 0)
            @include('livewire.pages.academic.partials.wizard-valid-table', [
                'validData' => $staffValid,
                'columns' => ['name' => 'Nama', 'email' => 'Email', 'phone' => 'No. WA', 'role' => 'Role'],
                'removeMethod' => 'removeStaffValid',
            ])
        @endif
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- STEP 3: SANTRI, WALI & KEUANGAN --}}
    {{-- ============================================================ --}}
    @if($currentStep === 3)
    <div class="space-y-6">
        <flux:heading size="lg">🎓 Step 3: Data Santri, Wali & Keuangan</flux:heading>
        <flux:text>Masukkan data lengkap santri beserta wali dan nominal SPP/tunggakan masing-masing.</flux:text>

        @include('livewire.pages.academic.partials.wizard-import-section', ['stepLabel' => 'Santri'])

        {{-- Manual Entry --}}
        <div class="p-4 bg-white rounded-xl border border-zinc-200 space-y-4">
            <flux:heading size="sm">Tambah Santri Manual</flux:heading>

            {{-- Row 1: Student Data --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <flux:field>
                    <flux:label>Nama Santri</flux:label>
                    <flux:input wire:model="newStudentName" placeholder="Ahmad Fauzi" />
                </flux:field>
                <flux:field>
                    <flux:label>Gender</flux:label>
                    <flux:select wire:model="newStudentGender">
                        <flux:select.option value="L">Laki-laki</flux:select.option>
                        <flux:select.option value="P">Perempuan</flux:select.option>
                    </flux:select>
                </flux:field>
                <flux:field>
                    <flux:label>Kelas</flux:label>
                    <flux:select wire:model="newStudentClassName">
                        <flux:select.option value="">Pilih Kelas</flux:select.option>
                        @foreach($availableClasses as $cls)
                            <flux:select.option value="{{ $cls }}">{{ $cls }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            {{-- Row 2: Parent Data --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <flux:field>
                    <flux:label>Nama Wali</flux:label>
                    <flux:input wire:model="newParentName" placeholder="Bapak Hasan" />
                </flux:field>
                <flux:field>
                    <flux:label>No. WA Wali</flux:label>
                    <flux:input wire:model="newParentPhone" placeholder="08123456789" />
                </flux:field>
                <flux:field>
                    <flux:label>Email Wali (opsional)</flux:label>
                    <flux:input type="email" wire:model="newParentEmail" placeholder="hasan@email.com" />
                </flux:field>
            </div>

            {{-- Row 3: Billing Data --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <flux:field>
                    <flux:label>Nominal SPP Bulanan (Rp)</flux:label>
                    <flux:input type="number" wire:model="newSppAmount" placeholder="500000" />
                </flux:field>
                <flux:field>
                    <flux:label>Tunggakan Awal (Rp)</flux:label>
                    <flux:input type="number" wire:model="newInitialArrears" placeholder="0" />
                </flux:field>
                <flux:button variant="primary" wire:click="addStudentManually" icon="plus">Tambah Santri</flux:button>
            </div>
        </div>

        @if(count($studentsInvalid) > 0)
            @include('livewire.pages.academic.partials.wizard-invalid-table', [
                'invalidData' => $studentsInvalid,
                'columns' => ['name' => 'Nama', 'gender' => 'Gender', 'class_name' => 'Kelas', 'parent_name' => 'Wali', 'parent_phone' => 'WA Wali', 'spp_amount' => 'SPP', 'initial_arrears' => 'Tunggakan'],
                'type' => 'students',
            ])
        @endif

        @if(count($studentsValid) > 0)
            @include('livewire.pages.academic.partials.wizard-valid-table', [
                'validData' => $studentsValid,
                'columns' => ['name' => 'Nama', 'gender' => 'Gender', 'class_name' => 'Kelas', 'parent_name' => 'Wali', 'parent_phone' => 'WA Wali', 'spp_amount' => 'SPP', 'initial_arrears' => 'Tunggakan'],
                'removeMethod' => 'removeStudentValid',
            ])
        @endif
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- STEP 4: TAGIHAN NON-SPP --}}
    {{-- ============================================================ --}}
    @if($currentStep === 4)
    <div class="space-y-6">
        <flux:heading size="lg">💰 Step 4: Biaya Daftar Ulang & Tagihan Non-SPP</flux:heading>
        <flux:text>Tambahkan jenis tagihan Non-SPP (Daftar Ulang, Seragam, Kegiatan, dll). Tagihan ini akan dikenakan ke semua atau tingkat tertentu.</flux:text>

        @include('livewire.pages.academic.partials.wizard-import-section', ['stepLabel' => 'Tagihan'])

        {{-- Manual Entry --}}
        <div class="p-4 bg-white rounded-xl border border-zinc-200 space-y-4">
            <flux:heading size="sm">Tambah Tagihan Manual</flux:heading>
            <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 items-end">
                <flux:field>
                    <flux:label>Nama Tagihan</flux:label>
                    <flux:input wire:model="newFeeName" placeholder="Daftar Ulang" />
                </flux:field>
                <flux:field>
                    <flux:label>Kategori</flux:label>
                    <flux:select wire:model="newFeeCategory">
                        <flux:select.option value="kegiatan">Kegiatan</flux:select.option>
                        <flux:select.option value="seragam">Seragam</flux:select.option>
                        <flux:select.option value="lain">Lainnya</flux:select.option>
                    </flux:select>
                </flux:field>
                <flux:field>
                    <flux:label>Nominal (Rp)</flux:label>
                    <flux:input type="number" wire:model="newFeeAmount" placeholder="2500000" />
                </flux:field>
                <flux:field>
                    <flux:label>Sasaran Tingkat</flux:label>
                    <flux:input wire:model="newFeeTargetGrades" placeholder="semua / 7,8,9" />
                </flux:field>
                <flux:button variant="primary" wire:click="addFeeTypeManually" icon="plus">Tambah</flux:button>
            </div>
        </div>

        @if(count($feeTypesInvalid) > 0)
            @include('livewire.pages.academic.partials.wizard-invalid-table', [
                'invalidData' => $feeTypesInvalid,
                'columns' => ['name' => 'Nama', 'category' => 'Kategori', 'amount' => 'Nominal', 'target_grades' => 'Sasaran'],
                'type' => 'fee_types',
            ])
        @endif

        @if(count($feeTypesValid) > 0)
            @include('livewire.pages.academic.partials.wizard-valid-table', [
                'validData' => $feeTypesValid,
                'columns' => ['name' => 'Nama', 'category' => 'Kategori', 'amount' => 'Nominal', 'target_grades' => 'Sasaran'],
                'removeMethod' => 'removeFeeTypeValid',
            ])
        @endif
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- STEP 5: RINGKASAN & SUBMIT --}}
    {{-- ============================================================ --}}
    @if($currentStep === 5)
    <div class="space-y-6">
        @if(!empty($submissionResult))
            {{-- Success Result --}}
            <div class="text-center p-8 bg-emerald-50 rounded-xl border border-emerald-200">
                <flux:icon.check-circle class="size-16 text-emerald-600 mx-auto mb-4" />
                <flux:heading size="lg" class="text-emerald-800">Inisialisasi Berhasil! 🎉</flux:heading>
                <flux:text class="mt-2 text-emerald-700">Semua data telah berhasil dibuat.</flux:text>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
                    <div class="p-4 bg-white rounded-lg border border-emerald-200 text-center">
                        <div class="text-2xl font-bold text-emerald-700">{{ $submissionResult['classes_count'] ?? 0 }}</div>
                        <div class="text-sm text-zinc-500">Kelas</div>
                    </div>
                    <div class="p-4 bg-white rounded-lg border border-emerald-200 text-center">
                        <div class="text-2xl font-bold text-emerald-700">{{ $submissionResult['staff_count'] ?? 0 }}</div>
                        <div class="text-sm text-zinc-500">Pengelola</div>
                    </div>
                    <div class="p-4 bg-white rounded-lg border border-emerald-200 text-center">
                        <div class="text-2xl font-bold text-emerald-700">{{ $submissionResult['students_count'] ?? 0 }}</div>
                        <div class="text-sm text-zinc-500">Santri</div>
                    </div>
                    <div class="p-4 bg-white rounded-lg border border-emerald-200 text-center">
                        <div class="text-2xl font-bold text-emerald-700">{{ $submissionResult['invoices_count'] ?? 0 }}</div>
                        <div class="text-sm text-zinc-500">Tagihan</div>
                    </div>
                </div>

                <div class="mt-6">
                    <flux:button variant="primary" :href="route('dashboard')" wire:navigate icon="home">Kembali ke Dashboard</flux:button>
                </div>
            </div>
        @else
            {{-- Pre-submit Summary --}}
            <flux:heading size="lg">📋 Step 5: Ringkasan & Finalisasi</flux:heading>
            <flux:text>Periksa kembali semua data sebelum menyimpan ke sistem.</flux:text>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 bg-white rounded-xl border border-zinc-200 text-center">
                    <flux:icon.building-office-2 class="size-8 text-emerald-600 mx-auto mb-2" />
                    <div class="text-2xl font-bold">{{ $summary['classes'] }}</div>
                    <div class="text-sm text-zinc-500">Kelas</div>
                </div>
                <div class="p-4 bg-white rounded-xl border border-zinc-200 text-center">
                    <flux:icon.user-group class="size-8 text-blue-600 mx-auto mb-2" />
                    <div class="text-2xl font-bold">{{ $summary['staff'] }}</div>
                    <div class="text-sm text-zinc-500">Pengelola</div>
                </div>
                <div class="p-4 bg-white rounded-xl border border-zinc-200 text-center">
                    <flux:icon.users class="size-8 text-amber-600 mx-auto mb-2" />
                    <div class="text-2xl font-bold">{{ $summary['students'] }}</div>
                    <div class="text-sm text-zinc-500">Santri & Wali</div>
                </div>
                <div class="p-4 bg-white rounded-xl border border-zinc-200 text-center">
                    <flux:icon.banknotes class="size-8 text-rose-600 mx-auto mb-2" />
                    <div class="text-2xl font-bold">{{ $summary['fee_types'] }}</div>
                    <div class="text-sm text-zinc-500">Jenis Tagihan Non-SPP</div>
                </div>
            </div>

            <div class="p-4 bg-white rounded-xl border border-zinc-200 space-y-3">
                <flux:heading size="sm">Proyeksi Tagihan 1 Tahun Ajaran</flux:heading>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between py-2 border-b border-zinc-100">
                        <span class="text-zinc-600">Total SPP 12 Bulan (semua santri)</span>
                        <span class="font-semibold">Rp {{ number_format($summary['total_spp_projection'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-zinc-100">
                        <span class="text-zinc-600">Total Tunggakan Awal</span>
                        <span class="font-semibold">Rp {{ number_format($summary['total_arrears'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-zinc-100">
                        <span class="text-zinc-600">Total Tagihan Non-SPP</span>
                        <span class="font-semibold">Rp {{ number_format($summary['total_non_spp'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2 text-base font-bold text-emerald-700">
                        <span>Grand Total Proyeksi</span>
                        <span>Rp {{ number_format($summary['total_spp_projection'] + $summary['total_arrears'] + $summary['total_non_spp'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-amber-50 rounded-xl border border-amber-200">
                <div class="flex items-start gap-3">
                    <flux:icon.exclamation-triangle class="size-6 text-amber-600 mt-0.5 shrink-0" />
                    <div>
                        <flux:heading size="sm" class="text-amber-800">Perhatian</flux:heading>
                        <flux:text class="text-amber-700 text-sm">Setelah menekan tombol "Submit", sistem akan membuat seluruh akun pengguna, data santri, dan tagihan 1 tahun penuh secara otomatis. Pastikan semua data sudah benar.</flux:text>
                    </div>
                </div>
            </div>

            <div class="flex justify-center">
                <flux:button
                    variant="primary"
                    wire:click="submitWizard"
                    wire:loading.attr="disabled"
                    icon="rocket-launch"
                    class="px-8 py-3 text-base"
                >
                    <span wire:loading.remove wire:target="submitWizard">Submit & Inisialisasi Sistem</span>
                    <span wire:loading wire:target="submitWizard">Memproses...</span>
                </flux:button>
            </div>
        @endif
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- NAVIGATION BUTTONS --}}
    {{-- ============================================================ --}}
    @if(empty($submissionResult))
    <div class="flex justify-between mt-8 pt-6 border-t border-zinc-200">
        <div>
            @if($currentStep > 1)
                <flux:button variant="ghost" wire:click="previousStep" icon="arrow-left">Sebelumnya</flux:button>
            @endif
        </div>
        <div>
            @if($currentStep < 5)
                <flux:button variant="primary" wire:click="nextStep" icon-trailing="arrow-right">Selanjutnya</flux:button>
            @endif
        </div>
    </div>
    @endif
</div>
