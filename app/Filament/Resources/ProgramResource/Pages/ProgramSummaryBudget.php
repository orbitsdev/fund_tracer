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
    public $summary_budget=[];
    public function mount($record): void
    {
        static::authorizeResourceAccess();

        $this->record = Program::with(['projects.project_years.project_quarters.project_divisions.project_division_categories.project_division_sub_category_expenses.fourth_layers'])->find($record);

        $this->constructObject($this->record );
    }


    public function constructObject(Program $program){



        foreach($program->projects as $project){

            foreach($project->project_years as $project_year){

                foreach($project_year->project_quarters as $project_quarter){

                    foreach($project_quarter->project_divisions as $project_division){

                        foreach($project_division->project_division_categories as $project_division_category){

                            foreach($project_division_category->project_division_sub_category_expenses as $key => $thirdLayer){
                                // $this->summary_budget['total '.$key] = $thirdLayer->fourth_layers->sum('amount');
                            }
                        }

                    }

                }

            }

        }
        //  dd($this->summary_budget);

    }
}
