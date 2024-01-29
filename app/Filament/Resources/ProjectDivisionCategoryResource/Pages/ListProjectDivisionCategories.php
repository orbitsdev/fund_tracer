<?php

namespace App\Filament\Resources\ProjectDivisionCategoryResource\Pages;

use App\Filament\Resources\ProjectDivisionCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectDivisionCategories extends ListRecords
{
    protected static string $resource = ProjectDivisionCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
