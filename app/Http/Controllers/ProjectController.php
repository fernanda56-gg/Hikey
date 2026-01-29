<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia(
            'Project/IndexProject',
            [
                'projects' => Project::with('area')->get(),
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
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
        $validated = $request->validate([
                    'name' => 'required|string|min:3|max:50',
                    'description' => 'required|string|max:255',
                    'link' => 'required|url',
                    'image_path' => 'required|url',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'status' => 'required|string',
                    'area_id' => 'required|integer|exists:areas,id',
        ]);
        $company = $request->user()->companies()->first();
        $validated['company_id'] = $company->id ?? null;

        $request->user()->projects()->create($validated);

            //los datos se guardan en BD
            return redirect()->route('projects.index')->with('success', 'Proyecto creado con éxito.');
        }catch(ValidationException $e){
            throw $e;
        }catch(\Exception $e){
            //si los datos no se guardan, se muestra mensaje de error
            return redirect()->back()->with('error', 'Error al generar proyecto.')->withInput();//withInput mantiene los datos del form

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load('area');
        return inertia(
            'Project/ShowProject',
            [
                'project' => $project,
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
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'status' => 'required|string',
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
