<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ActivityPolicy
{
    /**
     * Determina quien puede ver todas las actividades de todas las empresas
     */
    public function viewAny(User $user): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if($user->hasRole('admin')){
            return true;
        }

        // ? Solo el administrador puede ver todas las actividades del sistema
        return false;
    }

    /**
     * Determina quien puede ver el tablero de actividades de un proyecto en especifico
     */
    public function view(User $user, Activity $activity): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que el usuario pertenezca a la misma empresa que el proyecto
        return $user->companies()->where('companies.id', $activity->project_activity->company_id)->exists();
    }

    /**
     * Determina quien puede generar actividades
     */
    public function create(User $user, Project $project): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que el usuario sea el manager de la empresa
        if ($user->hasRole('manager')) {
            return $user->companies()->where('companies.id', $project->company_id)->exists();
        }

        // ? Solo el líder del proyecto puede crear nuevas actividades
        if ($user->hasRole('team-leader')) {
            return $project->leader()->contains($user);
        }

        return false;
    }

    /**
     * Determina quien puede editar la info de las actividades
     */
    public function update(User $user, Activity $activity): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que solo el manager de la empresa pueda actualizar la info de la actividad
        if ($user->hasRole('manager')) {
            return $user->companies()->where('companies.id', $activity->project_activity->company_id)->exists();
        }

        // ? Comprueba que solo el líder de equipo pueda editar la info de la actividad
        return $user->hasRole('team-leader') && $activity->project_activity->leader()->contains($user);
    }

    /**
     * Determina quien puede eliminar actividades
     */
    public function delete(User $user, Activity $activity): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if($user->hasRole('admin')){
            return true;
        }

        // ? Comprueba que solo el creador de la actividad pueda eliminarla
        if ($activity->by_user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determina quien puede restaurar actividades eliminadas
     */
    public function restore(User $user, Activity $activity): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if($user->hasRole('admin')){
            return true;
        }

        // ? Solo el manager de la empresa puede eliminar/restaurar una actividad
        if ($user->hasRole('manager')) {
            return $user->companies->where('companies.id', $activity->project_activity->company_id)->exists();
        }

        return false;
    }

    /**
     * Determina quien puede eliminar definitivamente actividades de un proyecto
     */
    public function forceDelete(User $user, Activity $activity): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if($user->hasRole('admin')){
            return true;
        }

        // ? Solo el manager de la empresa puede eliminar/restaurar una actividad
        if ($user->hasRole('manager')) {
            return $user->companies->where('companies.id', $activity->project_activity->company_id)->exists();
        }

        return false;
    }
}
