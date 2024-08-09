<?php

namespace App\Livewire\Backend;

use App\Models\Project;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Livewire\Attributes\On;

class DashboardComponent extends Component
{
    public $show_swl = false;
    public $encryption;
    public function mount()
    {
        $this->encryption = session('swl_key');
        session()->forget('swl_key');
        if($this->encryption){
            $this->show_swl = true;
        }
    }

    #[Title('Dashboard')]
    public function render()
    {
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
}
