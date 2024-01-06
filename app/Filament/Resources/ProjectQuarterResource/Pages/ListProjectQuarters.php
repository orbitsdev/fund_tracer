<?php

namespace App\Filament\Resources\ProjectQuarterResource\Pages;

use App\Filament\Resources\ProjectQuarterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectQuarters extends ListRecords
{
    protected static string $resource = ProjectQuarterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
