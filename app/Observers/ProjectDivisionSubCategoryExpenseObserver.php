<?php

namespace App\Observers;

use App\Models\ProjectDivisionSubCategoryExpense;

class ProjectDivisionSubCategoryExpenseObserver
{
    /**
     * Handle the ProjectDivisionSubCategoryExpense "created" event.
     */
    public function created(ProjectDivisionSubCategoryExpense $projectDivisionSubCategoryExpense): void
    {
        //
    }

    /**
     * Handle the ProjectDivisionSubCategoryExpense "updated" event.
     */
    public function updated(ProjectDivisionSubCategoryExpense $projectDivisionSubCategoryExpense): void
    {
        //
    }

    /**
     * Handle the ProjectDivisionSubCategoryExpense "deleted" event.
     */
    public function deleted(ProjectDivisionSubCategoryExpense $projectDivisionSubCategoryExpense): void
    {
        $projectDivisionSubCategoryExpense->fourth_layers->each(function ($fourth_layer) {
            $fourth_layer->delete();
        });
    }

    /**
     * Handle the ProjectDivisionSubCategoryExpense "restored" event.
     */
    public function restored(ProjectDivisionSubCategoryExpense $projectDivisionSubCategoryExpense): void
    {
        //
    }

    /**
     * Handle the ProjectDivisionSubCategoryExpense "force deleted" event.
     */
    public function forceDeleted(ProjectDivisionSubCategoryExpense $projectDivisionSubCategoryExpense): void
    {
        //
    }
}
