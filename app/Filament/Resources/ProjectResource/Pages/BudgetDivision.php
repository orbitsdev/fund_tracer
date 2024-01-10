<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Models\Project;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Contracts\HasActions;
use App\Filament\Resources\ProjectResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Form;

class BudgetDivision extends Page implements HasForms,  HasActions

{
    use InteractsWithForms;
    use InteractsWithActions;
    use InteractsWithForms;
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.budget-division';


    public ?array $data = [];

    public ?Project $project_quarter = null;

    public $record = null;



    public function mount($record): void
    {
        static::authorizeResourceAccess();
        $this->record  = Project::find($record);

        $this->form->fill([
            'project_id' => $this->record->id,
            // 'project_year_id' => $this->project_quarter->project_year->id,
        ]);


    }
    public function create()
    {

        // $project = ProjectQuarter::create($this->form->getState());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

            ])
            ->statePath('data')
            ->model(Project::class);
            ;
        }
}
