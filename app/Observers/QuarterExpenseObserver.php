<?php

namespace App\Observers;

use App\Models\QuarterExpense;

class QuarterExpenseObserver
{
    /**
     * Handle the QuarterExpense "created" event.
     */
    public function created(QuarterExpense $quarterExpense): void
    {
        //
    }

    /**
     * Handle the QuarterExpense "updated" event.
     */
    public function updated(QuarterExpense $quarterExpense): void
    {
        //
    }

    /**
     * Handle the QuarterExpense "deleted" event.
     */
    public function deleted(QuarterExpense $quarterExpense): void
    {
        //
    }

    /**
     * Handle the QuarterExpense "restored" event.
     */
    public function restored(QuarterExpense $quarterExpense): void
    {
        //
    }

    /**
     * Handle the QuarterExpense "force deleted" event.
     */
    public function forceDeleted(QuarterExpense $quarterExpense): void
    {
        //
    }
}
