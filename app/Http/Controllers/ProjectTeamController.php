<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Services\ProjectTeamService;
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
        /* al ser varios integrante los id llegan en un array por lo que se valida el array de esa forma */
        $validated = $request->validate([
            'members_ids' => 'required|array|min:1',
            'members_ids.*' => 'exists:users,id'
        ]);

        foreach ($validated['members_ids'] as $user_id) {
            $this->teamService->addMembers($project, User::findOrFail($user_id), 'Miembro');
        }

        return back()->with('success', 'Integrantes agregados exitosamente');
    }

    public function update(Request $request, Project $project, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:Lider,Miembro'
        ]);

        $this->teamService->changeRole($project, $user, $validated['role']);
        return back()->with('success', 'Rol de integrante actualizado');
    }

    public function destroy(Project $project, User $user)
    {
        $this->teamService->removeMember($project, $user);
        return back()->with('success', 'Integrante eliminado');
    }

    public function removeLeader(Project $project, User $user)
    {
        $this->teamService->removeLeader($project, $user);
        return back()->with('success', 'Lider de equipo removido exitosamente');
    }
}
