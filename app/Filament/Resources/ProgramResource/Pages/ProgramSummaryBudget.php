<?php

namespace App\Filament\Resources\ProgramResource\Pages;

use App\Filament\Resources\ProgramResource;
use Filament\Resources\Pages\Page;

class ProgramSummaryBudget extends Page
{
    protected static string $resource = ProgramResource::class;

    protected static string $view = 'filament.resources.program-resource.pages.program-summary-budget';

    public function mount(): void
    {
        static::authorizeResourceAccess();
    }
}
