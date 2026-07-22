<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">{{ __('Pusat Pemulihan Data') }}</flux:heading>
            <flux:subheading>{{ __('Kelola dan pulihkan data yang telah dihapus.') }}</flux:subheading>
        </div>
    </div>

    <flux:navlist variant="outline" class="flex-row gap-2 border-b border-zinc-200 dark:border-zinc-700 pb-0 mb-6">
        <flux:navlist.item wire:click="setTab('students')" :current="$tab === 'students'" class="cursor-pointer">{{ __('Siswa') }}</flux:navlist.item>
        <flux:navlist.item wire:click="setTab('invoices')" :current="$tab === 'invoices'" class="cursor-pointer">{{ __('Tagihan') }}</flux:navlist.item>
        <flux:navlist.item wire:click="setTab('payments')" :current="$tab === 'payments'" class="cursor-pointer">{{ __('Pembayaran') }}</flux:navlist.item>
    </flux:navlist>

    @if($tab === 'students')
        <div class="space-y-4">
            <flux:table :paginate="$trashedStudents">
                <flux:table.columns>
                    <flux:table.column>{{ __('NIS') }}</flux:table.column>
                    <flux:table.column>{{ __('Nama') }}</flux:table.column>
                    <flux:table.column>{{ __('Kelas') }}</flux:table.column>
                    <flux:table.column>{{ __('Dihapus Pada') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($trashedStudents as $student)
                        <flux:table.row :key="$student->id">
                            <flux:table.cell font="medium">{{ $student->nis }}</flux:table.cell>
                            <flux:table.cell>{{ $student->name }}</flux:table.cell>
                            <flux:table.cell>{{ $student->schoolClass->name ?? '-' }}</flux:table.cell>
                            <flux:table.cell>{{ $student->deleted_at->format('d/m/Y H:i') }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" icon="arrow-path" wire:click="restoreStudent({{ $student->id }})" wire:confirm="{{ __('Pulihkan data siswa ini?') }}">
                                        {{ __('Pulihkan') }}
                                    </flux:button>
                                    <flux:button size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-600" wire:click="forceDeleteStudent({{ $student->id }})" wire:confirm="{{ __('Hapus permanen? Tindakan ini tidak dapat dibatalkan.') }}">
                                        {{ __('Hapus Permanen') }}
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-8 text-zinc-500">
                                {{ __('Tidak ada data siswa yang dihapus.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    @endif

    @if($tab === 'invoices')
        <div class="space-y-4">
            <flux:table :paginate="$trashedInvoices">
                <flux:table.columns>
                    <flux:table.column>{{ __('Siswa') }}</flux:table.column>
                    <flux:table.column>{{ __('Jenis Tagihan') }}</flux:table.column>
                    <flux:table.column>{{ __('Nominal') }}</flux:table.column>
                    <flux:table.column>{{ __('Periode') }}</flux:table.column>
                    <flux:table.column>{{ __('Dihapus Pada') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($trashedInvoices as $invoice)
                        <flux:table.row :key="$invoice->id">
                            <flux:table.cell font="medium">{{ $invoice->student->name ?? __('Terhapus Permanen') }}</flux:table.cell>
                            <flux:table.cell>{{ $invoice->billing_detail ?? '-' }}</flux:table.cell>
                            <flux:table.cell>{{ number_format($invoice->amount, 0, ',', '.') }}</flux:table.cell>
                            <flux:table.cell>{{ $invoice->period_month }}/{{ $invoice->period_year }}</flux:table.cell>
                            <flux:table.cell>{{ $invoice->deleted_at->format('d/m/Y H:i') }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" icon="arrow-path" wire:click="restoreInvoice({{ $invoice->id }})" wire:confirm="{{ __('Pulihkan tagihan ini?') }}">
                                        {{ __('Pulihkan') }}
                                    </flux:button>
                                    <flux:button size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-600" wire:click="forceDeleteInvoice({{ $invoice->id }})" wire:confirm="{{ __('Hapus permanen? Tindakan ini tidak dapat dibatalkan.') }}">
                                        {{ __('Hapus Permanen') }}
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center py-8 text-zinc-500">
                                {{ __('Tidak ada tagihan yang dihapus.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    @endif

    @if($tab === 'payments')
        <div class="space-y-4">
            <flux:table :paginate="$trashedPayments">
                <flux:table.columns>
                    <flux:table.column>{{ __('No. Kwitansi') }}</flux:table.column>
                    <flux:table.column>{{ __('Siswa') }}</flux:table.column>
                    <flux:table.column>{{ __('Nominal') }}</flux:table.column>
                    <flux:table.column>{{ __('Tanggal Bayar') }}</flux:table.column>
                    <flux:table.column>{{ __('Dihapus Pada') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($trashedPayments as $payment)
                        <flux:table.row :key="$payment->id">
                            <flux:table.cell font="medium">{{ $payment->receipt_number }}</flux:table.cell>
                            <flux:table.cell>{{ $payment->invoice->student->name ?? '-' }}</flux:table.cell>
                            <flux:table.cell>{{ number_format($payment->amount, 0, ',', '.') }}</flux:table.cell>
                            <flux:table.cell>{{ $payment->paid_at?->format('d/m/Y') ?? '-' }}</flux:table.cell>
                            <flux:table.cell>{{ $payment->deleted_at->format('d/m/Y H:i') }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" variant="ghost" icon="arrow-path" wire:click="restorePayment({{ $payment->id }})" wire:confirm="{{ __('Pulihkan pembayaran ini?') }}">
                                        {{ __('Pulihkan') }}
                                    </flux:button>
                                    <flux:button size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-600" wire:click="forceDeletePayment({{ $payment->id }})" wire:confirm="{{ __('Hapus permanen? Tindakan ini tidak dapat dibatalkan.') }}">
                                        {{ __('Hapus Permanen') }}
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center py-8 text-zinc-500">
                                {{ __('Tidak ada pembayaran yang dihapus.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    @endif
</div>
