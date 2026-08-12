<?php

namespace App\Observers;

use App\Models\Company;

class CompanyObserver
{
    public function created(Company $company):void
    {
        $company->owner->removeRole('user');
        $company->owner->assignRole('manager');
    }

    public function deleted(Company $company):void
    {
        $company->owner->removeRole('manager');
        $company->owner->assignRole('user');
    }
}
