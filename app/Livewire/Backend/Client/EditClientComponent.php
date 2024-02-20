<?php

namespace App\Livewire\Backend\Client;

use Exception;
use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use App\Models\Country;
use Livewire\Component;
use App\Models\Business;
use App\Models\Currency;
use Illuminate\Support\Str;
use App\Enums\User\UserStatus;
use Livewire\Attributes\Title;
use App\Enums\User\AccountType;
use App\Livewire\Forms\ClientForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

#[Title('Edit Client')]
class EditClientComponent extends Component
{
    public ClientForm $form;

    public $countries, $rateUnits, $client;

    public function mount($slug)
    {
        $business = Business::whereName(session('business'))->first();

        $this->client = Client::whereHas('business', function ($query) use ($business) {
            $query->whereId($business->id);
        })->whereSlug($slug)->first();

        $this->form->business_id = $business->id;
        $this->countries = Country::all();
        $this->rateUnits = Currency::get(['code']);
        $this->form->isUpdate = true;
        $this->form->id = $this->client->id;
        $this->form->set($this->client);
    }

    public function render()
    {
        $countries = $this->countries;
        $rateUnits = $this->rateUnits;
        return view('livewire.backend.client.edit-client-component', compact('countries', 'rateUnits'));
    }

    public function update($id)
    {
        $validated = $this->form->validate();

        try {
            DB::beginTransaction();
            $client = Client::whereHas('business', function ($query) use ($validated) {
                $query->whereId($validated['business_id']);
            })->findOrFail($id);

            $client->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'business_id' => $validated['business_id'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'country_id' => $validated['country_id'],
                'postal_code' => $validated['postal_code'],
                'rate_per_hour' => $validated['rate_per_hour'],
                'rate_unit' => $validated['rate_unit'],
                'note' => $validated['note']
            ]);

            if ($validated['add_user'] === '1')
                $this->addUser($client);

            DB::commit();
            $this->form->reset();
            session()->flash('success', 'Client updated successfully.');
            return redirect()->route('dashboard.clients.index');
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Got error while update client: ' . $exception);
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Something went wrong.']);
        }
    }

    private function addUser($client)
    {
        foreach ($this->form->first_name as $index => $first_name) {
            $userValidate = Validator::make([
                'email' => $this->form->email[$index]
            ], [
                'email' => 'required|email|unique:users'
            ]);

            if ($userValidate->fails()) {
                DB::rollBack();
                return $this->dispatch('alert', [
                    'type' => 'error',
                    'message' => 'Client email already taken.'
                ]);
            }

            $user = User::create([
                'first_name' => $first_name,
                'last_name' => $this->form->last_name[$index],
                'email' => $this->form->email[$index],
                'phone' => $this->form->phone[$index],
                'password' => Hash::make($this->form->password[$index]),
                'account_type' => AccountType::CLIENT->value,
                'client_id' => $client->id,
                'is_active' => UserStatus::ACTIVE->value
            ]);

            $clientRoleId = Role::whereName('client')->pluck('id')->toArray();
            $user->roles()->sync($clientRoleId);
        }
    }
}
