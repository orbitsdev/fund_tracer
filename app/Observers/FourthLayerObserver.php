<?php

namespace App\Observers;

use App\Models\FourthLayer;

class FourthLayerObserver
{
    /**
     * Handle the FourthLayer "created" event.
     */
    public function created(FourthLayer $fourthLayer): void
    {
        //
    }

    /**
     * Handle the FourthLayer "updated" event.
     */
    public function updated(FourthLayer $fourthLayer): void
    {
        //
    }

    /**
     * Handle the FourthLayer "deleted" event.
     */
    public function deleted(FourthLayer $fourthLayer): void
    {
        $fourthLayer->quarter_expenses->each(function ($quarter_expense) {
            $quarter_expense->delete();
        });
    }

    /**
     * Handle the FourthLayer "restored" event.
     */
    public function restored(FourthLayer $fourthLayer): void
    {
        //
    }

    /**
     * Handle the FourthLayer "force deleted" event.
     */
    public function forceDeleted(FourthLayer $fourthLayer): void
    {
        //
    }
}
