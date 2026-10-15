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
            $activity->changeStatus_p = $user->can('updateStatus', $activity);
            $activity->review_p = $user->can('review', $activity);
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

    /**
     * Lleva al usuario a la vista TRASH de actividades
     */
    public function trash(Project $project)
    {
        if (Gate::denies('viewTrash', [Activity::class, $project]))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        $user = Auth::user();
        $activities = $project->activity()
                ->onlyTrashed()
                ->with('responsable')
                ->latest('deleted_at')
                ->get();

        $activities->each(function ($activity) use ($user) {
            $activity->restore_a = $user->can('restore', $activity);
            $activity->forceDelete_a = $user->can('forceDelete', $activity);
        });

        return inertia('Activities/ActivityTrashPage', [
            'project' => $project,
            'activities' => $activities
        ]);
    }

    /**
     * Restaura cualquier actividad que se encuentre en TRASH
     */
    public function restore(Activity $activity)
    {
        if(Gate::denies('restore', $activity))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        $projectId = $activity->project_id;
        $activity->restore();

        return redirect()->route('activities-board.show', $projectId)->with('success', 'Actividad restaurada exitosamente.');
    }

    /**
     * Elimina cualquier actividad definitivamente de la BD
     */
    public function forceDelete(Activity $activity)
    {
        if(Gate::denies('forceDelete', $activity))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        $projectId = $activity->project_id;
        $activity->forceDelete();

        return redirect()->route('activities-board.show', $projectId)->with('success', 'Actividad eliminada del sistema.');
    }

    /**
     * Permite realizar cambios en el estatus de la actividad
     */
    public function updateStatus(Request $request, Activity $activity)
    {
        if (Gate::denies('updateStatus', $activity))
        {
            return back()->with('error', 'No tienes permisos para actualizar estatus.');
        }

        $validate = $request->validate([
            'status' => 'required|in:En progreso,Revision'
        ]);

        $allowed = [
            'Disponible' => 'En progreso',
            'En progreso' => 'Revision', // Asegúrate de que en la BD el valor sea exactamente este
        ];

        // ESTO TE DIRÁ EXACTAMENTE QUÉ ESTÁ FALLANDO:
    if (($allowed[$activity->status] ?? null) !== $validate['status']) {
        dd([
            'estado_actual_bd' => $activity->status,
            'estado_permitido_segun_array' => $allowed[$activity->status] ?? 'No existe la clave',
            'estado_enviado_frontend' => $validate['status']
        ]);
    }

        if (($allowed[$activity->status] ?? null) !== $validate['status']) {
            return back()->with('error', 'Error al actualizar estatus.');
        }

        match ($validate['status']) {
            'En progreso' => $activity->markInProgress(),
            'Revision' => $activity->sendToReview()
        };

        return back()->with('success', 'Estatus actualizado.');
    }

    /**
     * Permite que el usuario pueda regresar una actividad o marcarla como completado
     */
    public function returnActivity(Request $request, Activity $activity)
    {
        if (Gate::denies('review', $activity))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        if ($activity->status !== 'Revision') {
            return back()->with('error', 'La actividad aún no esta en revisión.');
        }

        $activity->returnActivity();
        return back()->with('success', 'Actividad devuelta.');
    }

    public function markComplete(Request $request, Activity $activity)
    {
        if (Gate::denies('review', $activity))
        {
            abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
        }

        if ($activity->status !== 'Revision') {
            return back()->with('error', 'La actividad aún no esta en revisión.');
        }

        $activity->markCompletedAt();
        return back()->with('success', 'Actividad marcada como completada');
    }
}
