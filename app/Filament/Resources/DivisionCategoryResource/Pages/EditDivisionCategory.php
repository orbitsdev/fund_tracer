<?php

namespace App\Filament\Resources\DivisionCategoryResource\Pages;

use App\Filament\Resources\DivisionCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDivisionCategory extends EditRecord
{
    protected static string $resource = DivisionCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
