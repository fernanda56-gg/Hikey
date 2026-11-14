<?php

namespace App\Policies;

use App\Models\ActivityComments;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ActivityCommentsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ActivityComments $activityComments): bool
    {
        return false;
    }

    /**
     * Determina quien puede editar comentarios
     */
    public function update(User $user, ActivityComments $comments): bool
    {
        // ? solo quien publico el comentario puede editarlo
        return $comments->user_id === $user->id
            && $user->can('comments', $comments->activity);
    }

    /**
     * Determine quien puede editar comentarios
     */
    public function delete(User $user, ActivityComments $comments): bool
    {
        // ! Admin siempre puede, sin importar la empresa
        if($user->hasRole('admin')){
            return true;
        }

        // ? solo quien publico el comentario puede eliminarlo
        return $comments->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ActivityComments $activityComments): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ActivityComments $activityComments): bool
    {
        return false;
    }
}

