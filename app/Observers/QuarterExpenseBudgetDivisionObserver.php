<?php

namespace App\Observers;

use App\Models\QuarterExpenseBudgetDivision;

class QuarterExpenseBudgetDivisionObserver
{
    /**
     * Handle the QuarterExpenseBudgetDivision "created" event.
     */
    public function created(QuarterExpenseBudgetDivision $quarterExpenseBudgetDivision): void
    {
        //
    }

    /**
     * Handle the QuarterExpenseBudgetDivision "updated" event.
     */
    public function updated(QuarterExpenseBudgetDivision $quarterExpenseBudgetDivision): void
    {
        //
    }

    /**
     * Handle the QuarterExpenseBudgetDivision "deleted" event.
     */
    public function deleted(QuarterExpenseBudgetDivision $quarterExpenseBudgetDivision): void
    {
        $quarterExpenseBudgetDivision->quarter_expenses->each(function ($quarter_expense) {
            $quarter_expense->delete();
        });
    }

    /**
     * Handle the QuarterExpenseBudgetDivision "restored" event.
     */
    public function restored(QuarterExpenseBudgetDivision $quarterExpenseBudgetDivision): void
    {
        //
    }

    /**
     * Handle the QuarterExpenseBudgetDivision "force deleted" event.
     */
    public function forceDeleted(QuarterExpenseBudgetDivision $quarterExpenseBudgetDivision): void
    {
        //
    }
}
