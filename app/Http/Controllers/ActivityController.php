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
     * Almacenar nueva actividad en la BD
     */
    public function store(Request $request, Project $project)
    {
        if(Gate::denies('viewActivities', $project))
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
                ...$validated,
                'by_user_id' => $request->user()->id,
            ]);

            return redirect()->back()->with('success', 'Actividad creada con éxito.');
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
        $activities = $project->activity()->with(['responsable', 'user_activity', 'items', 'comments.author'])->get();

        $activities->each(function ($activity) use ($user) {
            $activity->update_p = $user->can('update', $activity);
        });

        return inertia('Activities/ActivityBoardPage', [
            'project' => $project,
            'activities' => $activities
        ]);
    }

    /**
     * Actualiza la info de actividades
     */
    public function update(Request $request, Activity $activity)
    {
        if (Gate::denies('update', $activity))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'required|string|max:500',
            'priority' => 'required|in:Baja,Media,Alta,Urgente',
            'link' => 'nullable|url',
            'due_date' => 'nullable|date'
        ]);

        try {
            $activity->update($validated);

            return redirect()->back()->with('success', 'Información de actividad actualizada exitosamente.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar actividad.')->withInput();//withInput mantiene los datos del form
        }
    }

    /**
     * Elimina una actividad (deleted_at)
     */
    public function destroy(Activity $activity)
    {
        if (Gate::denies('delete', $activity))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        $projectId = $activity->project_id;
        $activity->delete();

        return redirect()->route('activities-board.show', $projectId)->with('success', 'Actividad eliminada.');
    }
}
