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


    public $over_all_expenses;

    protected function mutateFormDataBeforeFill(array $data): array
    {

      
        $project = $this->getRecord()->project_year->project;
        
        $total_expenses = $this->getRecord()->quarter_expense_budget_divisions
        ->flatMap->quarter_expenses->sum('amount');


        $total_dc = $this->getRecord()->quarter_expense_budget_divisions
            ->flatMap->quarter_expenses
            ->filter(function ($expense) {
                return $expense->fourth_layer()->whereHas('project_division_sub_category_expense', function ($query) {
                    $query                      
                        ->whereHas('project_division_category', function ($query) {
                            $query->where('from', 'Direct Cost');
                        });
                })->exists();
            })
            ->sum('amount');

        $total_ic_sksu = $this->getRecord()->quarter_expense_budget_divisions
            ->flatMap->quarter_expenses
            ->filter(function ($expense) {
                return $expense->fourth_layer()->whereHas('project_division_sub_category_expense', function ($query) {
                    $query      
                     ->where('parent_title', 'SKSU')                
                        ->whereHas('project_division_category', function ($query) {
                            $query->where('from', 'Indirect Cost');
                        });
                })->exists();
            })
            ->sum('amount');
        $total_ic_pcaarrd = $this->getRecord()->quarter_expense_budget_divisions
            ->flatMap->quarter_expenses
            ->filter(function ($expense) {
                return $expense->fourth_layer()->whereHas('project_division_sub_category_expense', function ($query) {
                    $query      
                     ->where('parent_title', 'PCAARRD')                
                        ->whereHas('project_division_category', function ($query) {
                            $query->where('from', 'Indirect Cost');
                        });
                })->exists();
            })
            ->sum('amount');

            $data['project_name_overview'] = $project->title ? $project->title : '';
            $data['project_budget_overview'] = number_format($project->allocated_fund ? $project->allocated_fund : 0);
            $remaining_budget =  floatval(str_replace(',', '', $project->allocated_fund)) - $total_expenses;
            
            $allocatedFund = floatval(str_replace(',', '', $project->allocated_fund));
            $left_budget = $remaining_budget - $total_expenses;
            $this->over_all_expenses = $total_expenses;

            // $data['left_budget'] =number_format($left_budget);
            $data['project_remaining_budget_overview'] = number_format($remaining_budget);
            $data['total_dc'] = number_format($total_dc, 2);
            $data['total_ic_sksu'] = number_format($total_ic_sksu, 2);
            $data['total_ic_pcaarrd'] = number_format($total_ic_pcaarrd, 2);
            $data['current_expenses'] = number_format($total_expenses, 2);
            // $data['total_expenses'] = number_format($total_expenses, 2);



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
        unset($data['left_budget']);
       
       
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
                                           $this->over_all_expenses =   self::updateTotal($get, $set, $this->getRecord());
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
                                                ->debounce(900)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    self::updateTotal($get, $set, $this->getRecord());
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
                                            self::updateTotal($get, $set , $this->getRecord());
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
                                                ->debounce(900)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    self::updateTotal($get, $set , $this->getRecord());
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
                                            self::updateTotal($get, $set , $this->getRecord());
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
                                                ->debounce(900)
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    self::updateTotal($get, $set , $this->getRecord());
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
                                ->columns([
                                    'sm' => 3,
                                    'xl' => 6,
                                    '2xl' => 8,
                                ])
                                ->schema([
                                    TextInput::make('project_name_overview')
                                    ->label('Selected Project')
                                    // ->prefix('₱ ')
                                    // ->numeric()
                                    ->columnSpanFull()
                                    // ->maxLength(191)
                                    ->disabled()
                                    ->readOnly(),
                                TextInput::make('project_budget_overview')
                                    ->label('Project Budget')
                                    // ->default(0)
                                    ->prefix('₱ ')
                                    // ->numeric()
                                    ->disabled()
                                    ->columnSpan(4)

                                    // ->maxLength(191)
                                    ->readOnly(),
                                // TextInput::make('program_use_budget_overview')
                                //     ->label('Total Used')
                                //     // ->prefix('₱ ')
                                //     // ->numeric()
                                //     ->columnSpan(4)

                                //     // ->maxLength(191)
                                //     ->readOnly(),
                                TextInput::make('project_remaining_budget_overview')
                                    ->label('Project Remaining Budget')
                                    ->prefix('₱ ')
                                    // ->numeric()
                                    ->columnSpan(4)
                                    ->disabled()
                                    // ->maxLength(191)
                                    ->readOnly(),

                             

                                ])
                                ,

                            Section::make('Financial Summary')
                            ->description('Live calculations based on your inputs')
                                //  ->icon('heroicon-m-square-3-stack-3d')
                                // ->description('Manage and organize project expenses here. You can only add expense in edit')
                                ->columnSpanFull()
                                ->columns([
                                    'sm' => 3,
                                    'xl' => 6,
                                    '2xl' => 8,
                                ])
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
                                     
                                        ->columnSpanFull()
                                        ->default(0)
                                        
                                        // ->maxLength(191)
                                        ->readOnly(),
                                    TextInput::make('total_ic_sksu')
                                        ->label('IC SKSU')
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')
                                        ->numeric()
                                        
                                        ->columnSpan(4)
                                        ->default(0)
                                     
                                        // ->maxLength(191)
                                        ->readOnly(),

                                    TextInput::make('total_ic_pcaarrd')
                                        ->label('IC PCAARRD')
                                     
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')
                                        ->numeric()
                                        ->columnSpan(4)
                                        ->default(0)
                                        // ->maxLength(191)
                                        ->readOnly(),
                                        TextInput::make('current_expenses')
                                        ->label('Original Expenses')
                                     ->disabled()
                                          
                                     ->prefix('₱ ')
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')
                                        ->numeric()
                                        ->columnSpanFull()
                                        ->default(0)
                                        // ->maxLength(191)
                                        ->readOnly(),
                                 

                                 


                                ]),

                                Section::make('')
                             

                                    //  ->icon('heroicon-m-square-3-stack-3d')
                                    // ->description('Manage and organize project expenses here. You can only add expense in edit')
                                    ->columnSpanFull()
                                    ->columns([
                                        'sm' => 3,
                                        'xl' => 6,
                                        '2xl' => 8,
                                    ])
                                    ->schema([
                                               
                                        TextInput::make('expense_adjustment')
                                        ->label('New Amount Added')
                                    
                                    
                                ->prefix('₱ ')
                                // ->numeric()
                                ->columnSpanFull(4)
                                // ->disabled()
                                // ->maxLength(191)
                                ->readOnly()
                                ->hidden(function (string $operation) {
                                    return $operation === 'edit' ? false : true;
                                }),
                          

                                        TextInput::make('total_expenses')
                                        ->label('Total ')
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')
                                        ->numeric()
                                        ->prefix('₱ ')
                                        ->columnSpanFull()
                                        ->default(0)
                                        ->afterStateUpdated(function (Get $get, Set $set) {
                                            // dd($this->over_all_expenses);
                                            // self::updateTotal($get, $set, $this->getRecord());
                                        })
                                        // ->maxLength(191)
                                        ->required()
                                        ->live()
                                        ->debounce(900)
                                        ->rules([
                                            fn (Get $get, string $operation): Closure => function (string $attribute, $value, Closure $fail,) use ($get, $operation) {
    
                                                // $project = $this->getRecord()->project_year->project;
                                                // $allocatedFund = floatval(str_replace(',', '', $project->allocated_fund));
                                                // $old_expenses = $this->getRecord()->quarter_expense_budget_divisions->flatMap->quarter_expenses->sum('amount');
                                                // $dc_expenses = collect($get('irect_cost_expenses'))->filter(fn ($item) => !empty($item['amount']));

                                                // // IC SKSU
                                                // $ic_expenses_sksu = collect($get('indirect_cost_expenses_sksu'))->filter(fn ($item) => !empty($item['amount']));
                                        
                                                // // IC PCAARRD
                                                // $ic_expenses_pcaarrd = collect($get('../../indirect_cost_expenses_pcaarrd'))->filter(fn ($item) => !empty($item['amount']));
                                        
                                                // $dc_total = $dc_expenses->sum(function ($item) {
                                                //     return (float) str_replace(',', '', $item['amount']);
                                                // });
                                        
                                                // $ic_sksu_total = $ic_expenses_sksu->sum(function ($item) {
                                                //     return (float) str_replace(',', '', $item['amount']);
                                                // });
                                        
                                                // $ic_pcaarrd_total = $ic_expenses_pcaarrd->sum(function ($item) {
                                                //     return (float) str_replace(',', '', $item['amount']);
                                                // });
                                        
                                                // $expenses = collect([
                                                //     'dc_total' => $dc_total,
                                                //     'ic_sksu_total' => $ic_sksu_total,
                                                //     'ic_pcaarrd_total' => $ic_pcaarrd_total,
                                                // ]);
                                                // $total_expenses = $expenses->sum();
                                                // $remaining_budget =  floatval(str_replace(',', '', $project->allocated_fund)) - $old_expenses;
                                                // $overall_expenses = $total_expenses - $remaining_budget;
                                        

                                                // if ($overall_expenses > $remaining_budget) {
                                                //     $fail("The allocated amount should not exceed the remaining budget of the selected program");
                                                // }
                                               
                                            },
                                        ])
                                         ->readOnly()
                                        ,
                                        TextInput::make('left_budget')
                                        ->prefix('=')
                                        ->label('Remaining Project Budget After Updating Expenses')
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')
                                        ->numeric()
                                        ->columnSpanFull()
                                        ->default(0)
                                     
                                        // ->maxLength(191)
                                        ->readOnly(),
                               


                                        ]),
                                   

                        ])->columnSpan(['lg' => 1]),



                ]
            )
            ->columns(3);
    }

    public static function updateTotal(Get $get, Set $set , Model $record)
    {

      

        $project = $record->project_year->project;
        

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
        
        $total_expenses = $expenses->sum() ;

        $old_expenses = $record->quarter_expense_budget_divisions
        ->flatMap->quarter_expenses->sum('amount');
        
        $total_added_expenses = $total_expenses - $old_expenses;
        
        
        $remaining_budget =  floatval(str_replace(',', '', $project->allocated_fund)) - $old_expenses;
        $left_budget = $remaining_budget - $total_added_expenses;



        // Now $total_expenses holds the sum of all expenses.
        $set('../../../../total_dc', number_format($dc_total, 2));
        $set('../../../../total_ic_sksu', number_format($ic_sksu_total, 2));
        $set('../../../../total_ic_pcaarrd', number_format($ic_pcaarrd_total, 2));
        $set('../../../../expense_adjustment', number_format($total_added_expenses, 2));
        $set('../../../../total_expenses', number_format($total_expenses, 2));
         $set('../../../../left_budget', number_format($left_budget));


        return $total_expenses;


    }
}
