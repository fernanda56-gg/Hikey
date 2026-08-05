<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Company;
use App\Models\Project;
use App\Notifications\ProjectChangeDates;
use App\Notifications\ProjectCreated;
use App\Notifications\ProjectDeleted;
use App\Notifications\ProjectEdited;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Project::with('area')->mostRecent(); //Utiliza el scope de Proyectos para ordenarlos por fecha de creación

        if($user->hasRole('admin')){ //El usuario ADMIN puede ver todos los proyectos independientemente de la empresa
            $projects = $query;
        }elseif($user->companies()->exists()){ //Cualquier otro usuario sea miembro o propietario puede ver los proyectos de su empresa
            $company = $user->companies()->pluck('companies.id');
            $projects = $query->whereIn('company_id', $company);
        }else{//Si el usuario no pertenece a ninguna empresa no podrá ver ningún proyecto
            $projects = collect();
        }

        /* Para acceder a los campos del filtro */
        $filters = $request->only(['name', 'area', 'status']);
        $areas = Area::all();

        /* Realiza la paginación y mediante el scope que se llama desde el modelo se filtran los datos */
        $projects = $query->filter($filters)->paginate(10)->withQueryString();

        /* Para acceder a permisos de editar y eliminar proyectos */
        $projects->each(function ($project) use ($user){
            $project->update_p = $user->can('update', $project);
            $project->delete_p = $user->can('delete', $project);
        });

        return inertia(
            'Project/IndexProject',
            [
                'projects' => $projects,
                'areas' => $areas,
                'filters' => $filters,
                'can' => [
                'create' => $user->can('create', Project::class),
                'view' => $user->can('viewAny', Project::class),
            ]
            ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(Gate::denies('create', Project::class))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        return inertia('Project/CreateProject', [
            'areas' => Area::all(),
            'companies' => Auth::user()->hasRole('admin') ? Company::all() : [], //Asegura que solo el ADMIN pueda ver todas las empresas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = [
                'name' => 'required|string|min:3|max:50',
                'description' => 'required|string|max:255',
                'link' => 'required|url',
                'image_path' => 'required|url',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'area_id' => 'required|exists:areas,id',
            ];
            $user = $request->user();

            if ($user->hasRole('admin')) { //Si el user es ADMIN validara lo que haya puesto en el select
                $validated['company_id'] = 'required|exists:companies,id';
            }

            $data = $request->validate($validated); //Solicita los datos validados

            /* Dependiendo del rol que tenga el usuario almacena de forma diferente el company_id */
            if ($user->hasRole('admin')) {
                $data['company_id'] = $data['company_id'];
            } else {
                $company = $user->companies()->first();
                $data['company_id'] = $company?->id;
            }

            /* notificación */
            $project = $user->projects()->create($data);
            $project->project_owner->notify(
                new ProjectCreated($project)
            );

            return redirect()->route('projects.index')->with('success', 'Proyecto creado con éxito.');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            /* dd($e->getMessage()); */
            return redirect()->back()->with('error', 'Error al generar proyecto.')->withInput();//withInput mantiene los datos del form
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        if(Gate::denies('view', $project))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        $user = Auth::user();
        $project->load('area', 'company', 'clients', 'users');

        $project->clients->each(function ($client) use ($user) { //Permisos para poder editar y eliminar clientes desde este controlador
            $client->client_update = $user->can('update', $client);
            $client->client_delete = $user->can('delete', $client);
            $client->client_unlink = $user->can('assign', $client);
        });

        return inertia('Project/ShowProject', [
            'project' => $project,
            'can' => [
                'update' => $user->can('update', $project),
                'delete' => $user->can('delete', $project),
            ],
            'canManageTeam' =>  $user->can('manageTeam', $project),
            'canManageLeader' => $user->can('manageLeaders', $project),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        if(Gate::denies('update', $project))
        {
            abort(403, 'No tienes los permisos necesarios para realizar esta acción.');
        }

        return inertia(
            'Project/EditProject',
            [
                'project' => $project,
                'areas' => Area::all(),
            ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {

        try{
        $validated = $request->validate([
                    'name' => 'required|string|min:3|max:50',
                    'description' => 'required|string|max:255',
                    'link' => 'required|url',
                    'image_path' => 'required|url',
                    'area_id' => 'required|integer|exists:areas,id',
        ]);
        $project->update($validated);

        /* Generar notificación para dueño del proyecto */
        $project->project_owner->notify(
            new ProjectEdited($project)
        );

            //los datos se guardan en BD
            return redirect()->route('projects.show', ['project' => $project])->with('success', 'Proyecto actualizado.');
        }catch(ValidationException $e){
            throw $e;
        }catch(\Exception $e){
            //si los datos no se guardan, se muestra mensaje de error
            return redirect()->back()->with('error', 'Error al actualizar proyecto.')->withInput();//withInput mantiene los datos del form
        }
    }

    public function updateDate(Request $request, Project $project)
    {

        if(Gate::denies('updateDate', $project))
        {
            return redirect()->back()->with('error', 'No tienes permiso para modificar fechas.');
        }

            $validates = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $project->update($validates);

        /* Generar notificación para dueño del proyecto */
        $project->project_owner->notify(
            new ProjectChangeDates($project)
        );
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        if(Gate::denies('delete', $project))
        {
            abort(403, 'No tienes los permisos necesarios para realizar esta acción.');
        }

        if ($project->trashed()) {
            /* Generar notificación para dueño del proyecto */
            $project->project_owner->notify(
                new ProjectDeleted($project)
                );
            $project->forceDelete();
        } else {
            $project->delete();
        }
        return redirect()->route('projects.index')->with('success', 'Proyecto eliminado.');
    }

    public function trash()
    {
        if(Gate::denies('viewAny', Project::class))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        $user = Auth::user();
        $query = Project::onlyTrashed()->with('area')->mostRecent();

        if($user->hasRole('admin')){ //El usuario ADMIN puede ver todos los proyectos independientemente de la empresa
            $projects = $query->get();
        }elseif($user->companies()->exists()){ //Cualquier otro usuario sea miembro o propietario puede ver los proyectos de su empresa
            $company = $user->companies()->pluck('companies.id');
            $projects = $query->whereIn('company_id', $company)->get();
        }

        $projects = $query->paginate(5)->withQueryString();

        return inertia(
            'Project/TrashProject',
            [
                'projects' => $projects,
            ]);
    }

    public function recover($id)
    {
        $project = Project::withTrashed()->findOrFail($id);

        if (Gate::denies('delete', $project)) {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        $project->restore();
        return redirect()->route('projects.show', $project->id)->with('success', 'Proyecto recuperado con éxito.');
    }
}
