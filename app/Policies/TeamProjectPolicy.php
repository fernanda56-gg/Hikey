<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;

class TeamProjectPolicy
{
    /**
     * Permite que el usuario pueda ver la lista de equipos
     */
    public function viewTeams(User $user, Project $project): bool
    {
        if($user->hasRole('admin')){
            return true;
        }

        if ($user->hasRole('manager')) {
            return $project->company->owner_id === $user->id;
        }

        return false;
    }

    /* Permite administrar a los equipos esto incluye añadir o remover miembros  */
    public function manageTeam(User $user, Project $project): bool
    {
        if($user->hasRole('admin')){
            return true;
        }

        if ($user->hasRole('manager')) {
            return $project->company->owner_id === $user->id;
        }

        if($user->hasRole('team-leader') && $project->users()
                                            ->wherePivot('role', 'Lider')
                                            ->where('user_id', $user->id)
                                            ->exists()){
                                                return true;
                                            }

        return false;
    }

    /* Permite administrar el rol de LIDER de equipo de un proyecto */
    public function manageLeaders(User $user, Project $project): bool
    {
        if($user->hasRole('admin')){
            return true;
        }

        if ($user->hasRole('manager')) {
            return $project->company->owner_id === $user->id;
        }
        return false;
    }
}
