<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityComments;
use App\Notifications\DeletedComment;
use App\Notifications\NewComment;
use App\Notifications\UpdatedComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
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
            if ($activity->users) {
                Notification::send($activity->users, new NewComment($activity, $request->user()));
            }

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
        $activity = $comment->activity;
        if (Gate::denies('update', $comment)) {
            return back()->with('error', 'No tienes permisos para comentar esta actividad.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:500'
        ]);

        try {
            $comment->update($validated);

            /* notificación */
            if ($activity->users) {
                Notification::send($activity->users, new UpdatedComment($activity, $request->user()));
            }

            return back()->with('success', 'Comentario actualizado');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar comentario.');
        }
    }

    /**
     * Remueve un comentario de la actividad
     */
    public function destroy(ActivityComments $comment)
    {
        if (Gate::denies('delete', $comment)) {
            abort(403, 'No tienes los permisos necesarios para realizar esta acción');
        }

        $comment->delete();

        /* notificación */
        if ($comment->author) {
            Notification::send($comment->author, new DeletedComment($comment));
        }

        return back()->with('error', 'Comentario eliminado');
    }
}
// TODO: AÑADIR ACCIONES DE ELIMINAR Y EDITAR COMENTARIOS Y VER COMO HACER QUE SE VEA MEJOR LO DE ACTIVITY_ITEMS
