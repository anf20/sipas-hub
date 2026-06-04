<?php

namespace App\Livewire\Pages\Admin;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class RecoveryCenter extends Component
{
    use WithPagination;

    #[Url]
    public string $tab = 'students';

    public function setTab(string $tab)
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function restoreStudent($id)
    {
        Student::onlyTrashed()->findOrFail($id)->restore();
        \Flux::toast(__('Data siswa berhasil dipulihkan.'), variant: 'success');
    }

    public function forceDeleteStudent($id)
    {
        Student::onlyTrashed()->findOrFail($id)->forceDelete();
        \Flux::toast(__('Data siswa dihapus permanen.'), variant: 'success');
    }

    public function restoreInvoice($id)
    {
        Invoice::onlyTrashed()->findOrFail($id)->restore();
        \Flux::toast(__('Tagihan berhasil dipulihkan.'), variant: 'success');
    }

    public function forceDeleteInvoice($id)
    {
        Invoice::onlyTrashed()->findOrFail($id)->forceDelete();
        \Flux::toast(__('Tagihan dihapus permanen.'), variant: 'success');
    }

    public function restorePayment($id)
    {
        Payment::onlyTrashed()->findOrFail($id)->restore();
        \Flux::toast(__('Pembayaran berhasil dipulihkan.'), variant: 'success');
    }

    public function forceDeletePayment($id)
    {
        Payment::onlyTrashed()->findOrFail($id)->forceDelete();
        \Flux::toast(__('Pembayaran dihapus permanen.'), variant: 'success');
    }

    public function render()
    {
        return view('livewire.pages.admin.recovery-center', [
            'trashedStudents' => Student::onlyTrashed()->with('schoolClass')->latest('deleted_at')->paginate(10, pageName: 'students-page'),
            'trashedInvoices' => Invoice::onlyTrashed()->with(['student', 'feeType'])->latest('deleted_at')->paginate(10, pageName: 'invoices-page'),
            'trashedPayments' => Payment::onlyTrashed()->with('invoice.student')->latest('deleted_at')->paginate(10, pageName: 'payments-page'),
        ])->title(__('Pusat Pemulihan Data'));
    }
}
