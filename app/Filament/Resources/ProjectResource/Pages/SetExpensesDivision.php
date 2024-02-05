<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\ProjectDevision;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Contracts\HasActions;
use App\Filament\Resources\ProjectResource;
use App\Models\ProjectQuarter;
use App\Models\QuarterExpenseBudgetDivision;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;

class SetExpensesDivision extends Page implements HasForms,  HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;
    use InteractsWithForms;
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.set-expenses-division';

    public ?array $data = [];

    public ?ProjectDevision $project_devision = null;

    public $record = null;


    public function mount($record): void
    {

        $this->record = ProjectQuarter::find($record);
        //  dd($this->record);
        // $this->project_quarter = $this->record->project_quarters->first();
        // dd($this->project_quarter->project_year->id);





        $this->form->fill([
            'project_quarter_id' => $this->record->id,
            // 'project_year_id' => $this->project_quarter->project_year->id,
        ]);

        // dd($this->form);

        // $this->fillForm();

    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('project_quarter_id'),
            ])
            ->statePath('data')
            ->model(ProjectDevision::class);
    }

}
