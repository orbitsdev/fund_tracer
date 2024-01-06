<?php

namespace App\Filament\Resources\ProjectYearResource\Pages;

use App\Filament\Resources\ProjectYearResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProjectYear extends EditRecord
{
    protected static string $resource = ProjectYearResource::class;
    protected function getRedirectUrl(): string
    {
    return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
