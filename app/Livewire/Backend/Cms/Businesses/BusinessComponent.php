<?php

namespace App\Livewire\Backend\Cms\Businesses;

use App\Models\Country;
use Livewire\Component;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Business;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Libraries\ImageManager;
use App\Livewire\Forms\BusinessForm;
use App\Livewire\Forms\PermissionForm;
use App\Models\Role;
use App\Models\User;
use App\Traits\WithMainModal;
use Spatie\Permission\Models\Permission;
use Livewire\WithFileUploads;
class BusinessComponent extends Component
{
    use WithMainModal,WithFileUploads;

    public $user,$countries, $roles, $logoImage, $limitPerPage = 15;
    public string $imagePath = 'images/business';
    public BusinessForm $form;
    protected $listeners = [
        'openCreateBusinessModal' => 'openModel',
    ];

    public function mount()
    {
        $this->countries = Country::get();
        $this->user = auth()->user();
    }
    private function getBusinesses(): LengthAwarePaginator
    {
        $query = Business::select('id', 'name', 'slug', 'logo', 'created_at');
        $data = $this->user->hasRole('super-admin')
            ? $query
            : $query->whereHas('roles', function($query) {
                    $query->whereIn('name', auth()->user()->roles->pluck('name')->toArray());
                });
        return $data->paginate($this->limitPerPage);
    }

    public function selectBusiness($name)
    {
        session(['business' => $name]);

        return redirect()->route('dashboard.home');
    }
    public function render()
    {
        $countries = $this->countries;
        $businesses = $this->getBusinesses();
        return view('livewire.backend.cms.businesses.business-component' ,compact('businesses'));
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
                'slug' => str::slug($validated['name']),
                'logo' => $validated['logo'] ? $this->imagePath . '/' . $validated['logo'] : null,
                'favicon' => $validated['favicon'] ? $this->imagePath . '/' . $validated['favicon'] : null,
                'address' => $validated['address'],
                'city' => $validated['city'],
                'country_id' => $validated['country_id'],
                'postal_code' => $validated['postal_code'],
                'invoice_prefix' => $validated['invoice_prefix'],
                'invoice_serial' => $validated['invoice_serial'],
                'date_format' => $validated['date_format'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);
            if($business)
            {
                $adminRole = new Role();
                $adminRole->name = 'admin';
                $adminRole->business_id = $business->id;
                $adminRole->title = 'Admin';
                $adminRole->save();

                $user = User::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'email_verified_at' => now(),
                    'password' => $validated['password'],
                    'business_id' => $business->id,
                    'is_active' => 1,
                ]);

                if ($adminRole) {
                    $excludedGroups = ['business', 'system','settings'];
                    $allPermissions = Permission::all();
                    $filteredPermissions = $allPermissions->filter(function($permission) use ($excludedGroups) {
                        return !in_array($permission->group, $excludedGroups);
                    });
                    $adminRole->permissions()->sync($filteredPermissions->pluck('id')->all());
                    DB::table('model_has_roles')->insert([
                        'role_id' => $adminRole->id,
                        'model_type' => User::class,
                        'model_id' => $user->id,
                    ]);
                }
            }

            DB::commit();

            if (!empty($this->form->logo))
                $this->form->logo = '';

            if (!empty($this->form->favicon))
                $this->form->favicon = '';

                $this->form->reset();
                $this->resetValidation();
                $this->closeMainModal();
                $this->dispatch('reinitialize-dispatcher');
                $this->dispatch('alert', ['type' => 'success', 'message' => 'Business created successfully.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while adding business: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Something went wrong.']);
        }
    }

    public function openModel()
    {
        $this->openMainModal();
    }
}
