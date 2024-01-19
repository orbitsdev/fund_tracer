<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Models\Quarter;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Division;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ProjectYear;
use Filament\Support\RawJs;
use App\Models\ProjectQuarter;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action as FAction;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Contracts\HasActions;
use App\Filament\Resources\ProjectResource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Concerns\InteractsWithActions;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;

class ManageQuarter extends Page implements HasForms,  HasActions

{
    use InteractsWithForms;
    use InteractsWithActions;
    use InteractsWithForms;
    //  use InteractsWithTable;
    // use InteractsWithActions;
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.manage-quarter';


    public ?array $data = [];

    public ?ProjectQuarter $project_quarter = null;

    public $record = null;


    public function mount($record): void
    {

        $this->record = ProjectYear::find($record);
        // $this->project_quarter = $this->record->project_quarters->first();
        // dd($this->project_quarter->project_year->id);





        $this->form->fill([
            'project_year_id' => $this->record->id,
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
            ->action(function(){
                dd('test');
            })
            ->url(fn (): string => ProjectResource::getUrl('manage-quarter-year', ['record' => $this->record->project->id]))
            ;
    }


//     public function fillForm(): void
// {

//     $data = $this->project_quarter->attributesToArray();

//      $data = $this->mutateFormDataBeforeFill($data);

//     $this->form->fill($data);
// }

// public function mutateFormDataBeforeFill(array $data): array
// {
//     // STORE TEAMS
//     $data['project_divisions'] = $this->project_quarter->project_divisions()->get()->toArray();
//     // dd($data);

//     return $data;
// }


    public function createQuarter(): FAction
    {
        return FAction::make('save');
    }

    public function create()
    {


        // dd($this->data);
         $project_quarter = ProjectQuarter::create($this->data);



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
        return redirect(ProjectResource::getUrl('manage-quarter-year', ['record' => $this->record->project->id]));
        // dd($this->form->fill($this->data));
        // dd($this->form->getState());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([


                Hidden::make('project_year_id'),


                Select::make('quarter_id')

                // ->required()
                // ->unique( ignoreRecord: true,modifyRuleUsing: function (Unique $rule, Get $get,  Model $record) {
                //     return $rule->where('quarter_id', $get('quarter_id'))->where('project_year_id', $record->id);
                // })
                ->required()
                ->live()
                // ->options(Quarter::pluck('title','id'))
                ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                    return $rule->where('quarter_id', $get('quarter_id'))->where('project_quarters.project_year', $this->record->id);
                })
                ->relationship(
                    name: 'quarter',
                    titleAttribute: 'title',
                    modifyQueryUsing: fn (Builder $query) => $query->whereDoesntHave('project_quarters.project_year', function($query){
                        $query->where('project_year_id', $this->record->id);
                    }),

                )
                ->createOptionForm([
                    TextInput::make('title')
                    ->maxLength(191)
                    ->required()
                    ->unique()
                ])
                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                ->searchable()
                ->label('Select Quarter')
                ->preload()
                ->native(false)
                ->columnSpanFull()
                ->distinct()
                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
            // ->disable()
            // ->disabled()
            ,


                // ...
            ])
            ->statePath('data')
            ->model(ProjectQuarter::class);
    }



}
