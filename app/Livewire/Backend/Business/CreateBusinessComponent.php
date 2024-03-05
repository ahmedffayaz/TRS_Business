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
    public $businesses, $countries, $user, $roles, $logoImage;
    public string $imagePath = 'images/business';

    public function mount()
    {
        $this->user = auth()->user();
        $this->businesses = Business::pluck('name', 'id')->all();
        $this->countries = Country::get();
        $this->roles = Role::all();
        $this->logoImage = 'assets/images/avatar.png';
    }

    public function store()
    {
        $validated = $this->form->validate();

        try {
            DB::beginTransaction();

            if (!empty($validated['logo'])) {
                $imageManager = new ImageManager();
                $validated['logo'] = $imageManager->setFile($validated['logo'])->resize(64)->setDirectory($this->imagePath)->save();
            }

            if (!empty($validated['favicon'])) {
                $imageManager = new ImageManager();
                $validated['favicon'] = $imageManager->setFile($validated['favicon'])->resize(64)->setDirectory($this->imagePath)->save();
            }

            $business = Business::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'logo' => $validated['logo'] ? $this->imagePath . '/' . $validated['logo'] : null,
                'favicon' => $validated['favicon'] ? $this->imagePath . '/' . $validated['favicon'] : null,
                'address' => $validated['address'],
                'city' => $validated['city'],
                'country_id' => $validated['country_id'],
                'postal_code' => $validated['postal_code'],
                'invoice_prefix' => $validated['invoice_prefix'],
                'invoice_serial' => $validated['invoice_serial'],
                'date_format' => $validated['date_format']
            ]);

            $business->roles()->attach($validated['roles']);

            DB::commit();

            // Reset form logo field
            if (!empty($this->form->logo))
                $this->form->logo = '';

            // Reset form favicon field
            if (!empty($this->form->favicon))
                $this->form->favicon = '';

            session()->flash('success', 'Business added successfully.');
            return redirect()->route('dashboard.businesses.create');
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while adding business: ' . $exception->getMessage());
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
