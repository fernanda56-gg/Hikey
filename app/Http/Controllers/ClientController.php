<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        if(Gate::denies('viewAny', Client::class))
            {
                abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
            }

        /* Dependiendo del rol del usuario es a los registros que lo dejara ver */
        $user = Auth::user();
        if($user->hasRole('admin')){ //ADMIN: Puede ver todos los registros
            $clients = Client::with('projects', 'company')->get();
        }else{
            $company = $user->companies()->first(); //MANAGER: Solo puede ver los registros de su empresa
            $clients = Client::where('company_id', $company->id)->with('projects')->get();
        }

        return inertia('Clients/IndexClient',[
            'clients' => $clients,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project) // De esta manera trae el ID del proyecto al que se vinculara el registro
    {
        $user = Auth::user();
        if(Gate::denies('create', Client::class))
            {
                abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
            }

        return inertia('Clients/CreateClient', [
            'project' => $project
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try{
        $validated = $request->validate([
        'name' => 'required|string|min:3|max:100',
        'email' => 'required|string|email',
        'phone' => 'required|string|max:20',
        'project_id' => 'required|exists:projects,id'
    ]);

    $project = Project::findOrFail($validated['project_id']); //encuentra el ID del proyecto
    $data = collect($validated)->except('project_id')->merge(['company_id' => $project->company_id])->toArray(); //recolecta la información con excepción del project_id
    $client = Client::create($data); //crea el registro
    $client->projects()->attach($project->id); //añade el id del proyecto para la tabla pivote

    return redirect()->route('projects.show', $project->id)->with('success', 'Cliente creado con éxito.');

    }
    catch(ValidationException $e){
            throw $e;
        }catch(\Exception $e){
            //si los datos no se guardan, se muestra mensaje de error
            /* dd($e->getMessage()); */
            return redirect()->back()->with('error', 'Error al generar cliente.')->withInput();//withInput mantiene los datos del form
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
    }

    public function clientProjects(Client $client)
    {
        $user = Auth::user();
        if(Gate::denies('view', $client))
            {
                abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
            }
        $projects = $client->projects()->get();
        $projects->load('area');

        return inertia('Clients/ProjectsClient', [
            'client' => $client,
            'projects' => $projects,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
        $user = Auth::user();
        if(Gate::denies('update', $client))
            {
                abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
            }
        return inertia('Clients/EditClient', [
            'client' => $client
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        //
        $user = Auth::user();
        if(Gate::denies('delete', $client))
            {
                abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
            }

        try{
            $validated = $request->validate([
                        'name' => 'required|string|min:3|max:100',
                        'email' => 'required|string|email',
                        'phone' => 'required|string|max:20',
                    ]);
            $client->update($validated);
            $project = $client->projects()->first();

            //los datos se guardan en BD
            return redirect()->route('projects.show', $project)->with('success', 'Cliente actualizado.');
        }catch(ValidationException $e){
            throw $e;
        }catch(\Exception $e){
            //si los datos no se guardan, se muestra mensaje de error
            return redirect()->back()->with('error', 'Error al actualizar empresa.')->withInput();//withInput mantiene los datos del form
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        //
        $user = Auth::user();
        if(Gate::denies('delete', $client))
            {
                abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
            }

        $client->delete();
        return redirect()->back()->with('success', 'Cliente eliminado exitosamente.');
    }

    public function assignClient(Project $project)
    {
        if(Gate::denies('create', Client::class))
            {
                abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
            }
        $user = Auth::user();
        if($user->hasRole('admin')){ //ADMIN: Puede ver todos los registros
            $client = Client::all();
        }else{
            $company = $user->companies()->first(); //MANAGER: Solo puede ver los registros de su empresa
            $client = Client::where('company_id', $company->id)->with('projects')->get();
        }

        return inertia('Clients/AssignClient',[
            'project' => $project,
            'client' => $client,
        ]);
    }

    public function attach(Request $request, Project $project)
    {
        if(Gate::denies('create', Client::class))
            {
                abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
            }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id'
        ]);

        $project->clients()->attach($validated);

        return redirect()->route('projects.show', $project)->with('success', 'Cliente vinculado a ' . $project->name);
    }

    public function detach(Client $client, Project $project)
    {
        if(Gate::denies('delete', $client))
            {
                abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
            }

        $client->projects()->detach($project->id);
        return redirect()->route('projects.show', $project)->with('success', 'El cliente ya no esta vinculado a'. $project->name );
    }
}
