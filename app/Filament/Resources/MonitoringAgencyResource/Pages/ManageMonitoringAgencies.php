<?php

namespace App\Filament\Resources\MonitoringAgencyResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\MonitoringAgencyResource;

class ManageMonitoringAgencies extends ManageRecords
{
    protected static string $resource = MonitoringAgencyResource::class;
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
