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

    public function back(): FAction
    {
        return FAction::make('back')
            ->requiresConfirmation()
            ->action(function(){
                dd('test');
            });
    }

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

        $project_quarter = ProjectQuarter::create($this->form->getState());



        // Save the relationships from the form to the post after it is created.
         $a = $this->form->model($project_quarter)->saveRelationships();
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
            Repeater::make('quarter_expense_budget_divisions')

                ->relationship()

                ->label('Budget Divisions')
                ->addActionLabel('Budget Division')
                ->schema([
                    Select::make('project_devision_id')
                        ->required()
                        ->required()
                        ->live()
                        ->relationship(
                            name: 'project_division',
                            titleAttribute: 'title',
                            modifyQueryUsing: fn (Builder $query, Get $get, Set $set) => $query->where('project_id', $this->record->id)
                        )
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->division->title}")
                        ->searchable()
                        ->label('Division')
                        ->preload()
                        ->native(false)
                        ->columnSpanFull()
                        ->distinct()
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems(),



                    Repeater::make('direct_cost_expenses')
                    ->addActionLabel('Direct Cost')
                        ->relationship(
                            'quarter_expenses',
                            modifyQueryUsing: fn (Builder $query, Get $get, Set $set) =>

                            $query->whereHas('fourth_layer.project_division_sub_category_expense.project_division_category', function ($query) {
                                $query->where('from', 'Direct Cost');
                            })

                            //    $f = QuarterExpense::first();

                            //    dd($f->fourth_layer->project_division_sub_category_expense->project_division_category);

                            // $find = QuarterExpense::find($state);
                            // dd($find->fourth_layer->project_division_sub_category_expense->project_division_category->from);

                        )
                        ->label('Direct Cost Expenses')
                        ->columns([
                            'sm' => 3,
                            'xl' => 6,
                            '2xl' => 8,
                        ])
                        ->schema([


                            Select::make('fourth_layer_id')
                                ->required()
                                ->required()
                                ->live()
                                ->relationship(
                                    name: 'fourth_layer',
                                    titleAttribute: 'title',
                                    modifyQueryUsing: fn (Builder $query, Get $get, Set $set) =>

                                    $query->whereHas('project_division_sub_category_expense.project_division_category', function ($query) use ($get, $set) {
                                        $query->where('from', 'Direct Cost')
                                            ->where('project_devision_id', $get('../../project_devision_id'))
                                            ->whereHas('project_devision', function ($query) {
                                                $query->where('project_id', $this->record->id);
                                            });
                                    }),
                                )
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                                ->searchable()
                                ->label('Expenses')
                                ->preload()
                                ->native(false)
                                ->columnSpan(4)

                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->afterStateUpdated(function (Get $get, Set $set, $state) {

                                    //    $f = QuarterExpense::first();

                                    //    dd($f->fourth_layer->project_division_sub_category_expense->project_division_category);

                                    // $find = QuarterExpense::find($state);
                                    // dd($find->fourth_layer->project_division_sub_category_expense->project_division_category->from);
                                }),

                            TextInput::make('amount')

                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')

                                // ->mask(RawJs::make('$money($input)'))
                                // ->stripCharacters(',')
                                ->prefix('₱')
                                ->numeric()
                                // ->maxValue(9999999999)
                                ->default(0)
                                ->columnSpan(4)
                                ->required(),

                        ])
                        ->collapsible()

                        ->columnSpanFull()
                        ->visible(fn (Get $get) => !empty($get('project_devision_id')) ? true : false),

                    Repeater::make('indirect_cost_expenses_sksu')
                    ->addActionLabel('IC SKSU')

                        ->relationship(
                            'quarter_expenses',
                            modifyQueryUsing: fn (Builder $query, Get $get, Set $set) =>

                            $query->whereHas('fourth_layer.project_division_sub_category_expense', function ($query) {
                                $query
                                    ->where('parent_title', 'SKSU')
                                    ->whereHas('project_division_category', function ($query) {
                                        $query->where('from', 'Indirect Cost');
                                    });
                            })
                        )
                        ->label('Indrect Cost Expenses SKSU')
                        ->columns([
                            'sm' => 3,
                            'xl' => 6,
                            '2xl' => 8,
                        ])
                        ->schema([
                            Select::make('fourth_layer_id')
                                ->required()
                                ->required()
                                ->live()
                                ->relationship(

                                    name: 'fourth_layer',
                                    titleAttribute: 'title',

                                    modifyQueryUsing: fn (Builder $query, Get $get, Set $set) =>

                                    $query->whereHas('project_division_sub_category_expense',function($query)use ($get ,$set){

                                        $query->where('parent_title', 'SKSU')

                                        ->whereHas('project_division_category', function ($query) use ($get, $set) {
                                            $query->where('from', 'Indirect Cost')
                                                ->where('project_devision_id', $get('../../project_devision_id'))
                                                ->whereHas('project_devision', function ($query) {
                                                    $query->where('project_id', $this->record->id);
                                                });
                                        });
                                    }),
                                )
                                ->getOptionLabelFromRecordUsing(function (Model $record) {
                                    return $record->title;
                                })
                                ->searchable()
                                ->label('Expenses')
                                ->preload()
                                ->native(false)
                                ->columnSpan(4)

                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                            TextInput::make('amount')

                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')


                                ->prefix('₱')
                                ->numeric()
                                // ->maxValue(9999999999)
                                ->default(0)
                                ->columnSpan(4)
                                ->required(),

                        ])
                        ->collapsible()

                        ->columnSpanFull()
                        ->visible(fn (Get $get) => !empty($get('project_devision_id')) ? true : false),
                    Repeater::make('indirect_cost_expenses_pcaarrd')
                    ->addActionLabel('IC PCAARRD')
                        ->relationship(
                            'quarter_expenses',
                            modifyQueryUsing: fn (Builder $query, Get $get, Set $set) =>

                            $query->whereHas('fourth_layer.project_division_sub_category_expense', function ($query) {
                                $query
                                    ->where('parent_title', 'PCAARRD')
                                    ->whereHas('project_division_category', function ($query) {
                                        $query->where('from', 'Indirect Cost');
                                    });
                            })
                        )
                        ->label('Indrect Cost Expenses PCAARRD')
                        ->columns([
                            'sm' => 3,
                            'xl' => 6,
                            '2xl' => 8,
                        ])
                        ->schema([
                            Select::make('fourth_layer_id')
                                ->required()
                                ->required()
                                ->live()
                                ->relationship(

                                    name: 'fourth_layer',
                                    titleAttribute: 'title',

                                    modifyQueryUsing: fn (Builder $query, Get $get, Set $set) =>

                                    $query->whereHas('project_division_sub_category_expense',function($query)use ($get ,$set){

                                        $query->where('parent_title', 'PCAARRD')

                                        ->whereHas('project_division_category', function ($query) use ($get, $set) {
                                            $query->where('from', 'Indirect Cost')
                                                ->where('project_devision_id', $get('../../project_devision_id'))
                                                ->whereHas('project_devision', function ($query) {
                                                    $query->where('project_id', $this->record->id);
                                                });
                                        });
                                    }),
                                )
                                ->getOptionLabelFromRecordUsing(function (Model $record) {
                                    return $record->title;
                                })
                                ->searchable()
                                ->label('Expenses')
                                ->preload()
                                ->native(false)
                                ->columnSpan(4)

                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                            TextInput::make('amount')

                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')


                                ->prefix('₱')
                                ->numeric()
                                // ->maxValue(9999999999)
                                ->default(0)
                                ->columnSpan(4)
                                ->required(),

                        ])
                        ->collapsible()

                        ->columnSpanFull()
                        ->visible(fn (Get $get) => !empty($get('project_devision_id')) ? true : false),

                ])
                ->columnSpanFull()
                ->visible(fn (Get $get) => !empty($get('quarter_id')) ? true : false),


                // ...
            ])
            ->statePath('data')
            ->model(ProjectQuarter::class);
    }



}
