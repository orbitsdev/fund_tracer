<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\ProjectResource;
use App\Models\Program;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Action;
class ManageQuarter extends Page
{
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.manage-quarter';

    use InteractsWithForms;
    use InteractsWithActions;

    public ?array $data = [];

    public ?Project $record = null;



    public function fillForm(): void
    {
        $data = $this->record->attributesToArray();

        $data = $this->mutateFormDataBeforeFill($data);

        $this->form->fill($data);
    }

    public function mutateFormDataBeforeFill(array $data): array
    {
        // STORE TEAMS
        $data['project_years'] = $this->record->project_years()->get()->toArray();

        return $data;
    }

    public function createQuarter(): Action
    {
        return Action::make('create')
            ->action(function(){
                $this->create();
            });
    }
    public function mount($record): void
    {

        $this->record =  Project::find($record);
        $this->fillForm();
        // dd($record);
          $this->form->fill();
    }

     public function form(Form $form): Form
    {
        return $form
            ->schema([

            ])
            ->model($this->record)
            ->statePath('data');
    }


    public function create(): void
    {
        dd($this->form->getState());
    }


}
