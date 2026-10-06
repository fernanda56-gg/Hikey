<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Almacenar nueva actividad en la BD
     */
    public function store(Request $request, Project $project)
    {
        if(Gate::denies('view', [Activity::class, $project]))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'required|string|max:500',
            'priority' => 'required|in:Baja,Media,Alta,Urgente',
            'link' => 'nullable|url',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'completed_at' => 'nullable|date'
        ]);

        try {
            $project->activity()->create([
                $validated,
                'by_user_id' => $request->user()->id,
            ]);

            return redirect()->back()->with('success', 'Actividad creada correctamente.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al generar actividad.')->withInput();//withInput mantiene los datos del form
        }
    }

    /**
     * Muestra el tablero de actividades de un usuario en especifico
     */
    public function show(Project $project)
    {
        if(Gate::denies('viewActivities', $project))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        $user = Auth::user();
        $activities = $project->activity()->with(['responsable', 'user_activity'])->get();

        return inertia('Activities/ActivityBoardPage', [
            'project' => $project,
            'activities' => $activities
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Activity $activity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        //
    }
}
