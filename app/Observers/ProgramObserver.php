<?php

namespace App\Observers;

use App\Models\Program;

class ProgramObserver
{
    /**
     * Handle the Program "created" event.
     */
    public function created(Program $program): void
    {
        //
    }

    /**
     * Handle the Program "updated" event.
     */
    public function updated(Program $program): void
    {
        //
    }

    /**
     * Handle the Program "deleted" event.
     */
    public function deleted(Program $program): void
    {
        $program->files->each(function ($file) {
            $file->delete();
        });
    }

    /**
     * Handle the Program "restored" event.
     */
    public function restored(Program $program): void
    {
        //
    }

    /**
     * Handle the Program "force deleted" event.
     */
    public function forceDeleted(Program $program): void
    {
        //
    }
}
