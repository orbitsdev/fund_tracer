<?php

namespace App\Observers;

use App\Models\ProjectYear;

class ProjectYearObserver
{
    /**
     * Handle the ProjectYear "created" event.
     */
    public function created(ProjectYear $projectYear): void
    {
        //
    }

    /**
     * Handle the ProjectYear "updated" event.
     */
    public function updated(ProjectYear $projectYear): void
    {
        //
    }

    /**
     * Handle the ProjectYear "deleted" event.
     */
    public function deleted(ProjectYear $projectYear): void
    {
        $projectYear->project_quarters->each(function ($project_quarters) {
            $project_quarters->delete();
        });
    }

    /**
     * Handle the ProjectYear "restored" event.
     */
    public function restored(ProjectYear $projectYear): void
    {
        //
    }

    /**
     * Handle the ProjectYear "force deleted" event.
     */
    public function forceDeleted(ProjectYear $projectYear): void
    {
        //
    }
}
