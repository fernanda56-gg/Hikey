<?php

namespace App\Observers;

use App\Models\Project;
use App\Services\ProjectTeamService;

class ProjectObserver
{
    /**
     * Handle the Project "created" event.
     */
    public function created(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "updated" event.
     */
    public function updated(Project $project): void
    {
        // * verifica el status del proyecto si el proyecto tiene estatus de completado se debe de actualizar el rol de spatie

        if($project->wasChanged('status')){
            $leaders = $project->users()->wherePivot('role', 'Lider')->get();
            $teamService = app(ProjectTeamService::class);

            foreach($leaders as $leader){
                $teamService->syncLeaderRole($leader);
            }
        }
    }

    /**
     * Handle the Project "deleted" event.
     */
    public function deleted(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "restored" event.
     */
    public function restored(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "force deleted" event.
     */
    public function forceDeleted(Project $project): void
    {
        //
    }
}
