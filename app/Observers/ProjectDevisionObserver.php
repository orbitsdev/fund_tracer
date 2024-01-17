<?php

namespace App\Observers;

use App\Models\ProjectDevision;

class ProjectDevisionObserver
{
    /**
     * Handle the ProjectDevision "created" event.
     */
    public function created(ProjectDevision $projectDevision): void
    {
        //
    }

    /**
     * Handle the ProjectDevision "updated" event.
     */
    public function updated(ProjectDevision $projectDevision): void
    {
        //
    }

    /**
     * Handle the ProjectDevision "deleted" event.
     */
    public function deleted(ProjectDevision $projectDevision): void
    {
        $projectDevision->project_division_categories->each(function ($project_division_category) {
            $project_division_category->delete();
        });
        $projectDevision->quarter_expense_budget_divisions->each(function ($quarter_expense_budget_division) {
            $quarter_expense_budget_division->delete();
        });
    }

    /**
     * Handle the ProjectDevision "restored" event.
     */
    public function restored(ProjectDevision $projectDevision): void
    {
        //
    }

    /**
     * Handle the ProjectDevision "force deleted" event.
     */
    public function forceDeleted(ProjectDevision $projectDevision): void
    {
        //
    }
}
