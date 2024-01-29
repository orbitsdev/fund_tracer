<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Closure;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\ProjectDevision;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use App\Models\ProjectDivisionCategory;
use Filament\Actions\Action as FAction;
use Illuminate\Validation\Rules\Unique;
use Filament\Notifications\Notification;
use Filament\Actions\Contracts\HasActions;
use App\Filament\Resources\ProjectResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Concerns\InteractsWithActions;

class CreateProjectDivisionCategory extends Page implements HasForms,  HasActions
{

    use InteractsWithForms;
    use InteractsWithActions;
    use InteractsWithForms;
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.create-project-division-category';


    public ?array $data = [];

    public ?ProjectDivisionCategory $project_division_category = null;

    public $record = null;


    public function mount($record): void
    {

        $this->record = ProjectDevision::find($record);

        // dd($this->record);
        // $this->project_quarter = $this->record->project_quarters->first();
        // dd($this->project_quarter->project_year->id);





        $this->form->fill([
            'project_devision_id' => $this->record->id,
            // 'project_year_id' => $this->project_quarter->project_year->id,
        ]);

        // dd($this->form);

        // $this->fillForm();

    }

     public function back(): FAction
    {
        return FAction::make('back')
            ->button()
            ->outlined()
            ->color('gray')

            ->url(fn (): string => ProjectResource::getUrl('project-table-division', ['record' => $this->record->project_id]))
            ;
    }


    public function create()
    {


        // dd($this->data);
         $new_record = ProjectDivisionCategory::create($this->data);



        // Save the relationships from the form to the post after it is created.
        //  $a = $this->form->model($project_quarter)->saveRelationships();
        // dd($a);
        Notification::make()
            ->title('Saved successfully')
            ->icon('heroicon-o-document-text')
            ->iconColor('success')
            ->send();
            $this->form->fill();
        // dd(ProjectResource::getUrl('manage-quarter-year', ['record'=>$this->record->project->id]));
        return redirect(ProjectResource::getUrl('project-table-division', ['record' => $this->record->project->id]));
        // dd($this->form->fill($this->data));
        // dd($this->form->getState());
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([


                Hidden::make('project_devision_id'),


                 Select::make('from')
                            ->label('Costing Type')
                            ->required()
                            ->options([

                                'Direct Cost' => 'Direct Cost',
                                'Indirect Cost' => 'Indirect Cost',
                 ])
                //  ->rules([
                //     fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                //          $exist = $this->record->whereHas('project_division_categories', function($query) use($get){
                //     $query->where('project_devision_id', $this->record->id)->where('from', $get('from'));
                // })->exist();

                // if($exist){
                //     $fail("The {$attribute} is invalid.");
                // }
                //     },
                // ])
                // ->afterStateUpdated(function (HasForms $livewire, $component) {
                //     $livewire->validateOnly($component->getStatePath());
                // }),
                 ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                    return $rule->where('from', $get('from'))->where('project_devision_id', $this->record->id);
                })





                // ...
            ])
            ->statePath('data')
            ->model(ProjectDivisionCategory::class);
    }


}
