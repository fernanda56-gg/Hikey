<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    /**
     * Permite que se pueda ver la vista company.index
     */
    public function viewAny(User $user): bool
    {
        if($user->hasRole('admin')){
            return true;
        }

        // ? Rol usuario puede ver la vista si no se ha unido o creado a una empresa
        if($user->hasRole('user'))
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
        if ($user->hasAnyRole(['manager', 'team-leader', 'user'])){
            return $company->member()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /* Permite que se pueda ver la lista de miembros de empresa */
    public function viewList(User $user, Company $company): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que el usuario solo pueda ver info de su empresa
        if ($user->hasAnyRole(['manager', 'team-leader', 'user'])){
            return $company->member()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Permite crear empresas
     */
    public function create(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')) {
            return true;
        }

        // ? Comprueba que solo usuarios sin empresa puedan crear una
        if ($user->hasRole('user') && ! $user->companies()->exists()){
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
        if ($user->hasRole('manager')){
            return $user->isOwner($company);
        }

        return false;
    }

    /**
     * Permite que el usuario pueda eliminar una empresa
     */
    public function delete(User $user, Company $company): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que solo el dueño de la empresa pueda eliminar la info de la empresa
        if ($user->hasRole('manager')){
            return $user->isOwner($company);
        }

        return false;
    }

    /* Permite que el usuario pueda sacar miembros de una empresa */
    public function leaveCompany(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que solo el dueño de la empresa pueda sacar a algún usuario
        if ($user->hasRole('manager')){
            return $user->companies()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /* Permite al usuario unirse a una empresa */
    public function joinCompany(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que solo los usuarios que no se han unido a una empresa puedan unirse a una
        if ($user->hasRole('user') && ! $user->companies()->where('user_id', $user->id)->exists()){
            return true;
        }

        return false;
    }

    /* Permite que se pueda ver la lista de miembros de empresa */
    public function viewMembers(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que el usuario solo pueda ver info de su empresa
        if ($user->hasAnyRole(['manager', 'team-leader', 'user'])){
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

    /* Verifica que el usuario sea el administrador */
    public function is_Admin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /* Permite que el usuario pueda enviar invitaciones a otros usuarios para unirse a una empresa */
    public function sendInvitation(User $user, Company $company): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que solo estos usuarios puedan hacer invitaciones
        if ($user->hasAnyRole(['manager', 'team-leader'])){
            return $company->member()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /* Permite si se puede mostrar el código de unirse a empresa */
    public function showCode(User $user, Company $company): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que solo estos usuarios puedan ver el código de invitación
        if ($user->hasAnyRole(['manager', 'team-leader'])){
            return $user->companies()->where('user_id', $user->id)->exists();
        }

        // ? El usuario no puede ver el código de invitación
        if ($user->hasRole('user') && $user->companies()->where('user_id', $user->id)->exists()){
            return false;
        }

        return false;
    }
}
