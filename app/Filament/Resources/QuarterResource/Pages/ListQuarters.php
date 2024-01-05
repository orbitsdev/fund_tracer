<?php

namespace App\Filament\Resources\QuarterResource\Pages;

use App\Filament\Resources\QuarterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuarters extends ListRecords
{
    protected static string $resource = QuarterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
