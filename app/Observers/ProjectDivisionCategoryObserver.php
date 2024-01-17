<?php

namespace App\Observers;

use App\Models\ProjectDivisionCategory;

class ProjectDivisionCategoryObserver
{
    /**
     * Handle the ProjectDivisionCategory "created" event.
     */
    public function created(ProjectDivisionCategory $projectDivisionCategory): void
    {
        //
    }

    /**
     * Handle the ProjectDivisionCategory "updated" event.
     */
    public function updated(ProjectDivisionCategory $projectDivisionCategory): void
    {
        //
    }

    /**
     * Handle the ProjectDivisionCategory "deleted" event.
     */
    public function deleted(ProjectDivisionCategory $projectDivisionCategory): void
    {
        $projectDivisionCategory->project_division_sub_category_expenses->each(function ($project_division_sub_category_expense) {
            $project_division_sub_category_expense->delete();
        });

        // $projectDivisionCategory->quarter_expenses->each(function ($quarter_expense) {
        //     $quarter_expense->delete();
        // });
    }

    /**
     * Handle the ProjectDivisionCategory "restored" event.
     */
    public function restored(ProjectDivisionCategory $projectDivisionCategory): void
    {
        //
    }

    /**
     * Handle the ProjectDivisionCategory "force deleted" event.
     */
    public function forceDeleted(ProjectDivisionCategory $projectDivisionCategory): void
    {
        //
    }
}
