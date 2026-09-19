<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determina quien puede ver la vista users.index
     */
    public function viewAny(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return false;
    }

    /* Determina que solo el usuario pueda ver la info de su cuenta */
    public function viewAccountUser(User $user, User $model): bool
    {
        // ? Comprueba que solo el usuario pueda ver la info de su cuenta
        return $user->id === $model->id;
    }

    /**
     * Determina quien puede crear usuarios
     */
    public function create(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        return $user->hasRole('admin');
    }

    /**
     * Determina quien puede editar la info de usuario
     */
    public function update(User $user, User $model): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        return $user->hasRole('admin');
    }

    // Determina que solo el usuario pueda actualizar la info de cuenta
    public function updateAccountUser(User $user, User $model): bool
    {
        // ? Comprueba que sea el dueño de la cuenta
        return $user->id === $model->id;
    }

    /* Determina quien puede cambiar la contraseña de la cuenta de usuario */
    public function updatePassword(User $user, User $model): bool
    {
        // ? Comprueba que sea el dueño de la cuenta
        return $user->id === $model->id;
    }

    /**
     * Determina quien puede eliminar cuenta de usuario
     */
    public function delete(User $user, User $model): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        return $user->hasRole('admin');
    }

    public function deleteAccountUser(User $user, User $model): bool
    {
        // ? Comprueba que sea el dueño de la cuenta
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
