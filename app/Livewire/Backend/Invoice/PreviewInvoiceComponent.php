<?php

namespace App\Livewire\Backend\Invoice;

use App\Models\Business;
use App\Models\Project;
use Livewire\Component;
use Illuminate\Http\Request;

class PreviewInvoiceComponent extends Component
{
    public $data = [];
    public $client = [];
    public $invoice_number;
    public $project;
    public function mount(Request $request)
    {
        foreach ($request->data['task'] as $data) {
            if (array_key_exists('time', $data)) {
                $this->data = $request->data;
            }
        }


        $this->invoice_number = $request->invoice_number;
        $project = Project::where('id', $request->data['project_id'])->with('client')->first();
        $this->project = $project;
        $this->client[] = $project?->client?->name;
        $this->client[] =  $project?->client?->address;
    }
    public function render()
    {
        $business = Business::findOrFail(session('business_details.id'));
        return view('livewire.backend.invoice.preview-invoice-component')->with([
            'data' => $this->data,
            'business' => $business,
        ]);
    }
}
