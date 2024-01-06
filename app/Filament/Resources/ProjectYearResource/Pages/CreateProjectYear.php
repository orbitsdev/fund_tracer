<?php

namespace App\Filament\Resources\ProjectYearResource\Pages;

use App\Filament\Resources\ProjectYearResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectYear extends CreateRecord
{
    protected static string $resource = ProjectYearResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
    return $this->getResource()::getUrl('index');
    }
}
