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
    public $isUpdate, $countries, $slug, $user, $roles;

    public function mount($slug)
    {
        $this->user = auth()->user();
        if ($slug === nameToSlug(session('business')))
            $this->slug = Business::whereSlug($slug)->firstOrFail();
        else
            abort(404);
        $this->countries = Country::all();
        $this->roles = Role::all();
        $this->form->isUpdate = true;
        $this->form->roles = $this->slug->roles->pluck('id')->toArray();
        $this->dispatch('roles-select', ['formRoles' => $this->form->roles]);
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

            $business->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'logo' => !empty($validated['logo']) ? $validated['logo'] : $business->logo,
                'address' => $validated['address'],
                'city' => $validated['city'],
                'country_id' => $validated['country_id'],
                'postal_code' => $validated['postal_code'],
                'invoice_prefix' => $validated['invoice_prefix'],
                'invoice_serial' => $validated['invoice_serial']
            ]);
            $business->roles()->sync($validated['roles']);
            DB::commit();

            // Reset form logo field
            if (!empty($this->form->logo))
                $this->form->logo = '';

            $this->dispatch('alert', ['type' => 'success',  'message' => 'Business updated successfully!']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Something went wrong.']);
        }
    }
}
