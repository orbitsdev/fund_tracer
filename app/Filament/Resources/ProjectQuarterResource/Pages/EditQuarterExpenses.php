<?php

namespace App\Filament\Resources\ProjectQuarterResource\Pages;

use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Support\RawJs;
use Filament\Actions\Action;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\ProjectQuarterResource;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;

class EditQuarterExpenses extends EditRecord
{
    protected static string $resource = ProjectQuarterResource::class;




    public function getHeader(): ?View
    {
        return view('filament.settings.custom-header', ['title' => 'Edit Project Quarter', 'first' => 'Project Quarters', 'second' => 'edit']);
    }


    protected function getRedirectUrl(): string
    {
        return ProjectResource::getUrl('quarter-list', ['record' => $this->getRecord()]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(
                [

                    Select::make('quarter_id')

                        // ->required()
                        ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get,  Model $record) {
                            return $rule->where('quarter_id', $get('quarter_id'))->where('project_year_id', $record->id);
                        })

                        ->live()
                        // ->options(Quarter::pluck('title','id'))
                        ->relationship(name: 'quarter', titleAttribute: 'title')
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                        ->searchable()
                        ->label('Quarter')
                        ->preload()
                        ->native(false)
                        ->columnSpanFull()
                        ->distinct()
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        // ->disable()
                        ->disabled(),


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
                                    modifyQueryUsing: fn (Builder $query, Get $get, Set $set) => $query->where('project_id', $this->getRecord()->project_year->project_id)
                                )
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->division->title}")
                                ->searchable()
                                ->label('Division')
                                ->preload()
                                ->native(false)
                                ->columnSpanFull()
                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),



                            TableRepeater::make('direct_cost_expenses')
                                ->withoutHeader()
                                ->addActionLabel('Direct Cost')
                                ->relationship(
                                    'quarter_expenses',
                                    modifyQueryUsing: fn (Builder $query, Get $get, Set $set) =>

                                    $query->whereHas('fourth_layer.project_division_sub_category_expense.project_division_category', function ($query) {
                                        $query->where('from', 'Direct Cost');
                                    })



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
                                            // modifyQueryUsing: fn (Builder $query, Get $get, Set $set) =>

                                            // $query->whereHas('project_division_sub_category_expense.project_division_category', function ($query) use ($get) {
                                            //     $query->where('from', 'Direct Cost');
                                            // }),

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
                                ->visible(function (Get $get, $record) {

                                    if (!empty($get('project_devision_id')) && $this->getRecord()->project_year->project->project_divisions()->whereHas('project_division_categories', function ($query) {
                                        $query->where('from', 'Direct Cost');
                                    })->exists()) {
                                        return true;
                                    } else {
                                        return false;
                                    }
                                }),

                            TableRepeater::make('indirect_cost_expenses_sksu')
                                ->withoutHeader()
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

                                            $query->whereHas('project_division_sub_category_expense', function ($query) use ($get, $set) {

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
                                ->visible(function (Get $get, $record) {

                                    if (!empty($get('project_devision_id')) && $this->getRecord()->project_year->project->project_divisions()->whereHas('project_division_categories', function ($query) {
                                        $query->where('from', 'Indirect Cost')->whereHas('project_division_sub_category_expenses', function ($query) {
                                            $query->where('parent_title', 'SKSU');
                                        });
                                    })->exists()) {
                                        return true;
                                    } else {
                                        return false;
                                    }
                                }),

                            TableRepeater::make('indirect_cost_expenses_pcaarrd')
                                ->columnWidths([
                                    'file' => '200px',
                                ])
                                ->withoutHeader()
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
                                    '2xl' => 9,
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

                                            $query->whereHas('project_division_sub_category_expense', function ($query) use ($get, $set) {

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
                                        ->columnSpan(3)

                                        ->distinct()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                    TextInput::make('amount')

                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')


                                        ->prefix('₱')
                                        ->numeric()
                                        // ->maxValue(9999999999)
                                        ->default(0)
                                        ->columnSpan(3)
                                        ->required(),
                                    // FileUpload::make('file')
                                    // ->columnSpan(3)
                                    // ->disk('public')
                                    // ->directory('quarter-expenses')
                                ])
                                ->collapsible()

                                ->columnSpanFull()
                                ->visible(function (Get $get) {

                                    if (!empty($get('project_devision_id')) && $this->getRecord()->project_year->project->project_divisions()->whereHas('project_division_categories', function ($query) {
                                        $query->where('from', 'Indirect Cost')->whereHas('project_division_sub_category_expenses', function ($query) {
                                            $query->where('parent_title', 'PCAARRD');
                                        });
                                    })->exists()) {
                                        return true;
                                    } else {
                                        return false;
                                    }
                                }),



                        ])
                        ->columnSpanFull()

                        ->visible(function (Get $get, Model $record) {
                            return !empty($get('quarter_id')) && $this->getRecord()->project_year &&
                                $this->getRecord()->project_year->project &&
                                $this->getRecord()->project_year->project->project_divisions->isNotEmpty();
                        }),


                ]
            );
    }
}
