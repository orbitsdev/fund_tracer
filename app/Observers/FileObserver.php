<?php

namespace App\Observers;

use App\Http\Controllers\FileController;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class FileObserver
{
    /**
     * Handle the File "created" event.
     */
    public function created(File $file): void
    {
        //
    }

    /**
     * Handle the File "updated" event.
     */
    public function updated(File $file): void
    {   
        //  

      
        // FileController::removeFile($file);
        
    }

    /**
     * Handle the File "deleted" event.
     */
    public function deleted(File $file): void
    {
        if(!empty($file->file)){

            if(Storage::disk('public')->exists($file->file)){
                Storage::disk('public')->delete($file->file);
            }
        }
    }

    /**
     * Handle the File "restored" event.
     */
    public function restored(File $file): void
    {
        //
    }

    /**
     * Handle the File "force deleted" event.
     */
    public function forceDeleted(File $file): void
    {
        //
    }
}
