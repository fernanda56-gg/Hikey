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
        return $user->companies()->where('companies.id', $activity->project->company_id)->exists();
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
            return $project->leader()->where('users.id', $user->id)->exists();
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
            return $user->companies()->where('companies.id', $activity->project->company_id)->exists();
        }

        // ? Comprueba que solo el líder de equipo pueda editar la info de la actividad
        return $user->hasRole('team-leader') && $activity->project->leader()->where('users.id', $user->id)->exists();
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

        // ? Comprueba que solo el manager de la empresa pueda eliminarla
        if ($user->hasRole('manager')) {
            return $user->companies()->where('companies.id', $activity->project->company_id)->exists();
        }

        // ? Comprueba que solo el líder de equipo del proyecto pueda eliminarla
        if ($user->hasRole('team-leader')) {
            return $user->companies()->where('companies.id', $activity->project->company_id)->exists();
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
            return $user->companies->where('companies.id', $activity->project->company_id)->exists();
        }

        return false;
    }

    /**
     * Determina quien puede eliminar definitivamente actividades de un proyecto
     */
    public function forceDelete(User $user, Activity $activity): bool
    {
        // ! La misma regla que la función restore
        return $this->restore($user, $activity);
    }

    /**
     * Determina quien puede acceder a la vista de trash
     */
    public function viewTrash(User $user, Project $project): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if($user->hasRole('admin')){
            return true;
        }

        // ? Solo el manager de la empresa puede ver la vista trash de actividad
        if ($user->hasRole('manager')) {
            return $user->companies->where('companies.id', $project->company->id)->exists();
        }

        return false;
    }

    /**
     * Determina quien puede modificar el estatus de una actividad
     */
    public function updateStatus(User $user, Activity $activity): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if($user->hasRole('admin')){
            return true;
        }

        // ? El manager de la empresa puede cambiar el estatus de las actividades
        if ($user->hasRole('manager')) {
            return $user->companies()->where('companies.id', $activity->project->company_id)->exists();
        }

        // ? Solo los integrantes de equipo asignados al proyecto pueden modificar el estatus (Líder y miembro)
        if ($user->hasAnyRole(['team-leader', 'user'])) {
            return $activity->project->users()->where('users.id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determina quien puede evaluar una actividad
     */
    public function review(User $user, Activity $activity): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if($user->hasRole('admin')){
            return true;
        }

        // ? El manager de la empresa puede evaluar las actividades realizadas
        if ($user->hasRole('manager')) {
            return $user->companies()->where('companies.id', $activity->project->company_id)->exists();
        }

        // ? El líder de equipo del proyecto puede evaluar las actividades realizadas
        if ($user->hasRole('team-leader')) {
            return $activity->project->leader()->where('users.id', $user->id)->exists();
        }

        return false;
    }
}
