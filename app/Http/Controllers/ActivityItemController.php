<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityItem;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Gate;

class ActivityItemController extends Controller
{

    /**
     * Almacena nueva acción para actividad
     */
    public function store(Request $request, Activity $activity)
    {
        if (Gate::denies('update', $activity))
        {
            return redirect()->back()->with('error', 'No tienes permiso para realizar está acción');
        }

        $validated = $request->validate([
            'description' => 'required|string|max:250'
        ]);

        try {
            $activity->items()->create([
                'description' => $validated['description'],
                'order' => ($activity->items()->max('order') ?? 0) + 1 // ? se incrementa el num de actividad
            ]);

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
            return redirect()->back()->with('error', 'No tienes permiso para realizar está acción');
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
            return redirect()->back()->with('error', 'No tienes permiso para realizar está acción');
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
    public function toggle(ActivityItem $item)
    {
        $activity = $item->activity;

        if (Gate::denies('toggleItem', $activity))
        {
            return redirect()->back()->with('error', 'No tienes permiso para realizar está acción');
        }

        if ($activity->status === 'Completado')
        {
            return redirect()->back()->with('error', 'La actividad ya fue marcada como completada.');
        }

        $item->update([
            'is_completed' => !$item->is_completed
        ]);

        return back();
    }
}
