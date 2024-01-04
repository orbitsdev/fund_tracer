<?php

namespace App\Filament\Resources\YearResource\Pages;

use App\Filament\Resources\YearResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListYears extends ListRecords
{
    protected static string $resource = YearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
