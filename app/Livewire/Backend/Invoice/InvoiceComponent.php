<?php

namespace App\Livewire\Backend\Invoice;

use App\Models\Invoice;
use App\Traits\WithMainModal;
use Livewire\Component;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Title('Invoices')]
class InvoiceComponent extends Component
{
    use WithPagination, WithMainModal;

    public $business_id;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public string $dataCountType = 'total'; // Default user type

    public int $limitPerPage = 20;

    private function getInvoiceQuery()
    {
        $this->search ? $this->resetPage() : ''; // reset pagination while searching

        return Invoice::whereHas('project', function ($query) {
            $query->sessionBusiness();
        })->getList($this->search, $this->columnName, $this->sortDirection);
    }

    private function getTotalInvoices(): LengthAwarePaginator
    {
        return $this->getInvoiceQuery()->withTrashed()->paginate($this->limitPerPage);
    }

    private function getActiveInvoices(): LengthAwarePaginator
    {
        return $this->getInvoiceQuery()->paginate($this->limitPerPage);
    }

    private function getArchivedInvoices(): LengthAwarePaginator
    {
        return $this->getInvoiceQuery()->onlyTrashed()->paginate($this->limitPerPage);
    }

    private function getInvoices(): LengthAwarePaginator
    {
        if ($this->dataCountType === 'active') {
            return $this->getActiveInvoices();
        } elseif ($this->dataCountType === 'archived') {
            return $this->getArchivedInvoices();
        } else {
            return $this->getTotalInvoices();
        }
    }

    public function render()
    {
        $invoices = $this->getInvoices();
        $totalInvoices = Invoice::withTrashed()->count();
        $activeInvoices = Invoice::count();
        $archivedInvoices = Invoice::onlyTrashed()->count();
        $this->dispatch('reinitialize-icons');
        return view('livewire.backend.invoice.invoice-component', compact('invoices', 'totalInvoices', 'activeInvoices', 'archivedInvoices'));
    }
}
