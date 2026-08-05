<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:main>
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <flux:heading size="xl">{{ __('Dashboard Admin') }}</flux:heading>
                <flux:text>{{ now()->translatedFormat('l, d F Y') }}</flux:text>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Siswa Aktif -->
                <flux:card class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-forest-surface text-forest-dark shrink-0">
                        <flux:icon.users class="size-6 text-forest-dark" variant="solid" />
                    </div>
                    <div class="flex flex-col">
                        <flux:text size="sm" class="text-forest-text-muted font-medium">{{ __('Total Siswa Aktif') }}</flux:text>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <flux:heading size="xl" class="text-forest-text-main font-bold leading-none">{{ number_format($totalStudents) }}</flux:heading>
                            <span class="text-xs font-semibold text-forest-success bg-forest-success/10 px-2 py-0.5 rounded-full">+3%</span>
                        </div>
                    </div>
                </flux:card>

                <!-- Pemasukan Bulan Ini -->
                <flux:card class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-forest-surface text-forest-dark shrink-0">
                        <flux:icon.banknotes class="size-6 text-forest-dark" variant="solid" />
                    </div>
                    <div class="flex flex-col">
                        <flux:text size="sm" class="text-forest-text-muted font-medium">{{ __('Pemasukan Bulan Ini') }}</flux:text>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <flux:heading size="xl" class="text-forest-text-main font-bold leading-none">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</flux:heading>
                            <span class="text-xs font-semibold text-forest-success bg-forest-success/10 px-2 py-0.5 rounded-full">+12%</span>
                        </div>
                    </div>
                </flux:card>

                <!-- Total Tunggakan -->
                <flux:card class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-forest-surface text-forest-dark shrink-0">
                        <flux:icon.exclamation-circle class="size-6 text-forest-dark" variant="solid" />
                    </div>
                    <div class="flex flex-col">
                        <flux:text size="sm" class="text-forest-text-muted font-medium">{{ __('Total Tunggakan') }}</flux:text>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <flux:heading size="xl" class="text-forest-danger font-bold leading-none">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</flux:heading>
                            <span class="text-xs font-semibold text-forest-danger bg-forest-danger/10 px-2 py-0.5 rounded-full">-5%</span>
                        </div>
                    </div>
                </flux:card>

                <!-- Tagihan Baru Bulan Ini -->
                <flux:card class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-forest-surface text-forest-dark shrink-0">
                        <flux:icon.document-text class="size-6 text-forest-dark" variant="solid" />
                    </div>
                    <div class="flex flex-col">
                        <flux:text size="sm" class="text-forest-text-muted font-medium">{{ __('Tagihan Baru Bulan Ini') }}</flux:text>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <flux:heading size="xl" class="text-forest-text-main font-bold leading-none">Rp {{ number_format($newInvoicesThisMonth, 0, ',', '.') }}</flux:heading>
                            <span class="text-xs font-semibold text-forest-success bg-forest-success/10 px-2 py-0.5 rounded-full">+8%</span>
                        </div>
                    </div>
                </flux:card>
            </div>

            <!-- Chart & Feature Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Chart Section -->
                <flux:card class="lg:col-span-2">
                    <div class="flex justify-between items-center mb-4">
                        <flux:heading size="lg" class="text-forest-text-main font-semibold">{{ __('Tren Pemasukan (12 Bulan Terakhir)') }}</flux:heading>
                        <div class="flex gap-1 bg-forest-surface p-1 rounded-lg border border-forest-light-sage/20">
                            <button id="btnLineChart" class="px-3 py-1 text-xs font-semibold rounded-md bg-white text-forest-text-main shadow-xs cursor-pointer transition-all duration-150">Line</button>
                            <button id="btnBarChart" class="px-3 py-1 text-xs font-semibold rounded-md text-forest-text-muted hover:text-forest-text-main cursor-pointer transition-all duration-150">Bar</button>
                        </div>
                    </div>
                    <div id="incomeChart" style="min-height: 350px;"></div>
                </flux:card>

                <!-- Feature Highlight Card (Dark Green Card with SVG Radial Progress Chart) -->
                <div class="dark-card flex flex-col justify-between h-full min-h-[350px]">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <span class="text-xs font-semibold tracking-wider text-forest-light-sage uppercase">{{ __('Target Capaian SPP') }}</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-forest-sage text-forest-surface">
                                {{ __('On Track') }}
                            </span>
                        </div>
                        
                        <flux:heading size="lg" class="text-white font-bold leading-tight mb-2">
                            {{ __('Kolektibilitas SPP') }}
                        </flux:heading>
                        <p class="text-forest-light-sage text-sm leading-relaxed mb-4">
                            {{ __('Progres pembayaran SPP siswa terhadap total tagihan aktif pada bulan berjalan.') }}
                        </p>
                    </div>

                    <div class="flex flex-col items-center justify-center my-auto py-2">
                        <div class="relative flex items-center justify-center">
                            <!-- SVG Radial Progress Ring -->
                            <svg class="w-32 h-32 transform -rotate-90">
                                <!-- Background Circle -->
                                <circle
                                    cx="64"
                                    cy="64"
                                    r="52"
                                    stroke="rgba(55, 85, 52, 0.4)"
                                    stroke-width="10"
                                    fill="transparent"
                                />
                                <!-- Progress Circle -->
                                <circle
                                    cx="64"
                                    cy="64"
                                    r="52"
                                    stroke="#6B9071"
                                    stroke-width="10"
                                    fill="transparent"
                                    stroke-dasharray="326.7"
                                    stroke-dashoffset="71.8"
                                    stroke-linecap="round"
                                />
                            </svg>
                            <!-- Inside text -->
                            <div class="absolute flex flex-col items-center justify-center">
                                <span class="text-3xl font-extrabold text-white">78%</span>
                                <span class="text-[10px] text-forest-light-sage uppercase tracking-wider font-semibold">{{ __('Tercapai') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between text-xs text-forest-light-sage mt-2">
                        <span>Rp 42.150.000 / Rp 54.000.000</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Upcoming Due Invoices -->
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('Jatuh Tempo (7 Hari ke Depan)') }}</flux:heading>
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>{{ __('Siswa') }}</flux:table.column>
                            <flux:table.column>{{ __('Tagihan') }}</flux:table.column>
                            <flux:table.column>{{ __('Nominal') }}</flux:table.column>
                            <flux:table.column>{{ __('Jatuh Tempo') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @forelse($upcomingInvoices as $invoice)
                                <flux:table.row>
                                    <flux:table.cell>
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ $invoice->student->name }}</span>
                                            <span class="text-xs text-zinc-500">{{ $invoice->student->nis }}</span>
                                        </div>
                                    </flux:table.cell>
                                    <flux:table.cell>{{ $invoice->billing_detail }}</flux:table.cell>
                                    <flux:table.cell>Rp {{ number_format($invoice->amount, 0, ',', '.') }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge color="red">{{ $invoice->due_date->format('d/m/Y') }}</flux:badge>
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="4" class="text-center py-4 text-zinc-500">
                                        {{ __('Tidak ada tagihan yang akan jatuh tempo.') }}
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </flux:card>

                <!-- Recent Payments -->
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('Pembayaran Terakhir') }}</flux:heading>
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>{{ __('Siswa') }}</flux:table.column>
                            <flux:table.column>{{ __('Tagihan') }}</flux:table.column>
                            <flux:table.column>{{ __('Nominal') }}</flux:table.column>
                            <flux:table.column>{{ __('Tanggal') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @forelse($recentPayments as $payment)
                                <flux:table.row>
                                    <flux:table.cell>
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ $payment->invoice->student->name }}</span>
                                            <span class="text-xs text-zinc-500">{{ $payment->invoice->student->nis }}</span>
                                        </div>
                                    </flux:table.cell>
                                    <flux:table.cell>{{ $payment->invoice->billing_detail }}</flux:table.cell>
                                    <flux:table.cell>Rp {{ number_format($payment->amount, 0, ',', '.') }}</flux:table.cell>
                                    <flux:table.cell>{{ $payment->paid_at->format('d/m/Y H:i') }}</flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="4" class="text-center py-4 text-zinc-500">
                                        {{ __('Belum ada data pembayaran.') }}
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </flux:card>
            </div>
        </div>
    </flux:main>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:navigated', () => {
            const chartElement = document.querySelector("#incomeChart");
            if (!chartElement) return;

            chartElement.innerHTML = ''; // Prevent duplicate charts

            let currentChartType = 'line'; // Default is area/line

            const chartOptions = (type) => ({
                chart: {
                    type: type === 'line' ? 'area' : 'bar',
                    height: 350,
                    toolbar: { show: false },
                    background: 'transparent'
                },
                stroke: {
                    curve: 'smooth',
                    width: type === 'line' ? 3 : 0
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: type === 'line' ? 0.35 : 0.85,
                        opacityTo: type === 'line' ? 0.05 : 0.15,
                        stops: [0, 90, 100]
                    }
                },
                theme: {
                    mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                },
                series: [{
                    name: '{{ __('Pemasukan') }}',
                    data: @json($chartData)
                }],
                xaxis: {
                    categories: @json($chartLabels)
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return "Rp " + value.toLocaleString('id-ID');
                        }
                    }
                },
                colors: ['#6B9071'], // Forest Sage Accent
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '50%',
                    }
                },
                dataLabels: { enabled: false },
                grid: {
                    borderColor: 'rgba(174, 195, 176, 0.15)'
                }
            });

            let chart = new ApexCharts(chartElement, chartOptions(currentChartType));
            chart.render();

            const btnLine = document.querySelector("#btnLineChart");
            const btnBar = document.querySelector("#btnBarChart");

            if (btnLine && btnBar) {
                btnLine.addEventListener('click', () => {
                    btnLine.classList.add('bg-white', 'text-forest-text-main', 'shadow-xs');
                    btnLine.classList.remove('text-forest-text-muted');
                    btnBar.classList.remove('bg-white', 'text-forest-text-main', 'shadow-xs');
                    btnBar.classList.add('text-forest-text-muted');
                    
                    chart.updateOptions(chartOptions('line'));
                });

                btnBar.addEventListener('click', () => {
                    btnBar.classList.add('bg-white', 'text-forest-text-main', 'shadow-xs');
                    btnBar.classList.remove('text-forest-text-muted');
                    btnLine.classList.remove('bg-white', 'text-forest-text-main', 'shadow-xs');
                    btnLine.classList.add('text-forest-text-muted');
                    
                    chart.updateOptions(chartOptions('bar'));
                });
            }
        });
    </script>
    @endpush
</div>
