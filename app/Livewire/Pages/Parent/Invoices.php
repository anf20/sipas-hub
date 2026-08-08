<?php

namespace App\Livewire\Pages\Parent;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.parent')]
class Invoices extends Component
{
    use WithFileUploads;

    public $filter = 'all';

    public $proofFile;

    public bool $isSelectMode = false;

    public bool $showConfirmationModal = false;

    public bool $showManualTransferModal = false;

    public array $selectedInvoices = [];

    public array $advanceCount = [];

    public string $paymentMethod = 'manual_transfer';

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

            if (! in_array((string) $invoiceToAdd->id, $this->selectedInvoices)) {
                $this->selectedInvoices[] = (string) $invoiceToAdd->id;
            }

            // Auto-check all unpaid invoices for this student
            $unpaidInvoices = Invoice::where('student_id', $studentId)
                ->where('status', 'unpaid')
                ->pluck('id');

            foreach ($unpaidInvoices as $unpaidId) {
                if (! in_array((string) $unpaidId, $this->selectedInvoices)) {
                    $this->selectedInvoices[] = (string) $unpaidId;
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

            $this->selectedInvoices = array_values(array_filter($this->selectedInvoices, fn ($id) => (string) $id !== (string) $invoiceToRemove->id));

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
                    ->map(fn ($id) => (string) $id)
                    ->toArray();

                // If the user unchecks an unpaid invoice while advance is active
                if (count(array_diff($unpaidInvoices, $this->selectedInvoices)) > 0) {
                    // Reset advance count
                    $this->advanceCount[$studentId] = 0;

                    // Remove all inactive invoices of this student from selectedInvoices
                    $inactiveIds = Invoice::where('student_id', $studentId)
                        ->where('status', 'inactive')
                        ->pluck('id')
                        ->map(fn ($id) => (string) $id)
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

    public function startPayment()
    {
        $invoices = Invoice::whereIn('id', $this->selectedInvoices)
            ->whereIn('status', ['unpaid', 'inactive'])
            ->get();

        if ($invoices->isEmpty()) {
            \Flux::toast(__('Pilih minimal satu tagihan untuk dibayar.'), variant: 'warning');

            return;
        }

        if ($this->paymentMethod === 'manual_transfer') {
            $this->showConfirmationModal = false;
            $this->showManualTransferModal = true;
        } else {
            $this->paySelected();
        }
    }

    public function paySelected()
    {
        $invoices = Invoice::whereIn('id', $this->selectedInvoices)
            ->whereIn('status', ['unpaid', 'inactive'])
            ->get();

        if ($invoices->isEmpty()) {
            return;
        }

        if ($this->paymentMethod === 'manual_transfer') {
            $this->validate([
                'proofFile' => 'required|image|max:2048',
            ]);

            try {
                DB::beginTransaction();

                $invoices = Invoice::with(['student.schoolClass'])
                    ->whereIn('id', $this->selectedInvoices)
                    ->whereIn('status', ['unpaid', 'inactive'])
                    ->lockForUpdate()
                    ->get();

                if ($invoices->isEmpty()) {
                    return;
                }

                // Store file once (with WebP compression)
                $path = $this->convertToWebp($this->proofFile);

                $prefix = 'SCH-PEND-'.date('Ym').'-';

                // Get the starting sequence
                $lastPayment = Payment::where('receipt_number', 'like', $prefix.'%')
                    ->orderBy('id', 'desc')
                    ->first();
                $sequence = 1;
                if ($lastPayment) {
                    $lastSequence = (int) substr($lastPayment->receipt_number, -4);
                    $sequence = $lastSequence + 1;
                }

                $invoiceDetails = [];
                $totalAmount = 0;

                foreach ($invoices as $invoice) {
                    $receiptNumber = $prefix.str_pad($sequence, 4, '0', STR_PAD_LEFT);
                    $sequence++;

                    // Create Payment Record
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'amount' => $invoice->amount,
                        'method' => 'transfer',
                        'status' => 'pending',
                        'proof_file' => $path,
                        'paid_at' => now(),
                        'receipt_number' => $receiptNumber,
                    ]);

                    // Update Invoice Status
                    $invoice->update([
                        'status' => 'pending',
                    ]);

                    $invoiceDetails[] = "- Siswa: {$invoice->student->name} | Tagihan: {$invoice->billing_detail} | Nominal: Rp ".number_format($invoice->amount, 0, ',', '.');
                    $totalAmount += $invoice->amount;
                }

                DB::commit();

                // Format WhatsApp message
                $adminNumber = config('services.whatsapp.admin_number', '6281234567890');
                $message = "Halo Admin Keuangan, saya telah melakukan transfer bank untuk pembayaran beberapa tagihan berikut:\n".
                           implode("\n", $invoiceDetails)."\n".
                           '- Total Bayar: Rp '.number_format($totalAmount, 0, ',', '.')."\n\n".
                           'Saya telah mengunggah bukti pembayaran di Portal SIPAS-Hub. Mohon bantuannya untuk memverifikasi transaksi ini. Terima kasih.';

                $waUrl = "https://wa.me/{$adminNumber}?text=".rawurlencode($message);

                $this->showConfirmationModal = false;
                $this->showManualTransferModal = false;
                $this->selectedInvoices = [];
                $this->isSelectMode = false;
                $this->reset('proofFile');

                \Flux::toast(__('Bukti transfer berhasil diunggah. Menghubungi admin via WhatsApp...'), variant: 'success');

                return redirect()->away($waUrl);

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Manual Transfer Bulk Error: '.$e->getMessage());
                \Flux::toast(__('Gagal mengunggah bukti transfer. Silakan coba lagi.'), variant: 'danger');

                return;
            }
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
            $query->whereIn('status', ['unpaid', 'pending']);
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

    /**
     * Compress and convert uploaded image to WebP format.
     * Falls back to normal upload if GD extension is not available.
     */
    private function convertToWebp($uploadedFile): string
    {
        try {
            if (function_exists('imagewebp')) {
                $path = $uploadedFile->getRealPath();

                // Determine image type and create image resource
                $image = match (strtolower($uploadedFile->getClientOriginalExtension())) {
                    'jpg', 'jpeg' => @imagecreatefromjpeg($path),
                    'png' => @imagecreatefrompng($path),
                    'webp' => @imagecreatefromwebp($path),
                    'gif' => @imagecreatefromgif($path),
                    default => false,
                };

                if ($image !== false) {
                    // Preserve transparency for PNG
                    if (strtolower($uploadedFile->getClientOriginalExtension()) === 'png') {
                        imagealphablending($image, false);
                        imagesavealpha($image, true);
                    }

                    // Capture WebP output using output buffering
                    ob_start();
                    if (imagewebp($image, null, 75)) {
                        $webpData = ob_get_clean();
                        imagedestroy($image);

                        $filename = 'payment-proofs/'.uniqid().'.webp';
                        Storage::disk('public')->put($filename, $webpData);

                        return $filename;
                    }
                    ob_end_clean();
                    imagedestroy($image);
                }
            }
        } catch (\Exception $e) {
            \Log::error('WebP Compression Failed, falling back: '.$e->getMessage());
        }

        // Fallback to standard Laravel store
        return $uploadedFile->store('payment-proofs', 'public');
    }
}
