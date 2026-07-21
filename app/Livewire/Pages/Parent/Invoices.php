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

    public array $advanceCount = [];

    public string $paymentMethod = 'bca_va';

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->isSelectMode = false;
        $this->selectedInvoices = [];
        $this->advanceCount = [];
        $this->showConfirmationModal = false;
    }

    public function toggleSelectMode()
    {
        $this->isSelectMode = ! $this->isSelectMode;
        $this->selectedInvoices = [];
        $this->advanceCount = [];
        $this->showConfirmationModal = false;
    }

    public function incrementAdvance($studentId)
    {
        $inactiveInvoices = Invoice::where('student_id', $studentId)
            ->where('status', 'inactive')
            ->orderBy('due_date', 'asc')
            ->get();
            
        $currentCount = $this->advanceCount[$studentId] ?? 0;
        
        if ($currentCount < $inactiveInvoices->count()) {
            $this->advanceCount[$studentId] = $currentCount + 1;
            $invoiceToAdd = $inactiveInvoices[$currentCount];
            
            if (!in_array((string)$invoiceToAdd->id, $this->selectedInvoices)) {
                $this->selectedInvoices[] = (string)$invoiceToAdd->id;
            }

            // Auto-check all unpaid invoices for this student
            $unpaidInvoices = Invoice::where('student_id', $studentId)
                ->where('status', 'unpaid')
                ->pluck('id');
                
            foreach ($unpaidInvoices as $unpaidId) {
                if (!in_array((string)$unpaidId, $this->selectedInvoices)) {
                    $this->selectedInvoices[] = (string)$unpaidId;
                }
            }
        }
    }

    public function decrementAdvance($studentId)
    {
        $inactiveInvoices = Invoice::where('student_id', $studentId)
            ->where('status', 'inactive')
            ->orderBy('due_date', 'asc')
            ->get();
            
        $currentCount = $this->advanceCount[$studentId] ?? 0;
        
        if ($currentCount > 0) {
            $invoiceToRemove = $inactiveInvoices[$currentCount - 1];
            
            $this->selectedInvoices = array_values(array_filter($this->selectedInvoices, fn($id) => (string)$id !== (string)$invoiceToRemove->id));
            
            $this->advanceCount[$studentId] = $currentCount - 1;
        }
    }

    public function updatedSelectedInvoices()
    {
        foreach ($this->advanceCount as $studentId => $count) {
            if ($count > 0) {
                $unpaidInvoices = Invoice::where('student_id', $studentId)
                    ->where('status', 'unpaid')
                    ->pluck('id')
                    ->map(fn($id) => (string)$id)
                    ->toArray();
                
                // If the user unchecks an unpaid invoice while advance is active
                if (count(array_diff($unpaidInvoices, $this->selectedInvoices)) > 0) {
                    // Reset advance count
                    $this->advanceCount[$studentId] = 0;
                    
                    // Remove all inactive invoices of this student from selectedInvoices
                    $inactiveIds = Invoice::where('student_id', $studentId)
                        ->where('status', 'inactive')
                        ->pluck('id')
                        ->map(fn($id) => (string)$id)
                        ->toArray();
                        
                    $this->selectedInvoices = array_values(array_diff($this->selectedInvoices, $inactiveIds));
                    
                    \Flux::toast(__('Sistem membatalkan tagihan bulan depan karena Anda menghapus pilihan pada tagihan bulan sebelumnya.'), variant: 'warning');
                }
            }
        }
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
            ->whereIn('status', ['unpaid', 'inactive'])
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
            'invoices' => $invoices,
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
