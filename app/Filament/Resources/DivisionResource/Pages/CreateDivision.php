<?php

namespace App\Filament\Resources\DivisionResource\Pages;

use App\Filament\Resources\DivisionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDivision extends CreateRecord
{
    protected static string $resource = DivisionResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
}
