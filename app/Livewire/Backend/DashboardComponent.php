<?php

namespace App\Livewire\Backend;

use App\Models\Task;
use App\Models\User;
use App\Models\Client;
use App\Models\Country;
use App\Models\Project;
use Livewire\Component;
use App\Models\Business;
use Livewire\Attributes\On;
use App\Traits\WithMainModal;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class DashboardComponent extends Component
{
    use WithMainModal;
    public $show_swl = false;
    public $encryption;
    public $userCount = 0;
    public $projectCount = 0;
    public $taskCount = 0;
    public $clientDetail = 0;
    protected $user;
    public $clientCount = 0;
    public $countries;
    public $tasks;
    public $projects;
    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';

    public string $taskSearch = '';
    public string $taskColumnName = 'created_at';
    public string $taskSortDirection = 'desc';
    public $form = [
        'clientName' => '',
        'clientContryId' => '',
        'clientCity' => '',
        'clientPostalCode' => '',
        'clientAddress' => '',
    ];
    protected $rules = [
        'form.clientName' => 'required|string|max:255',
        'form.clientContryId' => 'required|exists:countries,id',
        'form.clientCity' => 'required|string|max:255',
        'form.clientPostalCode' => 'nullable|numeric',
        'form.clientAddress' => 'required|string|max:255',
    ];
    public function mount()
    {
        $this->encryption = session('swl_key');
        session()->forget('swl_key');

        if ($this->encryption) {
            $decrypted_key = Crypt::decrypt($this->encryption);
            $project = Project::where('slug', $decrypted_key['slug'])->firstOrFail();
            $user = Auth::user();
            $is_member = $project->members->contains($user->id);
            if (!$is_member) {
                $this->show_swl = true;
            } else {
                $this->show_swl = false;
                session()->flash('status', 'You are already a member of this project.');
                return Redirect::route('dashboard.projects.index');
            }
        }
        $this->user = auth()->user();
        $this->userCount = $this->getUserCount();
        $this->projectCount = $this->getProjectCount();
        $this->taskCount = $this->getTaskCount();
        $this->clientDetail = $this->getClientDetail();
        $this->clientCount = $this->getClientCount();
        $this->tasks = $this->getTasks();

        if ($this->clientDetail) {
            $this->form = [
                'clientName' => $this->clientDetail->name,
                'clientContryId' => $this->clientDetail->country->id ?? null,
                'clientCity' => $this->clientDetail->city,
                'clientPostalCode' => $this->clientDetail->postal_code,
                'clientAddress' => $this->clientDetail->address,
            ];
        }
    }

    #[Title('Dashboard')]
    public function render()
    {
        $this->dispatch('reinitialize-icons');
        $this->dispatch('select-container');
        $this->tasks = $this->getTasks();
        $this->projects = $this->getProjects();
        return view('livewire.backend.dashboard-component');
    }

    #[On('reject_invitation_dashboard')]
    public function rejectInviteLink()
    {
        session()->flash('error', 'You rejected this invitation.');
        return redirect()->route('dashboard.projects.index');
    }

    #[On('accept_invitation_dashboard')]
    public function acceptInvitation()
    {
        try {
            $decryption = Crypt::decrypt($this->encryption);
            if (Auth::check()) {
                $user = Auth::user();
                $project = Project::where('client_id', $decryption['client_id'])
                    ->where('business_id', $decryption['business_id'])
                    ->where('slug', $decryption['slug'])->with('members')->firstOrFail();

                $is_member = $project->members->contains($user->id);

                if (!$is_member) {
                    $project->members()->attach($user->id);
                    session()->flash('status', 'Now you are member of project.');
                    return redirect()->route('dashboard.projects.index');
                } else {
                    session()->flash('status', 'You are already a member of this project.');
                    return redirect()->route('dashboard.projects.index');
                }
            }
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            session()->flash('error', 'Invalid data. Please try again.');
        }
    }

    public function getUserCount()
    {


        if ($this->user->hasRole('client')) {

            return User::where('client_id', $this->user->client_id)->statusActive()->whereHas('roles', function ($query) {
                $query->where('name', '=', 'client');
            })->count();

        } elseif ($this->user->hasRole('admin')) {

            return User::where('business_id', $this->user->business_id)->statusActive()->whereHas('roles', function ($query) {
                $query->where('name', '!=', 'client');
            })->count();
        }

        return 0;
    }

    public function getProjectCount()
    {
        if ($this->user->hasRole('client')) {

            return Project::where('client_id', $this->user->client_id)->where('business_id', $this->user->business_id)->count();
        } elseif ($this->user->hasRole('admin')) {

            return Project::where('business_id', $this->user->business_id)->count();
        }
    }

    public function getTaskCount()
    {
        if ($this->user->hasRole('client')) {

            return Project::where('client_id', $this->user->client_id)
                ->withCount('tasks')->get()->sum('tasks_count');
        } elseif ($this->user->hasRole('admin')) {

            return Project::where('business_id', $this->user->business_id)
                ->withCount('tasks')->get()->sum('tasks_count');
        }
    }
    public function getClientCount()
    {
        if ($this->user->hasRole('client')) {
            return 0;
        } elseif ($this->user->hasRole('admin')) {

            return Client::whereHas('business', function ($query) {
                $query->whereName(session('business'));
            })->count();
        }
    }

    public function getClientDetail()
    {
        if ($this->user->hasRole('client')) {
            return Client::sessionBusiness()->whereId($this->user->client_id)
                ->with(['employees', 'business', 'country'])->firstOrFail();
        }
        return null;
    }

    public function editClient($id)
    {
        $this->countries = Country::select('id', 'name')->orderBy('name', 'asc')->get();
        $this->openMainModal();
        $this->dispatch('reinitialize-icons');
    }

    public function updateClientDetail()
    {
        $this->validate([
            'form.clientName' => 'required|string|max:255',
            'form.clientContryId' => 'required|exists:countries,id',
            'form.clientCity' => 'required|string|max:255',
            'form.clientPostalCode' => 'nullable|numeric',
            'form.clientAddress' => 'required|string|max:255',
        ]);
        if ($this->clientDetail) {
            // Update the client details
            $this->clientDetail->update([
                'name' => $this->form['clientName'],
                'country_id' => $this->form['clientContryId'],
                'city' => $this->form['clientCity'],
                'postal_code' => $this->form['clientPostalCode'],
                'address' => $this->form['clientAddress'],
            ]);
            $this->dispatch('close-main-modal');
            session()->flash('status', 'Client information updated successfully.');

        }
    }

    public function closeMainModal()
    {
        $this->dispatch('close-main-modal');
    }

    public function getTasks()
    {
        $user = auth()->user();
        if ($user->hasRole('client')) {

            return Task::whereHas('project', function ($query) use ($user) {
                $query->where('business_id', $user->business_id)
                    ->where('client_id', $user->client_id);
            })->with(['project', 'comments'])
                ->whereNull('completed_at')
                ->whereBetween('end_date', [now()->subDays(15)->format('Y-m-d'), now()->addDays(15)->format('Y-m-d')])
                ->getList($this->taskSearch, $this->taskColumnName, $this->taskSortDirection)
                ->latest()->take(4)->get();
        }
        return null;
    }

    // #[On('get-projects')]
    public function getProjects()
    {
        $user = auth()->user();
        if ($user->hasRole('client')) {
            return Project::sessionBusiness()->when(!$user->hasRole('super-admin'), function ($query) use ($user) {
                $query->whereHas('client', function ($query) use ($user) {
                    $query->where('id', $user->client_id);
                });
            })->with(['client', 'members', 'tasks'])->whereIn('status', ['in-progress', 'pending'])
                ->whereBetween('end_date', [now()->subDays(15)->format('Y-m-d'), now()->addDays(15)->format('Y-m-d')])

                ->getList($this->search, $this->columnName, $this->sortDirection)->withCount(['members', 'tasks'])->take(6)->get();
        }
        return null;
    }
}
