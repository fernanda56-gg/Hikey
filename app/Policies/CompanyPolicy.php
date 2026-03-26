<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if($user->hasRole('admin')){
            return true;
        }if($user->hasRole('user'))
        {
            return true;
        }
        return false;
    }

    /**
     * Permite que pueda ver la empresa pero solo si es miembro.
     */
    public function view(User $user, Company $company): bool
    {
        return $company->member->contains($user->id) || $user->hasRole('admin');
    }

    public function viewList(User $user, Company $company): bool
    {
        if($user->hasRole('admin')){
            return true;
        }

        if($company->owner_id === $user->id){
            return true;
        }

        return $company->member()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return !$user->companies()->exists();
    }

    /**
     * Permite que el usuario pueda editar la info de la empresa pero solo si es propietario.
     */
    public function update(User $user, Company $company): bool
    {
        return $user->isOwner($company)  || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Company $company): bool
    {
        return $user->isOwner($company) || $user->hasRole('admin');
    }

    public function leaveCompany(User $user): bool
    {
        return $user->hasRole('manager') || $user->hasRole('admin');
    }

    public function joinCompany(User $user): bool
    {
        if($user->hasRole('admin')){
            return true;
        }if($user->hasRole('user') && !$user->companies()->where('user_id', $user->id)->exists()){
            return true;
        }elseif($user->hasRole('manager')){
            return false;
        }return false;
    }

    public function viewMembers(User $user): bool
    {
        return $user->companies()->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Company $company): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Company $company): bool
    {
        return false;
    }

    public function is_Admin(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
