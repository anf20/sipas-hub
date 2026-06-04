<?php

namespace App\Livewire\Pages\Parent;

use App\Models\Invoice;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.parent')]
class Invoices extends Component
{
    public $filter = 'all';

    public bool $isSelectMode = false;

    public bool $showConfirmationModal = false;

    public array $selectedInvoices = [];

    public string $paymentMethod = 'bca_va';

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->isSelectMode = false;
        $this->selectedInvoices = [];
        $this->showConfirmationModal = false;
    }

    public function toggleSelectMode()
    {
        $this->isSelectMode = ! $this->isSelectMode;
        $this->selectedInvoices = [];
        $this->showConfirmationModal = false;
    }

    public function initiatePayment()
    {
        if (empty($this->selectedInvoices)) {
            \Flux::toast(__('Pilih minimal satu tagihan.'), variant: 'warning');

            return;
        }

        $this->showConfirmationModal = true;
    }

    public function paySelected()
    {
        $invoices = Invoice::whereIn('id', $this->selectedInvoices)
            ->where('status', 'unpaid')
            ->get();

        if ($invoices->isEmpty()) {
            return;
        }

        try {
            $midtransService = app(MidtransService::class);
            $snapToken = $midtransService->getBulkSnapToken($invoices, Auth::user(), $this->paymentMethod);

            $this->showConfirmationModal = false;
            $this->dispatch('show-snap-popup', snapToken: $snapToken);
        } catch (\Exception $e) {
            \Log::error('Bulk Midtrans Error: '.$e->getMessage());
            \Flux::toast(__('Gagal memulai pembayaran massal.'), variant: 'danger');
        }
    }

    public function render()
    {
        $user = Auth::user();
        $students = $user->students()->with(['schoolClass'])->get();
        $studentIds = $students->pluck('id');

        $query = Invoice::with(['student', 'feeType'])
            ->whereIn('student_id', $studentIds);

        if ($this->filter === 'unpaid') {
            $query->where('status', 'unpaid');
        } elseif ($this->filter === 'paid') {
            $query->where('status', 'paid');
        }

        $invoices = $query->orderBy('due_date', 'desc')->get();

        $totalUnpaidBalance = Invoice::whereIn('student_id', $studentIds)
            ->where('status', 'unpaid')
            ->sum('amount');

        $selectedInvoicesData = Invoice::with(['student', 'feeType'])
            ->whereIn('id', $this->selectedInvoices)
            ->get();

        $invoicesTotal = $selectedInvoicesData->sum('amount');

        $midtransService = app(MidtransService::class);
        $serviceFee = $midtransService->calculateFee((float) $invoicesTotal, $this->paymentMethod);
        $totalToPay = $invoicesTotal + $serviceFee;

        $unpaidCount = Invoice::whereIn('student_id', $studentIds)
            ->where('status', 'unpaid')
            ->count();

        // Group invoices by student name
        $groupedInvoices = $invoices->groupBy(fn ($invoice) => $invoice->student->name);

        return view('livewire.pages.parent.invoices', [
            'groupedInvoices' => $groupedInvoices,
            'totalUnpaidBalance' => $totalUnpaidBalance,
            'invoicesTotal' => $invoicesTotal,
            'serviceFee' => $serviceFee,
            'totalToPay' => $totalToPay,
            'selectedInvoicesData' => $selectedInvoicesData,
            'unpaidCount' => $unpaidCount,
        ])->title(__('Tagihan Anak'));
    }
}
