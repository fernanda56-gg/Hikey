<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Company;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if($user->hasRole('admin')){ //El usuario ADMIN puede ver todos los proyectos independientemente de la empresa
            $projects = Project::with('area')->get();
        }elseif($user->companies()->exists()){ //Cualquier otro usuario sea miembro o propietario puede ver los proyectos de su empresa
            $company = $user->companies()->pluck('companies.id');
            $projects = Project::with('area')->whereIn('company_id', $company)->get();
        }else{//Si el usuario no pertenece a ninguna empresa no podrá ver ningún proyecto
            $projects = collect();
        }

        return inertia(
            'Project/IndexProject',
            [
                'projects' => $projects,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(!Auth::user()->can('create projects'), 403, 'No tienes los permisos necesarios para ver esta pagina.');
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

        $user->projects()->create($data);

        return redirect()->route('projects.index')->with('success', 'Proyecto creado con éxito.');

    } catch (ValidationException $e) {
        throw $e;
    } catch (\Exception $e) {
        /* dd($e->getMessage()); */
        return redirect()->back()->with('error', 'Error al actualizar proyecto.')->withInput();//withInput mantiene los datos del form
    }
}


    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $user = Auth::user();
        $project->load('area', 'company', 'clients');

        $project->clients->each(function ($client) use ($user) { //Permisos para poder editar y eliminar clientes desde este controlador
            $client->client_update = $user->can('update', $client);
            $client->client_delete = $user->can('delete', $client);
        });

        return inertia('Project/ShowProject', [
            'project' => $project,
            'can' => [
                'update' => $user->can('update', $project),
                'delete' => $user->can('delete', $project),
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {

        abort_if(!Auth::user()->can('edit projects'), 403, 'No tienes los permisos necesarios para realizar esta acción.');
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

            //los datos se guardan en BD
            return redirect()->route('projects.index')->with('success', 'Proyecto actualizado.');
        }catch(ValidationException $e){
            throw $e;
        }catch(\Exception $e){
            //si los datos no se guardan, se muestra mensaje de error
            return redirect()->back()->with('error', 'Error al actualizar proyecto.')->withInput();//withInput mantiene los datos del form
        }
    }

    public function updateDate(Request $request, Project $project)
    {

        Gate::denies('update', $project);
        {
            return redirect()->back()->with('error', 'No tienes permiso para modificar fechas.');
        }

            $validates = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $project->update($validates);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        abort_if(!Auth::user()->can('delete projects'), 403, 'No tienes los permisos necesarios para realizar esta acción.');
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Proyecto eliminado.');
    }
}
