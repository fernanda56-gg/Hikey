<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        /* Comprueba que sea admin o que sea el manager de la empresa donde pertenece el cliente */
        if ($user->hasRole('admin')){
            return true;
        } elseif ($user->hasRole('manager') && $user->companyOwner()->where('owner_id', $user->id)->exists()) {
            return true;
        }
        return false;
    }

    public function view(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que los otros roles puedan acceder siempre y cuando pertenezca a la empresa
        elseif ($user->hasAnyRole(['manager', 'team-leader', 'user'])){
            return $user->companies()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function viewClientProjects(User $user): bool
    {
        /* Comprueba que sea admin o que sea el manager de la empresa donde pertenece el cliente */
        if ($user->hasRole('admin')){
            return true;
        } elseif ($user->hasRole('manager') && $user->companyOwner()->where('owner_id', $user->id)->exists()) {
            return true;
        }
        return false;
    }
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba, que el los usuarios puedan crear clientes dentro de su empresa
        elseif ($user->hasAnyRole(['manager', 'team-leader'])){
            return $user->companies()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba, que el los usuarios puedan editar clientes dentro de su empresa
        elseif ($user->hasAnyRole(['manager', 'team-leader'])){
            return $user->companies()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba, que el los usuarios puedan eliminar clientes dentro de su empresa
        elseif ($user->hasAnyRole(['manager', 'team-leader'])){
            return $user->companies()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Client $client): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Client $client): bool
    {
        return false;
    }

    public function assign(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba, que el los usuarios puedan asignar clientes dentro de su empresa
        elseif ($user->hasAnyRole(['manager', 'team-leader'])){
            return $user->companies()->where('user_id', $user->id)->exists();
        }

        return false;
    }
}
