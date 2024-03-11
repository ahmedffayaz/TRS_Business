<?php

namespace App\Traits;

use App\Models\TermsCondition;

trait UserTermsCondition
{
    public $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function getUserTermsRoles()
    {
        return $this->getTermsConditionsHasRoles($this->getTermsConditions());
    }

    public function termsAccepted()
    {
        return $this->getTermsConditionsHasRoles($this->getTermsConditions())
            ->whereHas('termsConditionUsers', function ($query) {
                $query->where('user_id', $this->user->id);
            })->exists();
    }

    // Get user roles
    private function getUserRoles()
    {
        return $this->user->roles->pluck('id')->toArray();
    }

    // Get terms and conditions
    private function getTermsConditions()
    {
        return TermsCondition::sessionBusiness();
    }

    // Get terms & conditions where has roles
    private function getTermsConditionsHasRoles($query)
    {
        return $query->whereHas('roles', function ($query) {
            $query->whereIn('role_id', $this->getUserRoles());
        });
    }
}
