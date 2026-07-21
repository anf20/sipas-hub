<?php

namespace App\Livewire\Pages\Finance;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PaymentManual extends Component
{
    public $search = '';

    public $selectedStudentId = null;

    // Payment form state for modal
    public $selectedInvoiceId = null;

    public $paymentMethod = 'cash';

    public $paymentAmount = 0;

    public function selectStudent($id)
    {
        $this->selectedStudentId = $id;
        $this->search = '';
    }

    public function clearSelection()
    {
        $this->selectedStudentId = null;
        $this->resetPaymentForm();
    }

    public function openPaymentModal($invoiceId, $amount)
    {
        $this->selectedInvoiceId = $invoiceId;
        $this->paymentAmount = $amount;
        $this->paymentMethod = 'cash';
    }

    public function resetPaymentForm()
    {
        $this->selectedInvoiceId = null;
        $this->paymentAmount = 0;
        $this->paymentMethod = 'cash';
        $this->resetErrorBag();
    }

    public function processPayment()
    {
        $this->validate([
            'selectedInvoiceId' => 'required|exists:invoices,id',
            'paymentMethod' => 'required|in:cash,transfer',
            'paymentAmount' => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            $invoice = Invoice::where('id', $this->selectedInvoiceId)->whereIn('status', ['unpaid', 'inactive'])->lockForUpdate()->firstOrFail();

            // Generate receipt number (SCH-YYYYMM-XXXX)
            $prefix = 'SCH-'.date('Ym').'-';
            $lastPayment = Payment::where('receipt_number', 'like', $prefix.'%')
                ->orderBy('id', 'desc')
                ->first();

            $sequence = 1;
            if ($lastPayment) {
                $lastSequence = (int) substr($lastPayment->receipt_number, -4);
                $sequence = $lastSequence + 1;
            }
            $receiptNumber = $prefix.str_pad($sequence, 4, '0', STR_PAD_LEFT);

            // Create Payment Record
            Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $this->paymentAmount,
                'method' => $this->paymentMethod,
                'paid_at' => now(),
                'receipt_number' => $receiptNumber,
                'recorded_by' => auth()->id(),
            ]);

            // Update Invoice
            $invoice->update([
                'status' => 'paid',
            ]);

            DB::commit();

            \Flux::toast(__('Pembayaran berhasil diproses. Nomor Kwitansi: :receipt', ['receipt' => $receiptNumber]), variant: 'success');

            $this->dispatch('close-modal', 'payment-modal');
            $this->resetPaymentForm();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual Payment Failed: '.$e->getMessage());
            \Flux::toast(__('Gagal memproses pembayaran. Silakan coba lagi.'), variant: 'danger');
        }
    }

    public function render()
    {
        $students = [];
        if (strlen($this->search) >= 2) {
            $students = Student::with('schoolClass')
                ->where('status', 'aktif')
                ->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('nis', 'like', '%'.$this->search.'%');
                })
                ->limit(5)
                ->get();
        }

        $selectedStudent = null;
        $unpaidInvoices = collect();
        $futureInvoices = collect();
        $paidInvoices = collect();

        if ($this->selectedStudentId) {
            $selectedStudent = Student::with(['schoolClass.academicYear'])->find($this->selectedStudentId);
            if ($selectedStudent) {
                $unpaidInvoices = $selectedStudent->invoices()
                    ->with('feeType')
                    ->where('status', 'unpaid')
                    ->orderBy('due_date', 'asc')
                    ->get();

                $futureInvoices = $selectedStudent->invoices()
                    ->with('feeType')
                    ->where('status', 'inactive')
                    ->orderBy('due_date', 'asc')
                    ->get();

                $paidInvoices = $selectedStudent->invoices()
                    ->with(['feeType', 'payments'])
                    ->where('status', 'paid')
                    ->orderBy('updated_at', 'desc')
                    ->limit(10)
                    ->get();
            }
        }

        return view('livewire.pages.finance.payment-manual', [
            'students' => $students,
            'selectedStudent' => $selectedStudent,
            'unpaidInvoices' => $unpaidInvoices,
            'futureInvoices' => $futureInvoices,
            'paidInvoices' => $paidInvoices,
        ])->title(__('Pembayaran Manual'));
    }
}
