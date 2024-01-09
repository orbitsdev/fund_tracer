<?php

namespace App\Filament\Resources\ProgramResource\Pages;

use App\Models\Program;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\ProgramResource;

class ProgramSummaryBudget extends Page
{
    protected static string $resource = ProgramResource::class;

    protected static string $view = 'filament.resources.program-resource.pages.program-summary-budget';

    public $record;
    public function mount($record): void
    {
        static::authorizeResourceAccess();

        $this->record = Program::with(['projects.project_years.project_quarters.project_divisions.project_division_categories.project_division_sub_category_expenses.fourth_layers'])->find($record);
    }


    public function constructObject(Program $program){


        $computation = [
            'program_title' => $program->title,
            'projects'=> $projects

        ];

    }
}
