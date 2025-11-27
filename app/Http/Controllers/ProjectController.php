<?php

namespace App\Http\Controllers;
use App\Models\Project;
use Illuminate\Http\Request;
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
                'projects' => Project::all()
            ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Project/CreateProject');
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
        ]);
        //Project::create($validated);
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
        return inertia(
            'Project/ShowProject',
            [
                'project' => $project
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return inertia(
            'Project/EditProject',
            [
                'project' => $project
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
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Proyecto eliminado.');
    }
}
