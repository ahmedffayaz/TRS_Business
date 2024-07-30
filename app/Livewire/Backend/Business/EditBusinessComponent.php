<?php

namespace App\Livewire\Backend\Business;

use Exception;
use App\Models\Role;
use App\Models\Country;
use Livewire\Component;
use App\Models\Business;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use App\Libraries\ImageManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Livewire\Forms\BusinessForm;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\WithFileUploads;

#[Title('Edit Business')]
class EditBusinessComponent extends Component
{
    use WithFileUploads;

    public BusinessForm $form;
    public $isUpdate, $countries, $slug, $user, $roles, $logoImage, $businessLogo, $businessFavicon;
    public string $imagePath = 'images/business';

    public function mount($slug)
    {
        $this->user = auth()->user();
        if ($slug === nameToSlug(session('business'))) {
            $this->slug = Business::whereSlug($slug)->firstOrFail();
            $this->businessLogo = $this->slug->logo;
            $this->businessFavicon = $this->slug->favicon;
        }
        else
            abort(404);
        $this->countries = Country::all();
        $this->roles = Role::all();
        $this->form->isUpdate = true;
        $this->logoImage = 'assets/images/select-logo.png';
        $this->form->set($this->slug);
    }

    public function render()
    {
        $form = $this->form;
        $roles = $this->roles;
        return view('livewire.backend.business.edit-business-component', compact('form', 'roles'));
    }

    public function update($id)
    {
        $validated = $this->form->validate();

        try {
            DB::beginTransaction();
            $business = Business::findOrFail($id);

            if (!empty($validated['logo'])) {
                $imageManager = new ImageManager();
                $validated['logo'] = $imageManager->setFile($validated['logo'])->resize(64)->setDirectory("images/business")->save();
            }

            if (!empty($validated['favicon'])) {
                $imageManager = new ImageManager();
                $validated['favicon'] = $imageManager->setFile($validated['favicon'])->resize(64)->setDirectory($this->imagePath)->save();
            }

            $business->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'logo' => $validated['logo'] ? $this->imagePath . '/' . $validated['logo'] : $business->logo,
                'favicon' => $validated['favicon'] ? $this->imagePath . '/' . $validated['favicon'] : $business->favicon,
                'address' => $validated['address'],
                'city' => $validated['city'],
                'country_id' => $validated['country_id'],
                'postal_code' => $validated['postal_code'],
                'invoice_prefix' => $validated['invoice_prefix'],
                'invoice_serial' => $validated['invoice_serial'],
                'date_format' => $validated['date_format']
            ]);
            DB::commit();

            // Reset form logo field
            if (!empty($this->form->logo))
                $this->form->logo = '';

            // Reset form favicon field
            if (!empty($this->form->favicon))
                $this->form->favicon = '';

            // Reset form fields
            $this->resetValidation();
            session()->forget('business');
            session(['business' => $business->name]);
            session()->flash('success', 'Business updated successfully.');
            redirect()->route('dashboard.businesses.edit', $business->slug);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while update business data: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Something went wrong.']);
        }
    }
}
