<?php

namespace App\Livewire\Backend\Email;

use Livewire\Component;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;

class EmailSettingComponent extends Component
{
    public $showForm = false;
    public $email_driver;
    public $host;
    public $port;
    public $username;
    public $password;
    public $encryption;
    public $from_address;
    public $from_name;

    protected $rules = [
        'email_driver' => 'required|string',
        'host' => 'required|string',
        'port' => 'required|integer',
        'username' => 'required|string',
        'password' => 'required|string',
        'encryption' => 'required|string',
        'from_address' => 'required|email',
        'from_name' => 'required|string',
    ];

    public function mount()
    {
        $business_id = Auth::user()->business_id;
        $business = Business::find($business_id);

        if ($business && $business->email_settings) {
            $settings = json_decode($business->email_settings, true);
            $this->email_driver = $settings['mail_driver'];
            $this->host = $settings['mail_host'];
            $this->port = $settings['mail_port'];
            $this->username = $settings['mail_username'];
            $this->password = $settings['mail_password'] ;
            $this->encryption = $settings['mail_encryption'];
            $this->from_address = $settings['mail_from_address'];
            $this->from_name = $settings['mail_from_name'];
            $this->showForm = false;
        } else {
            $this->showForm = false;
        }
    }

    public function toggleForm()
    {
        $this->showForm != $this->showForm;
    }

    public function submit()
    {
        $this->validate();

        $business_id = Auth::user()->business_id;
        $business = Business::findOrFail($business_id);

        $settings = [
            'mail_driver' => $this->email_driver,
            'mail_host' => $this->host,
            'mail_port' => $this->port,
            'mail_username' => $this->username,
            'mail_password' => $this->password,
            'mail_encryption' => $this->encryption,
            'mail_from_address' => $this->from_address,
            'mail_from_name' => $this->from_name,
        ];

        $business->email_settings = json_encode($settings);
        $business->save();

        session()->flash('success', 'Settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.backend.email.email-setting-component');
    }
}
