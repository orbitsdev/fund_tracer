<?php

namespace App\Filament\Resources\QuarterExpenseBudgetDivisionResource\Pages;

use App\Filament\Resources\QuarterExpenseBudgetDivisionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuarterExpenseBudgetDivisions extends ListRecords
{
    protected static string $resource = QuarterExpenseBudgetDivisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
