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
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que el usuario solo pueda ver info de su empresa
        elseif ($user->hasAnyRole(['manager', 'team-leader', 'user'])){
            return $company->member()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    public function viewList(User $user, Company $company): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que el usuario solo pueda ver info de su empresa
        elseif ($user->hasAnyRole(['manager', 'team-leader', 'user'])){
            return $company->member()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')) {
            return true;
        }

        // ? Comprueba que solo usuarios sin empresa puedan crear una
        elseif ($user->hasRole('user') && ! $user->companies()->exists()){
            return true;
        }

        return false;
    }

    /**
     * Permite que el usuario pueda editar la info de la empresa pero solo si es propietario.
     */
    public function update(User $user, Company $company): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que solo el dueño de la empresa pueda editar la info de la empresa
        elseif ($user->hasRole('manager')){
            return $user->isOwner($company);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Company $company): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que solo el dueño de la empresa pueda eliminar la info de la empresa
        elseif ($user->hasRole('manager')){
            return $user->isOwner($company);
        }

        return false;
    }

    public function leaveCompany(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que solo el dueño de la empresa pueda sacar a algún usuario
        elseif ($user->hasRole('manager')){
            return $user->companies()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    public function joinCompany(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que solo los usuarios que no se han unido a una empresa puedan unirse a una
        elseif ($user->hasRole('user') && ! $user->companies()->where('user_id', $user->id)->exists()){
            return true;
        }

        return false;
    }

    public function viewMembers(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que el usuario solo pueda ver info de su empresa
        elseif ($user->hasAnyRole(['manager', 'team-leader', 'user'])){
            return $user->companies()->where('user_id', $user->id)->exists();
        }

        return false;
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

    public function sendInvitation(User $user, Company $company): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que solo estos usuarios puedan hacer invitaciones
        elseif ($user->hasAnyRole(['manager', 'team-leader'])){
            return $company->member()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    public function showCode(User $user, Company $company): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que solo estos usuarios puedan ver el código de invitación
        elseif ($user->hasAnyRole(['manager', 'team-leader'])){
            return $user->companies()->where('user_id', $user->id)->exists();
        }

        // ? El usuario no puede ver el código de invitación
        elseif ($user->hasRole('user') && $user->companies()->where('user_id', $user->id)->exists()){
            return false;
        }

        return false;
    }
}
