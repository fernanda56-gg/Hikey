<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;

class ClientPolicy
{
    /**
     * Determina quien puede ver el client.index
     */
    public function viewAny(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que el manager pueda acceder a los clientes de su empresa
        if ($user->hasRole('manager') && $user->companyOwner()->where('owner_id', $user->id)->exists()) {
            return true;
        }
        return false;
    }

    /* Permite que los usuarios puedan ver al cliente en projects.show  */
    public function view(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que los otros roles puedan acceder siempre y cuando pertenezca a la empresa
        if ($user->hasAnyRole(['manager', 'team-leader', 'user'])){
            return $user->companies()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Permite que los usuarios puedan ver al cliente en projects.show
     */
    public function viewClientProjects(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que los otros roles puedan acceder siempre y cuando pertenezca a la empresa
        if ($user->hasRole('manager') && $user->companyOwner()->where('owner_id', $user->id)->exists()) {
            return true;
        }
        return false;
    }
    /**
     * Determina quien puede generar clientes de una empresa
     */
    public function create(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba, que el los usuarios puedan crear clientes dentro de su empresa
        if ($user->hasAnyRole(['manager', 'team-leader'])){
            return $user->companies()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determina quien puede actualizar la info de un cliente
     */
    public function update(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba, que el los usuarios puedan editar clientes dentro de su empresa

        if ($user->hasAnyRole(['manager', 'team-leader'])){
            return $user->companies()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determina quien puede eliminar a un cliente
     */
    public function delete(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba, que el los usuarios puedan eliminar clientes dentro de su empresa
        if ($user->hasAnyRole(['manager', 'team-leader'])){
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

    /* Determina quien puede asignar a un cliente a proyecto */
    public function assignToProject(User $user, Client $client, Project $project): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que la empresa en donde haya sido registrado el proyecto sea la misma que la del proyecto
        if ($client->company_id !== $project->company_id) {
            return false;
        }

        // ? Comprueba, que el los usuarios puedan asignar clientes dentro de su empresa
        if ($user->hasAnyRole(['manager', 'team-leader'])){
            return $user->companies()->where('companies.id', $project->company_id)->exists();
        }

        return false;
    }

    /* Permite desvincular a un cliente de un proyecto */
    public function detachToProject(User $user, Client $client, Project $project): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')) {
            return true;
        }

        // ? Comprueba que la empresa en donde haya sido registrado el proyecto sea la misma que la del proyecto
        if ($client->company_id !== $project->company_id) {
            return false;
        }

        // ? Comprueba, que el los usuarios puedan asignar clientes dentro de su empresa
        if ($user->hasAnyRole(['manager', 'team-leader'])) {
            return $user->companies()->where('companies.id', $project->company_id)->exists();
        }

        return false;
    }
}
