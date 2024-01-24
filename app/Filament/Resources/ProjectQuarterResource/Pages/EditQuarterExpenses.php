<?php

namespace App\Filament\Resources\ProjectQuarterResource\Pages;

use Closure;
use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Support\RawJs;
use Filament\Actions\Action;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
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


    protected function mutateFormDataBeforeFill(array $data): array
    {

        // dd($this->getRecord()->quarter_expense_budget_divisions);
        $ex = $this->getRecord()->quarter_expense_budget_divisions()->with(['quarter_expenses'=>function($query){
            $query->whereHas('quarter_expense_budget_division.project_division.project_division_categories', function($query){
                $query->where('from', 'Direct Cost');
        });
         }])
            ->get();
        //  dd($ex);
        $expenses = $this->getRecord()
            ->project_year
            ->project
            ->project_divisions()->with(['quarter_expense_budget_divisions'])
            ->get();

        


        $dc_expenses = $this->getRecord()
            ->project_year
            ->project
            ->project_divisions()
            // ->whereHas('quarter_expense_budget_divisions.project_division.project_division_categories', function ($query) {
            //     $query->where('from', 'Direct Cost');
            // })
            ->with(['quarter_expense_budget_divisions.quarter_expenses'=>function ($query) {
                $query->whereHas('quarter_expense_budget_division.project_division.project_division_categories',function ($query) {
                    $query->where('from', 'Direct Cost');
                });
            }])
            ->get();

           

        $ic_sksu_expenses = $this->getRecord()
            ->project_year
            ->project
            ->project_divisions()
            ->whereHas('quarter_expense_budget_divisions.project_division.project_division_categories', function ($query) {
                $query->where('from', 'Indirect Cost')->whereHas('project_division_sub_category_expenses', function ($query) {
                    $query->where('parent_title', 'SKSU');
                });
            })
            ->with(['quarter_expense_budget_divisions.quarter_expenses'])
            ->get();
          
        $ic_pcaarrd_expenses = $this->getRecord()
            ->project_year
            ->project
            ->project_divisions()
            ->whereHas('quarter_expense_budget_divisions.project_division.project_division_categories', function ($query) {
                $query->where('from', 'Indirect Cost')->whereHas('project_division_sub_category_expenses', function ($query) {
                    $query->where('parent_title', 'PCAARRD');
                });
            })
            ->with(['quarter_expense_budget_divisions.quarter_expenses'])
            ->get();


        // $total = $this->getRecord()
        // ->project_year
        // ->project
        // ->project_divisions()

        // ->with(['quarter_expense_budget_divisions.quarter_expenses'])
        // ->get();



        $total_dc = $dc_expenses->flatMap(function ($projectDivision) {
            return $projectDivision->quarter_expense_budget_divisions->flatMap(function ($budgetDivision) {
                return $budgetDivision->quarter_expenses->pluck('amount');
            });
        })->sum();
        $total_ic_sksu = $ic_sksu_expenses->flatMap(function ($projectDivision) {
            return $projectDivision->quarter_expense_budget_divisions->flatMap(function ($budgetDivision) {
                return $budgetDivision->quarter_expenses->pluck('amount');
            });
        })->sum();
        $total_ic_pcaarrd = $ic_pcaarrd_expenses->flatMap(function ($projectDivision) {
            return $projectDivision->quarter_expense_budget_divisions->flatMap(function ($budgetDivision) {
                return $budgetDivision->quarter_expenses->pluck('amount');
            });
        })->sum();

        $totalAmount = $expenses->flatMap(function ($projectDivision) {
            return $projectDivision->quarter_expense_budget_divisions->flatMap(function ($budgetDivision) {
                return $budgetDivision->quarter_expenses->pluck('amount');
            });
        })->sum();




        $data['total_dc'] = number_format($total_dc, 2);
        $data['total_ic_sksu'] = number_format($total_ic_sksu, 2);
        $data['total_ic_pcaarrd'] = number_format($total_ic_pcaarrd, 2);
        $data['total_expenses'] = number_format($totalAmount, 2);



        // dd($totalAmount);
        return $data;
    }

    public function getHeader(): ?View
    {
        return view('filament.settings.custom-header', ['title' => 'Edit Project Quarter', 'first' => 'Project Quarters', 'second' => 'edit']);
    }


    protected function getRedirectUrl(): string
    {
        return ProjectResource::getUrl('quarter-list', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // unset($data['current_duration_overview']);
        // unset($data['duration_overview']);
        // unset($data['project_fund']);
        unset($data['total_dc']);
        unset($data['total_ic_sksu']);
        unset($data['total_ic_pcaarrd']);
        unset($data['total_expenses']);

        return $data;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(
                [

                    Group::make()
                        ->schema([


                            Section::make('')
                                ->columnSpanFull()
                                ->schema([
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
                                        ->hint(function (Get $get, Model $record) {
                                            if (
                                                $this->getRecord()->project_year &&
                                                $this->getRecord()->project_year->project &&
                                                $this->getRecord()->project_year->project->project_divisions->isNotEmpty()
                                            ) {
                                                return '';
                                            } else {
                                                return 'If you don\'t see any data below, it might be because the division was not set up first.';
                                            };
                                        })
                                        ->disabled(),
                                ]),



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
                                            self::updateTotal($get, $set);
                                        })
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
                                                                $query->where('project_id', $this->getRecord()->project_year->project_id);
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
                                                ->debounce(1000)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    self::updateTotal($get, $set);
                                                })

                                                // ->mask(RawJs::make('$money($input)'))
                                                // ->stripCharacters(',')
                                                ->prefix('₱')
                                                ->numeric()
                                                // ->maxValue(9999999999)
                                                ->default(0)
                                                ->columnSpanFull()
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
                                            self::updateTotal($get, $set);
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
                                                                    ->where('project_devision_id', $get('../../project_devision_id'))
                                                                    ->whereHas('project_devision', function ($query) {
                                                                        $query->where('project_id', $this->getRecord()->project_year->project_id);
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
                                                ->debounce(1000)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    self::updateTotal($get, $set);
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
                                            self::updateTotal($get, $set);
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
                                                                    ->where('project_devision_id', $get('../../project_devision_id'))
                                                                    ->whereHas('project_devision', function ($query) {
                                                                        $query->where('project_id', $this->getRecord()->project_year->project_id);
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
                                                ->debounce(1000)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    self::updateTotal($get, $set);
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

                        ])->columnSpan(['lg' => 2]),

                    Group::make()
                        ->schema([

                            Section::make('Project Overview')
                                //  ->icon('heroicon-m-square-3-stack-3d')
                                // ->description('Manage and organize project expenses here. You can only add expense in edit')
                                ->columnSpanFull()
                                ->schema([
                                    // TextInput::make('current_duration_overview')
                                    //     ->label('Current Duration')
                                    //     // ->prefix('₱ ')
                                    //     // ->numeric()
                                    //     ->columnSpan(3)

                                    //     ->columnSpanFull()
                                    //     // ->maxLength(191)
                                    //     ->readOnly(),
                                    // // Placeholder::make('duration')
                                    // TextInput::make('duration_overview')
                                    //     ->label('Total Duration')
                                    //     // ->prefix('₱ ')
                                    //     // ->numeric()
                                    //     ->columnSpan(3)

                                    //     ->columnSpanFull()
                                    //     // ->maxLength(191)
                                    //     ->readOnly(),

                                    // TextInput::make('project_fund')
                                    //     ->label('Allocated Amount')
                                    //     ->mask(RawJs::make('$money($input)'))
                                    //     ->stripCharacters(',')
                                    //     ->numeric()
                                    //     ->columnSpan(3)
                                    //     ->default(0)
                                    //     // ->maxLength(191)
                                    //     ->readOnly(),

                                    TextInput::make('total_dc')
                                        ->label('Total DC')
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')
                                        ->numeric()
                                        ->columnSpan(3)
                                        ->default(0)
                                        // ->maxLength(191)
                                        ->readOnly(),
                                    TextInput::make('total_ic_sksu')
                                        ->label('IC SKSU')
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')
                                        ->numeric()
                                        ->columnSpan(3)
                                        ->default(0)
                                        // ->maxLength(191)
                                        ->readOnly(),
                                    TextInput::make('total_ic_pcaarrd')
                                        ->label('IC PCAARRD')
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')
                                        ->numeric()
                                        ->columnSpan(3)
                                        ->default(0)
                                        // ->maxLength(191)
                                        ->readOnly(),

                                    TextInput::make('total_expenses')
                                        ->label('Total Expenses')
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')
                                        ->numeric()
                                        ->columnSpan(3)
                                        ->default(0)
                                        // ->maxLength(191)
                                        ->readOnly(),


                                ]),

                        ])->columnSpan(['lg' => 1]),



                ]
            )
            ->columns(3);
    }

    public static function updateTotal(Get $get, Set $set)
    {


        //  dd($get('../../direct_cost_expenses'));
        // Convert to float

        //DC
        $dc_expenses = collect($get('../../direct_cost_expenses'))->filter(fn ($item) => !empty($item['amount']));

        // IC SKSU
        $ic_expenses_sksu = collect($get('../../indirect_cost_expenses_sksu'))->filter(fn ($item) => !empty($item['amount']));

        // IC PCAARRD
        $ic_expenses_pcaarrd = collect($get('../../indirect_cost_expenses_pcaarrd'))->filter(fn ($item) => !empty($item['amount']));

        $dc_total = $dc_expenses->sum(function ($item) {
            return (float) str_replace(',', '', $item['amount']);
        });

        $ic_sksu_total = $ic_expenses_sksu->sum(function ($item) {
            return (float) str_replace(',', '', $item['amount']);
        });

        $ic_pcaarrd_total = $ic_expenses_pcaarrd->sum(function ($item) {
            return (float) str_replace(',', '', $item['amount']);
        });

        $expenses = collect([
            'dc_total' => $dc_total,
            'ic_sksu_total' => $ic_sksu_total,
            'ic_pcaarrd_total' => $ic_pcaarrd_total,
        ]);

        $total_expenses = $expenses->sum();


        // Now $total_expenses holds the sum of all expenses.





        $set('../../../../total_dc', number_format($dc_total, 2));
        $set('../../../../total_ic_sksu', number_format($ic_sksu_total, 2));
        $set('../../../../total_ic_pcaarrd', number_format($ic_pcaarrd_total, 2));
        $set('../../../../total_expenses', number_format($total_expenses, 2));

        // $left_fund = $current_fund - $totalAmount;



        // $current_fund = (float)$get('allocated_fund'); // Convert to float
        // $expenses = collect($get('expenses'))->filter(fn ($item) => !empty($item['amount']));


        // $totalAmount = $expenses->sum(function ($item) {
        //     return (float)$item['amount'];
        // });

        // // $left_fund = $current_fund - $totalAmount;

        // $set('total_expenses', number_format($totalAmount));




    }
}
