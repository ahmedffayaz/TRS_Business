<?php

namespace App\Livewire\Backend\TermsCondition;

use Exception;
use Livewire\Component;
use Illuminate\Http\Request;
use App\Traits\WithMainModal;
use Livewire\WithFileUploads;
use App\Models\TermsCondition;
use Livewire\Attributes\Title;
use App\Libraries\ImageManager;
use Illuminate\Http\JsonResponse;
use App\Models\TermsConditionUser;
use App\Traits\UserTermsCondition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Forms\TermsConditionAcceptForm;

#[Title('Accept Terms & Conditions')]
class TermsConditionAcceptComponent extends Component
{
    use UserTermsCondition, WithFileUploads, WithMainModal;

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
            $termsCondition = TermsCondition::with('business')->findOrFail($id);

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
                'signature_url' => $validated['signature_file'] ?? $validated['digital_signature_pad'],
                'pdf_url' => 'storage/' . getStoragePath('user-terms-condition') . '/' . $file_name
            ]);

            $name = $user->name;
            $pdf->loadView($view, compact('termsCondition', 'termsConditionUser','name'))->save($termsConditionPath);
            DB::commit();

            $this->dispatch('alert', ['type' => 'success',  'message' => 'Terms accepted successfully.']);
            return redirect()->route('dashboard.home');
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error on accept terms and conditions from user: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Something went wrong.']);
        }
    }

    public function uploadDigitalImage(Request $request)
    {
        try {
            $dir = getStoragePath('users');
            // Upload file if exists
            if ($request->file('digital_signature')) {
                $now = now()->timestamp;
                $digital_signature = 'signature_' . $now . '.png';
                $request->file('digital_signature')->storeAs($dir, $digital_signature, 'public');
                $inputs['digital_signature'] = $digital_signature;
            }
            return response()->json([
                'success' => JsonResponse::HTTP_OK,
                'message' => 'Contract saved successfully',
                'data' => ['signature' => 'images/users/' . $digital_signature],
            ], JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            Log::error('Get error on accept terms and conditions from user: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Something went wrong.']);
        }
    }
}
