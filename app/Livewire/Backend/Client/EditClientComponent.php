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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;

#[Title('Edit Client')]
class EditClientComponent extends Component
{
    public ClientForm $form;

    public $countries, $rateUnits, $client, $inputs, $i;

    public function mount($slug)
    {
        $business = Business::whereName(session('business'))->first();

        $this->client = Client::whereHas('business', function ($query) use ($business) {
            $query->whereId($business->id);
        })->whereSlug($slug)->firstOrFail();

        $this->form->business_id = $business->id;
        $this->countries = Country::all();
        $this->rateUnits = Currency::get(['code']);
        $this->form->isUpdate = true;
        $this->form->id = $this->client->id;
        $this->form->set($this->client);

        $this->inputs = [];
        $this->i = 1;
    }

    public function addUserFields($i)
    {
        $this->i = $i + 1;
        array_push($this->inputs, 1);
        $this->dispatch('feather-icons');
    }

    public function removeUserFields($key)
    {
        unset($this->inputs[$key]);
    }

    public function render()
    {
        $countries = $this->countries;
        $rateUnits = $this->rateUnits;
        $this->dispatch('reinitialize-icons');
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

            if ($validated['add_user'] === '1') {
                $this->addUser($client,$validated['business_id']);
            }

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

    private function addUser($client,$business_id)
    {
        if (!empty($this->form->first_name) && !empty($this->form->email) && !empty($this->form->last_name)
        && !empty($this->form->phone)) {

            foreach ($this->form->first_name as $index => $first_name) {
                $userValidate = Validator::make([
                    'email' => $this->form->email[$index]
                ], [
                    'email' => 'required|email|unique:users'
                ]);

                if ($userValidate->fails()) {
                    $this->dispatch('alert', [
                        'type' => 'error',
                        'message' => $this->form->email[$index] . ' email already exist.'
                    ]);
                }

                isset($this->form->send_email[$index]) && $this->form->send_email[$index] == true ? $is_active = UserStatus::ACTIVE->value : $is_active = UserStatus::INACTIVE->value;
                $user = User::create([
                    'first_name' => $first_name,
                    'last_name' => $this->form->last_name[$index],
                    'email' => $this->form->email[$index],
                    'phone' => $this->form->phone[$index],
                    'password' =>  '*&^%$#@!~~!@#$%^&*',
                    'account_type' => AccountType::CLIENT->value,
                    'client_id' => $client->id,
                    'is_active' => $is_active,
                ]);

                $clientRoleId = Role::whereBusinessId($business_id)->whereName('client')->pluck('id')->toArray();
                $user->roles()->sync($clientRoleId);

                if(isset($this->form->send_email[$index]) && $this->form->send_email[$index] == true){
                    $broker = Password::broker();
                    $broker->sendResetLink(['email' => $this->form->email[$index]]);
                }
    
            }
        } else {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'User input fields are required.'
            ]);
        }
    }
}
