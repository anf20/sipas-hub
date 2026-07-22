<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <flux:header>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('academic.dashboard') }}" wire:navigate>{{ __('Akademik') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Ringkasan') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:spacer />
        <div class="hidden md:block font-bold text-sm text-slate-700 dark:text-zinc-300">
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
    </flux:header>

    <div class="flex justify-between items-center">
        <flux:heading size="xl">{{ __('Dashboard Akademik') }}</flux:heading>
    </div>

    <div class="space-y-6">
        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <flux:card class="flex flex-col gap-2 relative overflow-hidden group">
                <div class="absolute right-0 top-0 opacity-5 group-hover:scale-110 transition-transform -mr-4 -mt-4">
                    <flux:icon.users class="w-24 h-24 text-blue-500" />
                </div>
                <flux:text size="sm" class="uppercase font-bold tracking-wider text-blue-600 dark:text-blue-400">{{ __('Total Siswa Aktif') }}</flux:text>
                <flux:heading size="xl" class="text-slate-800 dark:text-zinc-100">{{ number_format($totalStudents) }}</flux:heading>
            </flux:card>

            <flux:card class="flex flex-col gap-2 relative overflow-hidden group">
                <div class="absolute right-0 top-0 opacity-5 group-hover:scale-110 transition-transform -mr-4 -mt-4">
                    <flux:icon.building-office-2 class="w-24 h-24 text-emerald-500" />
                </div>
                <flux:text size="sm" class="uppercase font-bold tracking-wider text-emerald-600 dark:text-emerald-400">{{ __('Total Kelas') }}</flux:text>
                <flux:heading size="xl" class="text-slate-800 dark:text-zinc-100">{{ number_format($totalClasses) }}</flux:heading>
            </flux:card>

            <flux:card class="flex flex-col gap-2 relative overflow-hidden group">
                <div class="absolute right-0 top-0 opacity-5 group-hover:scale-110 transition-transform -mr-4 -mt-4">
                    <flux:icon.calendar-days class="w-24 h-24 text-purple-500" />
                </div>
                <flux:text size="sm" class="uppercase font-bold tracking-wider text-purple-600 dark:text-purple-400">{{ __('Tahun Ajaran Aktif') }}</flux:text>
                <flux:heading size="xl" class="text-slate-800 dark:text-zinc-100">{{ $activeYear?->name ?? '-' }}</flux:heading>
            </flux:card>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Distribusi Jenis Kelamin') }}</flux:heading>
                <div id="genderChart" style="min-height: 300px;"></div>
            </flux:card>
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Siswa per Jenjang Kelas') }}</flux:heading>
                <div id="gradeChart" style="min-height: 300px;"></div>
            </flux:card>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:navigated', () => {
            renderCharts();
        });

        function renderCharts() {
            const genderElement = document.querySelector("#genderChart");
            if (genderElement) {
                genderElement.innerHTML = ''; 
                new ApexCharts(genderElement, {
                    chart: { type: 'pie', height: 300 },
                    series: @json($genderValues),
                    labels: @json($genderLabels),
                    theme: { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' },
                    colors: ['#2563eb', '#db2777']
                }).render();
            }

            const gradeElement = document.querySelector("#gradeChart");
            if (gradeElement) {
                gradeElement.innerHTML = '';
                new ApexCharts(gradeElement, {
                    chart: { type: 'bar', height: 300 },
                    series: [{ name: '{{ __("Siswa") }}', data: @json($gradeValues) }],
                    xaxis: { categories: @json($gradeLabels) },
                    theme: { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' },
                    colors: ['#2563eb']
                }).render();
            }
        }
    </script>
    @endpush
</div>
