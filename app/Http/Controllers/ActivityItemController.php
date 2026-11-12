<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityItem;
use App\Notifications\ActionCompleted;
use App\Notifications\NewAction;
use App\Notifications\UpdateAction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Gate;
use Notification;

class ActivityItemController extends Controller
{

    /**
     * Almacena nueva acción para actividad
     */
    public function store(Request $request, Activity $activity)
    {
        if (Gate::denies('update', $activity))
        {
            abort(403, 'No tienes los permisos necesarios para realizar esta acción.');
        }

        $validated = $request->validate([
            'description' => 'required|string|max:250'
        ]);

        try {
            $activity->items()->create([
                'description' => $validated['description'],
                'order' => ($activity->items()->max('order') ?? 0) + 1 // ? se incrementa el num de actividad
            ]);

        /* notificación */
        Notification::send($activity->users, new NewAction($activity));

            return back()->with('success', 'Acción añadida exitosamente.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar acción.')->withInput();
        }
    }

    /**
     * Actualiza info de una acción
     */
    public function update(Request $request, ActivityItem $item)
    {
        $activity = $item->activity;

        if (Gate::denies('update', $activity))
        {
            abort(403, 'No tienes los permisos necesarios para realizar esta acción.');
        }

        if ($activity->status === 'Completado')
        {
            return redirect()->back()->with('error', 'La actividad ya fue marcada como completada.');
        }

        $validated = $request->validate([
            'description' => 'required|string|max:250'
        ]);

        try {
            $item->update($validated);

            /* notificación */
            Notification::send($activity->users, new UpdateAction($activity));

            return back()->with('success', 'Acción actualizada exitosamente.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar acción.');
        }
    }

    /**
     * Remueve una acción de la actividad
     */
    public function destroy(ActivityItem $item)
    {
        $activity = $item->activity;

        if (Gate::denies('delete', $activity))
        {
            abort(403, 'No tienes los permisos necesarios para realizar esta acción.');
        }

        if ($activity->status === 'Completado')
        {
            return redirect()->back()->with('error', 'La actividad ya fue marcada como completada.');
        }

        $item->delete();

        return back()->with('success', 'Acción eliminada exitosamente');
    }

    /**
     * Marca una actividad como completada
     */
    public function toggle(ActivityItem $item, Request $request)
    {
        $activity = $item->activity;

        if (Gate::denies('toggleItem', $activity))
        {
            abort(403, 'No tienes los permisos necesarios para realizar esta acción.');
        }

        if ($activity->status === 'Completado')
        {
            return redirect()->back()->with('error', 'La actividad ya fue marcada como completada.');
        }

        $item->update([
            'is_completed' => !$item->is_completed
        ]);

        /* notificación */
        Notification::send($activity->users, new ActionCompleted($item, $request->user()));

        return back();
    }
}
