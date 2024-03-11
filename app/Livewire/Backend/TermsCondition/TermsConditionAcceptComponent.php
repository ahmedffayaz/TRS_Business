<?php

namespace App\Livewire\Backend\TermsCondition;

use Exception;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\TermsCondition;
use App\Libraries\ImageManager;
use App\Models\TermsConditionUser;
use App\Traits\UserTermsCondition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Forms\TermsConditionAcceptForm;

class TermsConditionAcceptComponent extends Component
{
    use UserTermsCondition, WithFileUploads;

    public TermsConditionAcceptForm $form;
    public string $userTermsConditionPath = 'user-terms-conditions';

    public function render()
    {
        $termsConditions = $this->getUserTermsRoles()->first();
        return view('livewire.backend.terms-condition.terms-condition-accept-component', compact('termsConditions'));
    }

    public function acceptTerms($id)
    {
        $this->form->id = $id;
        $validated = $this->form->validate();
        $user = auth()->user();

        try {
            DB::beginTransaction();
            $termsCondition = TermsCondition::findOrFail($id);

            $pdf = App::make('dompdf.wrapper');
            $pdf->setOptions(['isPhpEnabled' => true])->setPaper('a4', 'portrait');
            $file_name = md5(time() . auth()->user()->name) . ' terms-condition' . '.pdf';
            if (!Storage::disk('public')->exists(getStoragePath('user-terms-condition'))) {
                Storage::disk('public')->makeDirectory(getStoragePath($this->userTermsConditionPath));
            }

            $termsConditionPath = public_path('storage/' . getStoragePath($this->userTermsConditionPath)) . '/' . $file_name;
            $view = 'pdf.term_condition-template';

            if (!empty($validated['signature_file'])) {
                $imageManager = new ImageManager();
                $validated['signature_file'] = $imageManager->setFile($validated['signature_file'])->setDirectory($this->userTermsConditionPath)->save();
            }

            $termsConditionUser = TermsConditionUser::updateOrCreate([
                'user_id' => $user->id,
                'terms_condition_id' => $id
            ], [
                'uuid' => getUuid(),
                'signature_url' => $validated['signature_file'],
                'pdf_url' => 'storage/' . getStoragePath('user-terms-condition') . '/' . $file_name
            ]);

            $name = $user->name;
            $pdf->loadView($view, compact('termsCondition', 'termsConditionUser','name'))->save($termsConditionPath);
            DB::commit();

            $this->dispatch('alert', ['type' => 'success',  'message' => 'Terms accepted successfully.']);
            return redirect()->route('dashboard.home');
        } catch (Exception $excpetion) {
            DB::rollBack();
            Log::error('Get error on accept terms and conditions from user: ' . $excpetion->getMessage());
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Something went wrong.']);
        }
    }
}
