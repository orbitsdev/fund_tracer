<?php

namespace App\Observers;

use App\Models\ProjectQuarter;

class ProjectQuarterObserver
{
    /**
     * Handle the ProjectQuarter "created" event.
     */
    public function created(ProjectQuarter $projectQuarter): void
    {
        //
    }

    /**
     * Handle the ProjectQuarter "updated" event.
     */
    public function updated(ProjectQuarter $projectQuarter): void
    {
        //
    }

    /**
     * Handle the ProjectQuarter "deleted" event.
     */
    public function deleted(ProjectQuarter $projectQuarter): void
    {
        $projectQuarter->quarter_expense_budget_divisions->each(function ($quarter_expense_budget_division) {
            $quarter_expense_budget_division->delete();
        });
    }

    /**
     * Handle the ProjectQuarter "restored" event.
     */
    public function restored(ProjectQuarter $projectQuarter): void
    {
        //
    }

    /**
     * Handle the ProjectQuarter "force deleted" event.
     */
    public function forceDeleted(ProjectQuarter $projectQuarter): void
    {
        //
    }
}
