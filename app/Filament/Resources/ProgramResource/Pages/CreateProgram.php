<?php

namespace App\Filament\Resources\ProgramResource\Pages;

use App\Filament\Resources\ProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProgram extends CreateRecord
{
    protected static string $resource = ProgramResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
  
}
