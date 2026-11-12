<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityComments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ActivityCommentController extends Controller
{
    /**
     * Almacena en la BD el nuevo comentario de la actividad
     */
    public function store(Request $request, Activity $activity)
    {
        if (Gate::denies('comments', $activity)) {
            return back()->with('error', 'No tienes permisos para comentar esta actividad.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:500'
        ]);

        try {
            $activity->comments()->create([
                'user_id' => $request->user()->id,
                'content' => $validated['content']
            ]);

            /* notificación */
            // TODO: AGREGAR NOTIFICACIÓN NEW COMMENT

            return redirect()->back()->with('success', 'Comentario publicado.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al publicar comentario.')->withInput();//withInput mantiene los datos del form
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ActivityComments $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ActivityComments $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ActivityComments $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActivityComments $comment)
    {
        //
    }
}
