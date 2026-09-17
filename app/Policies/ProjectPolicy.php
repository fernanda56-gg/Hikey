<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if($user->hasRole('admin')){
            return true;
        }elseif($user->companies()->exists()){
            return true;
        }return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        if($user->hasRole('admin')){
            return true;
        }elseif($user->companies()->where('company_id', $project->company_id)->exists()){
            return true;
        }return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if($user->hasRole('manager')){
            return $user->companies()->exists();
        } elseif ($user->hasRole('admin')){
            return true;
        }return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')) {
            return true;
        } // ? Comprueba que el usuario sea dueño del proyecto
        elseif ($project->by_user_id === $user->id) {
            return true;
        } // ? Comprueba que los usuarios pertenezcan a la empresa
        elseif ($user->hasAnyRole(['manager', 'team-leader'])) {
            return $project->company->member()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    public function updateDate(User $user, Project $project): bool
    {
        if($user->hasRole('admin')){
            return true;
        }elseif($project->by_user_id === $user->id){
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')) {
            return true;
        } // ? Comprueba que el usuario sea dueño del proyecto
        elseif ($project->by_user_id === $user->id) {
            return true;
        } // ? Comprueba que los usuarios pertenezcan a la empresa
        elseif ($user->hasRole('manager')) {
            return $project->company->member()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    public function trash(User $user): bool
    {
        if($user->hasRole('admin')){
            return true;
        } elseif ($user->hasRole('manager') && $user->companyOwner()->where('owner_id', $user->id)->exists()){
            return true;
        }return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return false;
    }
}
