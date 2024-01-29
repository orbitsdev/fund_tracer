<?php

namespace App\Filament\Resources\ProjectDivisionCategoryResource\Pages;

use App\Filament\Resources\ProjectDivisionCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProjectDivisionCategory extends EditRecord
{
    protected static string $resource = ProjectDivisionCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
