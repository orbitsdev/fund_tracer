<?php

namespace App\Filament\Resources\ProjectYearResource\Pages;

use App\Filament\Resources\ProjectYearResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectYears extends ListRecords
{
    protected static string $resource = ProjectYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
