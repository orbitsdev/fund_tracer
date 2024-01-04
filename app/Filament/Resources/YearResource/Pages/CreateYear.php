<?php

namespace App\Filament\Resources\YearResource\Pages;

use App\Filament\Resources\YearResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateYear extends CreateRecord
{
    protected static string $resource = YearResource::class;
    
    protected static bool $canCreateAnother = false;
    
    protected function getRedirectUrl(): string
    {
    return $this->getResource()::getUrl('index');
    }

}
