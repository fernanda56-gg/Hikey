<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Notifications\TeamProjectLeader;
use App\Notifications\TeamProjectMembers;
use App\Notifications\TeamProjectRemoveLeader;
use App\Notifications\TeamProjectRemoveMember;
use App\Services\ProjectTeamService;
use Gate;
use Illuminate\Http\Request;

use function Pest\Laravel\json;

class ProjectTeamController extends Controller
{
    // ! se añade el servicio de ProjectTeamService
    public function __construct(protected ProjectTeamService $teamService){
        $this->teamService = $teamService;
    }

public function index(Project $project)
{
    if(Gate::denies('manageTeam', $project)){
        abort(403, 'No tienes los permisos necesarios para realizar esta acción.');
    }

    $companyMembers = User::whereHas('companies', function ($query) use ($project) {
            $query->where('companies.id', $project->company_id);
        })
        ->select('id', 'name', 'last_name', 'email')
        ->withCount(['project_team as active_projects' => function ($query) {
            $query->whereIn('projects.status', ['Pendiente', 'En progreso']);
        }])
        ->orderBy('name')
        ->get()
        ->map(fn ($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'is_available' => $user->active_projects === 0,
        ]);

    $workingInProject = $project->users()->pluck('users.id');

    return response()->json([
        'members' => $companyMembers,
        'workingInProject' => $workingInProject,
    ]);
}

    public function store(Request $request, Project $project)
    {
        if(Gate::denies('manageTeam', $project)){
            abort(403, 'No tienes los permisos necesarios para realizar esta acción.');
        }

        /* al ser varios integrante los id llegan en un array por lo que se valida el array de esa forma */
        $validated = $request->validate([
            'members_ids' => 'required|array|min:1',
            'members_ids.*' => 'exists:users,id'
        ]);

        foreach ($validated['members_ids'] as $user_id) {
            $user = User::findOrFail($user_id);
            $this->teamService->addMembers($project, $user, 'Miembro');
            $user->notify(new TeamProjectMembers($project));
        }

        return back()->with('success', 'Integrantes agregados exitosamente');
    }

    public function update(Request $request, Project $project, User $user)
    {
        if(Gate::denies('manageLeaders', $project)){
            abort(403, 'No tienes los permisos necesarios para realizar esta acción.');
        }

        $validated = $request->validate([
            'role' => 'required|in:Lider,Miembro'
        ]);

        $this->teamService->changeRole($project, $user, $validated['role']);
        $user->notify(new TeamProjectLeader($project));

        return back()->with('success', 'Rol de integrante actualizado');
    }

    public function destroy(Project $project, User $user)
    {
        if(Gate::denies('manageTeam', $project)){
            abort(403, 'No tienes los permisos necesarios para realizar esta acción.');
        }

        $this->teamService->removeMember($project, $user);
        $user->notify(new TeamProjectRemoveMember($project));

        return back()->with('success', 'Integrante eliminado');
    }

    public function removeLeader(Project $project, User $user)
    {
        if(Gate::denies('manageLeaders', $project)){
            abort(403, 'No tienes los permisos necesarios para realizar esta acción.');
        }

        $this->teamService->removeLeader($project, $user);
        $user->notify(new TeamProjectRemoveLeader($project));
        
        return back()->with('success', 'Lider de equipo removido exitosamente');
    }
}
