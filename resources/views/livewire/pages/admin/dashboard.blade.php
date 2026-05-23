<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:main>
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <flux:heading size="xl">{{ __('Dashboard Admin') }}</flux:heading>
                <flux:text>{{ now()->translatedFormat('l, d F Y') }}</flux:text>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <flux:card class="flex flex-col gap-2">
                    <flux:text size="sm" class="text-zinc-500">{{ __('Total Siswa Aktif') }}</flux:text>
                    <flux:heading size="xl">{{ number_format($totalStudents) }}</flux:heading>
                </flux:card>

                <flux:card class="flex flex-col gap-2">
                    <flux:text size="sm" class="text-zinc-500">{{ __('Pemasukan Bulan Ini') }}</flux:text>
                    <flux:heading size="xl">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</flux:heading>
                </flux:card>

                <flux:card class="flex flex-col gap-2">
                    <flux:text size="sm" class="text-zinc-500">{{ __('Total Tunggakan') }}</flux:text>
                    <flux:heading size="xl" class="text-red-600 dark:text-red-400">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</flux:heading>
                </flux:card>

                <flux:card class="flex flex-col gap-2">
                    <flux:text size="sm" class="text-zinc-500">{{ __('Tagihan Baru Bulan Ini') }}</flux:text>
                    <flux:heading size="xl">Rp {{ number_format($newInvoicesThisMonth, 0, ',', '.') }}</flux:heading>
                </flux:card>
            </div>

            <!-- Chart Section -->
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Tren Pemasukan (12 Bulan Terakhir)') }}</flux:heading>
                <div id="incomeChart" style="min-height: 350px;"></div>
            </flux:card>

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
                                    <flux:table.cell>{{ $invoice->feeType->name }}</flux:table.cell>
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
                                    <flux:table.cell>{{ $payment->invoice->feeType->name }}</flux:table.cell>
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

            const options = {
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: { show: false },
                    background: 'transparent'
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
                colors: ['#2563eb'],
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '60%',
                    }
                },
                dataLabels: { enabled: false },
                grid: {
                    borderColor: document.documentElement.classList.contains('dark') ? '#3f3f46' : '#e4e4e7'
                }
            };

            const chart = new ApexCharts(chartElement, options);
            chart.render();
        });
    </script>
    @endpush
</div>
