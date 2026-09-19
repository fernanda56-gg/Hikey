<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determina que se puedan visualizar el dashboard de proyectos
     */
    public function viewAny(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que los usuarios que vean los proyectos pertenezcan a la misma empresa que los proyectos
        if($user->companies()->exists()){
            return true;
        }return false;
    }

    /**
     * Determina que se pueda ver la vista de projects.show
     */
    public function view(User $user, Project $project): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que los usuarios que vean los proyectos pertenezcan a la misma empresa que los proyectos
        if($user->companies()->where('company_id', $project->company_id)->exists()){
            return true;
        }return false;
    }

    /**
     * Determina quien puede crear proyectos
     */
    public function create(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')){
            return true;
        }

        // ?  Comprueba que el manager de la empresa pueda generar nuevos proyectos
        if($user->hasRole('manager')){
            return $user->companies()->exists();
        }

        return false;
    }

    /**
     * Determina quien puede actualizar la información del proyecto
     */
    public function update(User $user, Project $project): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')) {
            return true;
        }

        // ? Comprueba que el usuario sea dueño del proyecto
        if ($project->by_user_id === $user->id) {
            return true;
        }

        // ? Comprueba que los usuarios pertenezcan a la empresa
        if ($user->hasAnyRole(['manager', 'team-leader'])) {
            return $project->company->member()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /* Determina quien puede actualizar las fechas de inicio y fin de proyecto */
    public function updateDate(User $user, Project $project): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que solo esos roles de la empresa puedan actualizar las fechas del proyecto
        if($user->hasRole('manager')){
            return $project->by_user_id === $user->id;
        }

        return false;
    }

    /**
     * Determina quien puede eliminar un proyecto
     */
    public function delete(User $user, Project $project): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if ($user->hasRole('admin')) {
            return true;
        }

        // ? Comprueba que el usuario sea dueño del proyecto
        if ($project->by_user_id === $user->id) {
            return true;
        }

        // ? Comprueba que los usuarios pertenezcan a la empresa
        if ($user->hasRole('manager')) {
            return $project->company->member()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /* Determina quien puede ver la vista de proyectos eliminados */
    public function trash(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que el dueño de la empresa pueda ver los proyectos eliminados
        if ($user->hasRole('manager') && $user->companyOwner()->where('owner_id', $user->id)->exists()){
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
