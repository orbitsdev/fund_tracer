<?php

namespace App\Filament\Resources\DivisionCategoryResource\Pages;

use App\Filament\Resources\DivisionCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDivisionCategories extends ListRecords
{
    protected static string $resource = DivisionCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
