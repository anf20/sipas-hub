<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <flux:header>
        <flux:heading size="xl">{{ __('Pembayaran Manual') }}</flux:heading>
    </flux:header>

    <flux:main>
        <div class="space-y-6">
            
            <!-- 1. Search Section -->
            <flux:card class="border-blue-100 dark:border-blue-900/50 bg-blue-50/50 dark:bg-blue-900/10 shadow-sm relative">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="w-full md:w-1/2">
                        <flux:heading size="lg" class="mb-2">{{ __('Cari Siswa') }}</flux:heading>
                        <div class="relative">
                            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Ketik NIS atau Nama Siswa...') }}" size="lg" autofocus />
                            
                            <!-- Search Results Dropdown -->
                            @if(strlen($search) >= 2 && !empty($students))
                                <div class="absolute z-10 w-full mt-1 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    @foreach($students as $student)
                                        <div wire:click="selectStudent({{ $student->id }})" class="px-4 py-3 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer flex justify-between items-center border-b border-zinc-100 dark:border-zinc-700 last:border-0 transition-colors">
                                            <div>
                                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $student->name }}</div>
                                                <div class="text-sm text-zinc-500">{{ $student->nis }} • {{ $student->schoolClass->name }}</div>
                                            </div>
                                            <flux:icon.chevron-right class="text-zinc-400" size="sm" />
                                        </div>
                                    @endforeach
                                </div>
                            @elseif(strlen($search) >= 2 && empty($students))
                                <div class="absolute z-10 w-full mt-1 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg p-4 text-center text-zinc-500">
                                    {{ __('Siswa tidak ditemukan.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($selectedStudent)
                        <flux:button wire:click="clearSelection" variant="ghost" icon="x-mark">{{ __('Ganti Siswa') }}</flux:button>
                    @endif
                </div>
            </flux:card>

            <!-- 2. Student & Payment Workspace -->
            @if($selectedStudent)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Left: Profile & Stats -->
                    <div class="space-y-6">
                        <flux:card>
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-xl">
                                    {{ substr($selectedStudent->name, 0, 1) }}
                                </div>
                                <div>
                                    <flux:heading size="lg">{{ $selectedStudent->name }}</flux:heading>
                                    <flux:subheading>{{ $selectedStudent->nis }}</flux:subheading>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                                    <span class="text-zinc-500">{{ __('Kelas') }}</span>
                                    <span class="font-medium">{{ $selectedStudent->schoolClass->name }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                                    <span class="text-zinc-500">{{ __('Tahun Ajaran') }}</span>
                                    <span class="font-medium">{{ $selectedStudent->schoolClass->academicYear->name }}</span>
                                </div>
                                <div class="flex justify-between py-2">
                                    <span class="text-zinc-500">{{ __('Total Tunggakan') }}</span>
                                    <span class="font-bold text-orange-600 dark:text-orange-400">Rp {{ number_format($unpaidInvoices->sum('amount'), 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </flux:card>
                    </div>

                    <!-- Right: Invoices (Pending & History) -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Pending Invoices -->
                        <flux:card class="p-0 overflow-hidden border-orange-200 dark:border-orange-900/50">
                            <div class="p-4 bg-orange-50 dark:bg-orange-900/10 border-b border-orange-100 dark:border-orange-900/30 flex justify-between items-center">
                                <div class="flex items-center gap-2 text-orange-700 dark:text-orange-400">
                                    <flux:icon.exclamation-circle variant="mini" />
                                    <flux:heading size="md" class="text-inherit">{{ __('Tagihan Belum Lunas') }}</flux:heading>
                                </div>
                                <flux:badge color="orange" size="sm">{{ $unpaidInvoices->count() }}</flux:badge>
                            </div>

                            @if($unpaidInvoices->count() > 0)
                                <flux:table>
                                    <flux:table.columns>
                                        <flux:table.column>{{ __('Tagihan') }}</flux:table.column>
                                        <flux:table.column>{{ __('Jatuh Tempo') }}</flux:table.column>
                                        <flux:table.column align="end">{{ __('Nominal') }}</flux:table.column>
                                        <flux:table.column align="end"></flux:table.column>
                                    </flux:table.columns>

                                    <flux:table.rows>
                                        @foreach($unpaidInvoices as $invoice)
                                            <flux:table.row :key="$invoice->id">
                                                <flux:table.cell>
                                                    <div class="font-medium">{{ $invoice->billing_detail }}</div>
                                                    @if($invoice->feeType->is_recurring)
                                                        <div class="text-xs text-zinc-500">{{ \Carbon\Carbon::create()->month($invoice->period_month)->translatedFormat('F') }} {{ $invoice->period_year }}</div>
                                                    @endif
                                                </flux:table.cell>
                                                <flux:table.cell>
                                                    <span class="{{ $invoice->due_date->isPast() ? 'text-red-600 font-medium' : '' }}">
                                                        {{ $invoice->due_date->format('d/m/Y') }}
                                                    </span>
                                                </flux:table.cell>
                                                <flux:table.cell align="end" class="font-mono font-medium">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</flux:table.cell>
                                                <flux:table.cell align="end">
                                                    <flux:modal.trigger name="payment-modal">
                                                        <flux:button wire:click="openPaymentModal({{ $invoice->id }}, {{ $invoice->amount }})" variant="primary" size="sm">{{ __('Bayar') }}</flux:button>
                                                    </flux:modal.trigger>
                                                </flux:table.cell>
                                            </flux:table.row>
                                        @endforeach
                                    </flux:table.rows>
                                </flux:table>
                            @else
                                <div class="p-8 text-center text-zinc-500">
                                    <flux:icon.check-circle class="mx-auto mb-2 text-green-500" size="lg" />
                                    {{ __('Siswa ini tidak memiliki tunggakan.') }}
                                </div>
                            @endif
                        </flux:card>

                        <!-- Future Invoices -->
                        <flux:card class="p-0 overflow-hidden border-slate-200 dark:border-slate-800">
                            <div class="p-4 bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-100 dark:border-zinc-800 flex justify-between items-center">
                                <div class="flex items-center gap-2 text-slate-700 dark:text-zinc-300">
                                    <flux:icon.calendar variant="mini" />
                                    <flux:heading size="md" class="text-inherit">{{ __('Tagihan Masa Depan (Belum Jatuh Tempo)') }}</flux:heading>
                                </div>
                                <flux:badge color="zinc" size="sm">{{ $futureInvoices->count() }}</flux:badge>
                            </div>

                            @if($futureInvoices->count() > 0)
                                <flux:table>
                                    <flux:table.columns>
                                        <flux:table.column>{{ __('Tagihan') }}</flux:table.column>
                                        <flux:table.column>{{ __('Jatuh Tempo') }}</flux:table.column>
                                        <flux:table.column align="end">{{ __('Nominal') }}</flux:table.column>
                                        <flux:table.column align="end"></flux:table.column>
                                    </flux:table.columns>

                                    <flux:table.rows>
                                        @foreach($futureInvoices as $invoice)
                                            <flux:table.row :key="$invoice->id">
                                                <flux:table.cell>
                                                    <div class="font-medium text-slate-500">{{ $invoice->billing_detail }}</div>
                                                    @if($invoice->feeType->is_recurring)
                                                        <div class="text-xs text-slate-400">{{ \Carbon\Carbon::create()->month($invoice->period_month)->translatedFormat('F') }} {{ $invoice->period_year }}</div>
                                                    @endif
                                                </flux:table.cell>
                                                <flux:table.cell>
                                                    <span class="text-slate-500">
                                                        {{ $invoice->due_date->format('d/m/Y') }}
                                                    </span>
                                                </flux:table.cell>
                                                <flux:table.cell align="end" class="font-mono font-medium text-slate-500">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</flux:table.cell>
                                                <flux:table.cell align="end">
                                                    <flux:modal.trigger name="payment-modal">
                                                        <flux:button wire:click="openPaymentModal({{ $invoice->id }}, {{ $invoice->amount }})" variant="subtle" size="sm">{{ __('Bayar Lebih Awal') }}</flux:button>
                                                    </flux:modal.trigger>
                                                </flux:table.cell>
                                            </flux:table.row>
                                        @endforeach
                                    </flux:table.rows>
                                </flux:table>
                            @else
                                <div class="p-8 text-center text-zinc-400">
                                    <flux:icon.document-check class="mx-auto mb-2 text-slate-300" size="lg" />
                                    {{ __('Tidak ada tagihan masa depan untuk siswa ini.') }}
                                </div>
                            @endif
                        </flux:card>

                        <!-- Payment History -->
                        <flux:card class="p-0 overflow-hidden">
                            <div class="p-4 border-b border-zinc-100 dark:border-zinc-800">
                                <flux:heading size="md">{{ __('Riwayat Pembayaran Terakhir') }}</flux:heading>
                            </div>

                            @if($paidInvoices->count() > 0)
                                <flux:table>
                                    <flux:table.columns>
                                        <flux:table.column>{{ __('Tagihan') }}</flux:table.column>
                                        <flux:table.column>{{ __('Tanggal Bayar') }}</flux:table.column>
                                        <flux:table.column>{{ __('Kwitansi / Metode') }}</flux:table.column>
                                    </flux:table.columns>

                                    <flux:table.rows>
                                        @foreach($paidInvoices as $invoice)
                                            @php $payment = $invoice->payments->first(); @endphp
                                            <flux:table.row :key="'paid-'.$invoice->id">
                                                <flux:table.cell>
                                                    <div class="font-medium">{{ $invoice->billing_detail }}</div>
                                                    <div class="text-xs text-green-600 dark:text-green-400">{{ __('Lunas') }}</div>
                                                </flux:table.cell>
                                                <flux:table.cell>
                                                    {{ $payment ? $payment->paid_at->format('d/m/Y H:i') : $invoice->updated_at->format('d/m/Y H:i') }}
                                                </flux:table.cell>
                                                <flux:table.cell>
                                                    @if($payment)
                                                        <div class="font-mono text-xs">{{ $payment->receipt_number }}</div>
                                                        <div class="text-xs text-zinc-500 uppercase">{{ $payment->method }}</div>
                                                    @else
                                                        <span class="text-zinc-400">-</span>
                                                    @endif
                                                </flux:table.cell>
                                            </flux:table.row>
                                        @endforeach
                                    </flux:table.rows>
                                </flux:table>
                            @else
                                <div class="p-8 text-center text-zinc-500">
                                    {{ __('Belum ada riwayat pembayaran.') }}
                                </div>
                            @endif
                        </flux:card>

                    </div>
                </div>
            @else
                <div class="py-24 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-800 mb-4">
                        <flux:icon.user-circle class="text-zinc-400" size="xl" />
                    </div>
                    <flux:heading size="lg" class="mb-1">{{ __('Cari Siswa Terlebih Dahulu') }}</flux:heading>
                    <flux:text class="text-zinc-500">{{ __('Ketik NIS atau Nama pada kolom pencarian di atas untuk memproses pembayaran.') }}</flux:text>
                </div>
            @endif

        </div>
    </flux:main>

    <!-- Payment Confirmation Modal -->
    <flux:modal name="payment-modal" class="min-w-[24rem]">
        <form wire:submit.prevent="processPayment" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Proses Pembayaran') }}</flux:heading>
                <flux:subheading>{{ __('Pastikan nominal yang diterima sudah sesuai.') }}</flux:subheading>
            </div>

            <div class="space-y-4">
                <div class="p-4 bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                    <span class="text-zinc-500">{{ __('Total Harus Dibayar') }}</span>
                    <span class="font-bold text-lg">Rp {{ number_format($paymentAmount, 0, ',', '.') }}</span>
                </div>

                <div class="space-y-2">
                    <flux:select wire:model="paymentMethod" label="{{ __('Metode Pembayaran') }}">
                        <flux:select.option value="cash">{{ __('Tunai (Cash)') }}</flux:select.option>
                        <flux:select.option value="transfer">{{ __('Transfer Bank') }}</flux:select.option>
                    </flux:select>
                    <flux:error name="paymentMethod" />
                </div>
                
                <flux:error name="selectedInvoiceId" />
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" wire:click="resetPaymentForm">{{ __('Batal') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">{{ __('Konfirmasi Pembayaran') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
