<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
    

    static function removeFile(File $file){
       
        if(empty($file->file)){
     
            if(!empty($file->file_name)){
                $file->file_name = null;
            }
            if(!empty($file->file_type)){
                $file->file_type = null;
            }
            if(!empty($file->file_size)){
                $file->file_size = null;
            }

            $file->save();
        }
    }


}
