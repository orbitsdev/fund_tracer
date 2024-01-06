<?php

namespace App\Filament\Resources\ProjectQuarterResource\Pages;

use App\Filament\Resources\ProjectQuarterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProjectQuarter extends EditRecord
{
    protected static string $resource = ProjectQuarterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
