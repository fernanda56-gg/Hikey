<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Validation\ValidationException;

// * Servicio que maneja la lógica sobre los equipos de los proyectos

class ProjectTeamService{

    // ! se establece numero max de integrantes por equipo
    public const MAX_TEAM_MEMBERS = 9;

    // ! se establece el rol de líder de equipo
    public const LEADER_ROLE = 'team-leader';

    // * función para asignar el rol de spatie de team-leader
    protected function spatieRole(User $user)
    {
        // ? busca al lider y comprueba que sea lider de un proyecto que aun no este terminado
        $isProjectLeader = $user->project_team()
                                ->wherePivot('role', 'Lider')
                                ->whereIn('projects.status', ['Pendiente', 'En progreso'])
                                ->exists();

        if ($isProjectLeader) { // ? si el usuario aun no tiene el rol de spatie se le asigna
            if (! $user->hasRole(self::LEADER_ROLE)) {
                $user->assignRole(self::LEADER_ROLE);
            }
        }else { // ? si el usuario deja de ser líder de un proyecto se le remueve el rol de spatie y regresa a su rol USER
            if ($user->hasRole(self::LEADER_ROLE)) {
                $user->removeRole(self::LEADER_ROLE);
            }
        }
    }

    // * funcion para sincronizar el spatie rol del usuario en caso de que el proyecto se complete
    public function syncLeaderRole(User $user)
    {
        $this->spatieRole($user);
    }

    // * función de validación de miembros del equipo para un proyecto
    public function addMembers(Project $project, User $user, string $role = 'Miembro')
    {
        // ? Número de integrantes en equipo
        if($project->users()->count() >= self::MAX_TEAM_MEMBERS){
            throw ValidationException::withMessages([
                'team' => 'El equipo ha alcanzado el número máximo de integrantes'
            ]);
        }

        // ? El usuario no este ya en el proyecto
        if($project->users()->where('user_id', $user->id)->exists()){
            throw ValidationException::withMessages([
                'user' => 'El usuario ya pertenece a este proyecto'
            ]);
        }

        // ? Solo existe un líder de equipo
        if($role === 'Lider' && $project->users()->wherePivot('role', 'Lider')->exists()){
            throw ValidationException::withMessages([
                'leader' => 'Ya existe un líder para este proyecto'
            ]);
        }

        // * Vincular a los usuarios con su respectivo rol
        $project->users()->attach($user->id, ['role' => $role]);

        // * Actualizar rol con spatie
        if($role === 'Lider'){
            $this->spatieRole($user);
        }
    }

    // * función para cambiar de rol a líder
    public function changeRole(Project $project, User $user, string $newRole)
    {
        if($newRole === 'Lider'){
            $currentLeader = $project->users()->wherePivot('role', 'Lider')->first();

            // ? Si ya existe un líder para el proyecto marcara error
            if($currentLeader && $currentLeader->id !== $user->id){
                throw ValidationException::withMessages([
                    'role' => 'El proyecto ya tiene un líder'
                ]);
            }
        }

        // * actualizar el rol
        $project->users()->updateExistingPivot($user->id, ['role' => $newRole]);

        // * actualiza el rol con spatie
        $this->spatieRole($user);
    }

    // * función para eliminar a un lider
    public function removeLeader(Project $project, User $user)
    {
        // ? se obtiene al lider
        $isLeader = $project->users()
                            ->wherePivot('role', 'Lider')
                            ->where('user_id', $user->id)
                            ->exists();

        // ? mensaje en caso de que no se LIDER
        if(!$isLeader){
            throw ValidationException::withMessages([
                'role' => 'El usuario no es lider de equipo'
            ]);
        }

        // ? se actualiza el rol del usuario en el proyecto
        $project->users()->updateExistingPivot($user->id, ['role' => 'Miembro']);
        $this->spatieRole($user);
    }

    // * función para sacar a miembro del equipo
    public function removeMember(Project $project, User $user)
    {
        // ? en caso de que el usuario sea el líder
        $isLeader = $project->users()
                            ->wherePivot('role', 'Lider')
                            ->where('user_id', $user->id)
                            ->exists();

        if($isLeader){
            throw ValidationException::withMessages([
                'user' => 'No se puede eliminar al líder del proyecto, se debe de asignar a un nuevo líder antes'
            ]);
        }

        // ? eliminación de un usuario con el rol Miembro
        $project->users()->detach($user->id);
    }
}
