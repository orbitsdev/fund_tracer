<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Division;
use Filament\Forms\Form;
use App\Models\FourthLayer;
use Filament\Support\RawJs;
use App\Models\QuarterExpense;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;

use App\Filament\Resources\ProjectResource;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;

class ProjectQuarterBudjet extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected static ?string $title = 'Quarters Expenses';

    protected function mutateFormDataBeforeFill(array $data): array
    {

        // dd($data);
        // $data['user_id'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {

        // $data['last_edited_by_id'] = auth()->id();

        return $data;
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema(
                [

                    Repeater::make('project_years')

                        ->relationship()
                        ->addActionLabel('Year')
                        ->label('Years')

                        ->schema([

                            Select::make('year_id')
                                ->required()
                                ->live()

                                // ->options(Quarter::pluck('title','id'))
                                ->relationship(name: 'year', titleAttribute: 'title')
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                                ->searchable()
                                ->label('Select Year')
                                ->preload()
                                ->native(false)
                                ->columnSpanFull()
                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                            Repeater::make('project_quarters')

                                ->relationship()
                                ->addActionLabel('Quarter')

                                ->label('Quarters')

                                ->schema([
                                    
                                    Select::make('quarter_id')

                                        // ->required()
                                        // ->unique( ignoreRecord: true,modifyRuleUsing: function (Unique $rule, Get $get,  Model $record) {
                                        //     return $rule->where('quarter_id', $get('quarter_id'))->where('project_year_id', $record->id);
                                        // })
                                        ->required()
                                        ->live()
                                        // ->options(Quarter::pluck('title','id'))
                                        ->relationship(
                                            name: 'quarter',
                                            titleAttribute: 'title',
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
                                                    modifyQueryUsing: fn (Builder $query, Get $get, Set $set) => $query->where('project_id', $this->getRecord()->id)
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
                                                                        $query->where('project_id', $this->getRecord()->id);
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
                                                                            $query->where('project_id', $this->getRecord()->id);
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
                                                                            $query->where('project_id', $this->getRecord()->id);
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


                                ])
                                ->visible(fn (Get $get) => !empty($get('year_id')) ? true : false),

                        ])
                        ->columnSpanFull(),
                ]
            );
    }
}
