<?php

namespace App\Filament\Resources\ProgramResource\Pages;

use Filament\Actions;
use App\Models\Program;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProgramResource;

class CreateProgram extends CreateRecord
{
    protected static string $resource = ProgramResource::class;
    protected static bool $canCreateAnother = false;


   

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
  
}
