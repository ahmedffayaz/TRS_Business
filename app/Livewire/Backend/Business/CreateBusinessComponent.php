<?php

namespace App\Livewire\Backend\Business;

use Exception;
use App\Models\Role;
use App\Models\Country;
use Livewire\Component;
use App\Models\Business;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use App\Libraries\ImageManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Livewire\Forms\BusinessForm;

#[Title('Add Business')]
class CreateBusinessComponent extends Component
{
    use WithFileUploads;

    public BusinessForm $form;
    public $businesses, $countries, $user, $roles;

    public function mount()
    {
        $this->user = auth()->user();
        $this->businesses = Business::pluck('name', 'id')->all();
        $this->countries = Country::get();
        $this->roles = Role::all();
    }

    public function store()
    {
        $validated = $this->form->validate();

        try {
            DB::beginTransaction();

            if (!empty($validated['logo'])) {
                $imageManager = new ImageManager();
                $validated['logo'] = $imageManager->setFile($validated['logo'])->resize(64)->setDirectory("images/business")->save();
            }

            $business = Business::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'logo' => $validated['logo'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'country_id' => $validated['country_id'],
                'postal_code' => $validated['postal_code'],
                'invoice_prefix' => $validated['invoice_prefix'],
                'invoice_serial' => $validated['invoice_serial']
            ]);

            $business->roles()->attach($validated['roles']);

            DB::commit();

            // Reset form fields
            $this->form->reset();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Business added successfully!']);
            session()->flash('error', 'Business added successfully.');
            return redirect()->route('dashboard');
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Something went wrong.']);
        }
    }

    public function render()
    {
        $businesses = $this->businesses;
        $countries = $this->countries;
        $roles = $this->roles;
        return view('livewire.backend.business.create-business-component', compact('businesses', 'countries', 'roles'));
    }
}
