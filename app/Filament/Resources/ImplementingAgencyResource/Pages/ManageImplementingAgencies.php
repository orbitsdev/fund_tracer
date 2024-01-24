<?php

namespace App\Filament\Resources\ImplementingAgencyResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\ImplementingAgencyResource;

class ManageImplementingAgencies extends ManageRecords
{
    protected static string $resource = ImplementingAgencyResource::class;
    protected static bool $canCreateAnother = false;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Create New')
            ->modalWidth(MaxWidth::ThreeExtraLarge)->disableCreateAnother()
            ,
        ];
    }
}
