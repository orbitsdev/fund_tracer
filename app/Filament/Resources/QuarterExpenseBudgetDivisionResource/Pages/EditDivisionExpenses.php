<?php

namespace App\Filament\Resources\QuarterExpenseBudgetDivisionResource\Pages;

use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Support\RawJs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use App\Filament\Resources\QuarterExpenseBudgetDivisionResource;

class EditDivisionExpenses extends EditRecord

{
    protected static string $resource = QuarterExpenseBudgetDivisionResource::class;
    protected function mutateFormDataBeforeFill(array $data): array
    {

        // dd($this->getRecord()->project_quarter->project_year);
        $data['project_division'] = $this->getRecord()->project_division->division->title;

        return $data;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(
                [

                    TextInput::make('project_division')->disabled()
                        ->columnSpanFull(),
                    TableRepeater::make('direct_cost_expenses')
                        // ->deleteAction(
                        //     fn (Action $action) => $action->after(fn (Get $get, Set $set) =>   self::updateTotal($get, $set, $this->getRecord())),
                        // )
                        ->live()
                        ->debounce(900)
                        ->emptyLabel('No Data')
                        ->withoutHeader()
                        ->columnWidths([
                            'fourth_layer_id' => '200px',
                            'amount' => '200px',
                        ])
                        ->addActionLabel('Direct Cost')
                        ->relationship(
                            'quarter_expenses',
                            modifyQueryUsing: fn (Builder $query, Get $get, Set $set) =>

                            $query->whereHas('fourth_layer.project_division_sub_category_expense.project_division_category', function ($query) {
                                $query->where('from', 'Direct Cost');
                            })



                        )
                        ->label('Direct Cost')
                        ->columns([
                            'sm' => 3,
                            'xl' => 6,
                            '2xl' => 8,
                        ])
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            // self::updateTotal($get, $set, $this->getRecord());
                        })
                        ->schema([

                            Select::make('fourth_layer_id')
                                ->required()
                                ->required()
                                ->live()
                                ->relationship(
                                    name: 'fourth_layer',
                                    titleAttribute: 'title',
                                    //  modifyQueryUsing: fn (Builder $query, Get $get, Set $set) =>

                                    // // $query->whereHas('project_division_sub_category_expense.project_division_category', function ($query) use ($get) {
                                    // //     $query->where('from', 'Direct Cost');
                                    // // }),

                                    modifyQueryUsing: fn (Builder $query, Get $get, Set $set) =>

                                    $query->whereHas('project_division_sub_category_expense.project_division_category', function ($query) use ($get, $set) {
                                        $query->where('from', 'Direct Cost')
                                            ->where('project_devision_id', $this->getRecord()->project_division->id)
                                            ->whereHas('project_devision', function ($query) {
                                                $query->where('project_id', $this->getRecord()->project_quarter->project_year->project_id);
                                            });
                                    }),
                                )
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                                ->searchable()
                                ->label('Expenses')
                                ->preload()
                                ->native(false)
                                ->columnSpanFull()
                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                }),


                            TextInput::make('amount')

                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')
                                ->live()
                                ->debounce(900)
                                ->afterStateUpdated(function (Get $get, Set $set) {

                                    // self::updateTotal($get, $set, $this->getRecord());
                                })

                                // ->mask(RawJs::make('$money($input)'))
                                // ->stripCharacters(',')
                                ->prefix('₱')
                                ->inputMode('integer')
                                ->numeric()

                                // ->maxValue(9999999999)
                                ->default(0)
                                ->columnSpanFull()
                                ->required(),

                        ])
                        ->collapsible()
                        ->columnSpanFull()
                            ->visible(function (Get $get, $record) {

                                if (!empty($get('project_division')) && $this->getRecord()->project_quarter->project_year->project->project_divisions()->whereHas('project_division_categories', function ($query) {
                                    $query->where('from', 'Direct Cost');
                                })->exists()) {
                                    return true;
                                } else {
                                    return false;
                                }
                            })
                    ,


                    TableRepeater::make('indirect_cost_expenses_sksu')

                        ->columnWidths([
                            'fourth_layer_id' => '200px',
                            'amount' => '200px',
                        ])
                        ->withoutHeader()
                        ->emptyLabel('No Data')
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
                        ->label('Indrect Cost SKSU')
                        ->columns([
                            'sm' => 3,
                            'xl' => 6,
                            '2xl' => 8,
                        ])
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            // self::updateTotal($get, $set, $this->getRecord());
                        })
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
                                                    ->where('project_devision_id', $this->getRecord()->project_division->id)
                                                    ->whereHas('project_devision', function ($query) {
                                                        $query->where('project_id', $this->getRecord()->project_quarter->project_year->project_id);
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
                                ->live()
                                ->debounce(900)
                                ->afterStateUpdated(function (Get $get, Set $set) {

                                    // self::updateTotal($get, $set, $this->getRecord());
                                })

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

                        if (!empty($get('project_division')) && $this->getRecord()->project_quarter->project_year->project->project_divisions()->whereHas('project_division_categories', function ($query) {
                            $query->where('from', 'Indirect Cost')->whereHas('project_division_sub_category_expenses', function ($query) {
                                $query->where('parent_title', 'SKSU');
                            });
                        })->exists()) {
                            return true;
                        } else {
                            return false;
                        }
                    })
                    ,


                    TableRepeater::make('indirect_cost_expenses_pcaarrd')
                        // ->deleteAction(
                        //     fn (RAction $action) => $action->requiresConfirmation(),
                        // )
                        ->emptyLabel('No Data')
                        ->columnWidths([
                            'fourth_layer_id' => '200px',
                            'amount' => '200px',
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
                        ->label('Indrect Cost PCAARRD')
                        ->columns([
                            'sm' => 3,
                            'xl' => 6,
                            '2xl' => 8,
                        ])
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            // self::updateTotal($get, $set, $this->getRecord());
                        })
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
                                                    ->where('project_devision_id', $this->getRecord()->project_division->id)
                                                    ->whereHas('project_devision', function ($query) {
                                                        $query->where('project_id', $this->getRecord()->project_quarter->project_year->project_id);
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

                                ->live()
                                ->debounce(900)
                                ->afterStateUpdated(function (Get $get, Set $set) {

                                    // self::updateTotal($get, $set, $this->getRecord());
                                })
                                ->prefix('₱')
                                ->numeric()
                                // ->maxValue(9999999999)
                                ->default(0)
                                ->columnSpan(4)
                                ->required(),
                            // FileUpload::make('file')
                            // ->columnSpan(3)
                            // ->disk('public')
                            // ->directory('quarter-expenses')
                        ])
                        ->collapsible()

                        ->columnSpanFull()
                        ->visible(function (Get $get, $record) {

                            if (!empty($get('project_division')) && $this->getRecord()->project_quarter->project_year->project->project_divisions()->whereHas('project_division_categories', function ($query) {
                                $query->where('from', 'Indirect Cost')->whereHas('project_division_sub_category_expenses', function ($query) {
                                    $query->where('parent_title', 'PCAARRD');
                                });
                            })->exists()) {
                                return true;
                            } else {
                                return false;
                            }
                        })




                ]
            );
    }
}
