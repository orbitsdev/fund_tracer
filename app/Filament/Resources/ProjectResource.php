<?php

namespace App\Filament\Resources;

use Closure;
use Carbon\Carbon;
use Filament\Forms;
use App\Models\Year;
use Filament\Tables;
use App\Models\Program;
use App\Models\Project;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Tables\Actions\ViewAction;
use App\Models\MonitoringAgency;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\ImplementingAgency;
use Filament\Actions\CreateAction;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Grouping\Group as TGroup;
use Filament\Tables\Actions\Action as TBAction;
use App\Filament\Resources\ProjectResource\Pages;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Tables\Actions\HeaderActionsPosition;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Section as InSection;
use App\Filament\Resources\ProjectResource\RelationManagers;
use Filament\Infolists\Components\Actions\Action as IFAction;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Thiktak\FilamentSimpleListEntry\Infolists\Components\SimpleListEntry;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Program Management';



    public $data;
    // protected static bool $shouldRegisterNavigation = false;


    // public function mount(){
    //     dd('dsd');
    // }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Actions::make([
                //     IFAction::make('Back')->label('Back')->icon('heroicon-m-arrow-uturn-left')->outlined()->color('gray')->url(fn (): string => ProjectResource::getUrl('index')),

                //         // IFAction::make('resetStars')
                //         // ->icon('heroicon-m-arrow-down-tray')
                //         // // ->color('danger')
                //         // // ->requiresConfirmation()
                //         // ->action(function () {
                //         //     dd('test');
                //         //     // $resetStars();
                //         // })
                //         // ->outlined()
                //         // ->label('Export To Excel'),
                // ]),

                Fieldset::make('Program Details')


                    ->schema([

                        TextEntry::make('program.title')
                            ->label('Program Title')



                            ->columnSpanFull(),
                        TextEntry::make('program.program_leader')


                            ->label('Program Leader')


                            ->columnSpanFull(),
                        TextEntry::make('program.start_date')
                            ->label('Program Start')
                            ->date(),


                        TextEntry::make('program.end_date')
                            ->label('Program End')
                            ->date(),
                        TextEntry::make('program.total_budget')
                            ->money('PHP')
                            ->label('Program Budget')
                            ->size(TextEntry\TextEntrySize::Large),

                        TextEntry::make('program.total_usage')
                            ->money('PHP')
                            ->label('Program Budget')
                            ->size(TextEntry\TextEntrySize::Large),
                    ]),

                Fieldset::make('Project Details')
                    ->columns([
                        'sm' => 3,
                        'xl' => 6,
                        '2xl' => 9,
                    ])
                    ->schema([
                        TextEntry::make('title')
                            ->label('Project Title')
                            ->columnSpanFull(),
                        TextEntry::make('project_leader')
                            ->label('Project Leader')
                            ->columnSpanFull(),
                        TextEntry::make('implementing_agency')
                            ->label('Implementing Agency')
                            ->columnSpan(3),
                        TextEntry::make('monitoring_agency')
                            ->label('Monitorin Agency')
                            ->columnSpan(3),

                        TextEntry::make('allocated_fund')
                            ->label('Allocated Fund')
                            ->money('PHP')
                            ->badge()
                            ->columnSpan(3),
                        TextEntry::make('start_date')
                            ->label('Project Start')
                            ->date()
                            ->columnSpan(3),

                        TextEntry::make('end_date')
                            ->label('Project End')
                            ->date()
                            ->columnSpan(3),


                        ViewEntry::make('Total Duration')
                            ->view('infolists.components.project-duration')

                            ->columnSpanFull(),
                        ViewEntry::make('Current Duration')
                            ->view('infolists.components.current-duration')

                            ->columnSpanFull(),



                    ]),

                // Fieldset::make('Duration')
                //     ->columns([
                //         'sm' => 3,
                //         'xl' => 6,
                //         '2xl' => 9,
                //     ])
                //     ->schema([
                //         ViewEntry::make('Total Duration')
                //         ->view('infolists.components.project-duration')
                //         ->columnSpanFull(),
                //     ]),


                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Project Yearly Expenses')
                            ->schema([
                                ViewEntry::make('')
                                    ->view('infolists.components.project-division-details')
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Summary Budget')
                            ->schema([
                                ViewEntry::make('')
                                    ->view('infolists.components.summary-budget  ')
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Other')
                            ->schema([
                                // ...
                            ]),

                    ])
                    ->activeTab(1)
                    ->columnSpanFull(),



                // RepeatableEntry::make('project_years')
                // ->schema([
                //     TextEntry::make('year.title'),
                //     RepeatableEntry::make('project_quarters')
                //     ->schema([
                //         TextEntry::make('quarter.title'),
                //         RepeatableEntry::make('project_divisions')
                //         ->schema([
                //             TextEntry::make('division.title'),
                //             RepeatableEntry::make('project_division_categories')
                //             ->schema([
                //                 TextEntry::make('division.title'),

                //             ]),

                //         ]),
                //     ]),
                // ])->columnSpanFull()

                // RepeatableEntry::make('project_divisions')
                //     ->schema([

                //         TextEntry::make('division.title'),

                //         RepeatableEntry::make('project_division_categories')
                //         ->schema([
                //             TextEntry::make('from'),

                //         ])

                //     ])
                //     ->columns(2)



            ]);
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Group::make()
                    ->schema([

                        Section::make('Project Information')
                            ->icon('heroicon-m-pencil-square')
                            ->description('Provide project details below to better understand and support funding needs.')
                            ->columns([
                                'sm' => 3,
                                'xl' => 6,
                                '2xl' => 9,
                            ])
                            ->schema([


                                // TextInput::make('budget')
                                // ->required()
                                // ->numeric()
                                // ->columnSpan(2)
                                // ,


                                Radio::make('project_type')
                                    ->label('Project Type')
                                    ->options([
                                        'Dependent' => 'Dependent ',
                                        'Independent' => 'Independent',
                                    ])
                                    ->default('Dependent')
                                    // ->descriptions([
                                    //     'Dipendent' => 'Project is belong to program',
                                    //     'Independent' => 'Project is not belong to any program',
                                    // ])
                                    ->helperText('Choose whether the project is dependent on a program or not')
                                    ->live()
                                    ->debounce(700)
                                    ->inline()
                                    ->columnSpanFull()
                                    ->hidden(function (string $operation) {
                                        return $operation === 'edit' ? true : false;
                                    }),

                                Select::make('program_id')
                                    ->live()
                                    ->debounce(700)
                                    // ->required()
                                    ->label('Choose Program')
                                    ->relationship(
                                        name: 'program',
                                        titleAttribute: 'title'
                                    )
                                    ->hint('Program  & Budget')
                                    //->helperText(new HtmlString('Program  & Budget'))
                                    // ->hintColor('primary')
                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title} - ₱ " . number_format($record->total_budget))

                                    //     ->live()
                                    //     ->debounce(700)
                                    //     ->afterStateUpdated(function(Get $get , Set $set){
                                    //         // $program = Program::find($get('program_id'));
                                    //         // if(!empty($program)){
                                    //         //      set('allocated_fund', $program->total_budget);
                                    //         // }
                                    // subtract the allocate ddun to the total budget of
                                    // })
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateProgramOverviewDetails($get, $set);
                                        self::calculateTotalMonthDurationn($get, $set);
                                        self::setCurrentDuration($get, $set);
                                    })

                                    ->hidden(function (Get $get, Set $set) {
                                        //if project has program 
                                        if ($get('project_type')  !=  'Dependent') {

                                            self::resetSelectedProgram($get, $set);

                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->columnSpanFull(),

                                TextInput::make('title')
                                    ->label('Project Title')
                                    ->required()
                                    ->maxLength(191)
                                    ->columnSpanFull(),


                                Select::make('implementing_agency')
                                    ->label('Implementing Agency')
                                    ->options(ImplementingAgency::all()->pluck('title', 'title'))
                                    ->hint(function () {
                                        if (ImplementingAgency::count() > 0) {
                                            return '';
                                        } else {
                                            return 'No implementing agency agency found';
                                        }
                                    })
                                    ->searchable()
                                    ->columnSpan(3)
                                    ->required()


                                    ->native(function (Get $get, Set $set) {
                                        return self::disabledDate($get, $set);
                                    }),
                                Select::make('monitoring_agency')
                                    ->label('Monitoring Agency')
                                    ->options(MonitoringAgency::all()->pluck('title', 'title'))
                                    ->required()
                                    ->hint(function () {
                                        if (MonitoringAgency::count() > 0) {
                                            return '';
                                        } else {
                                            return 'No monitoring agency found';
                                        }
                                    })
                                    ->columnSpan(3)
                                    ->searchable()

                                    ->native(function (Get $get, Set $set) {
                                        return self::disabledDate($get, $set);
                                    }),




                                TextInput::make('allocated_fund')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->prefix('₱')
                                    ->numeric()
                                    ->default(0)
                                    ->label('Allocated Amount')
                                    ->live()
                                    ->debounce(700)
                                    ->required()

                                    ->afterStateUpdated(function (Get $get, Set $set, string $operation) {

                                    self::updateLeftAllocated($get, $set, $operation);
                                    })
                                    ->columnSpan(3)
                                    ->rules([
                                        fn (Get $get, string $operation): Closure => function (string $attribute, $value, Closure $fail,) use ($get, $operation) {



                                            if (empty($get('program_id'))) {
                                                //     // $fail("Program should be selected first before setting allocated fund");
                                            } else {

                                                $selected_program = Program::find($get('program_id'));
                                                $total_allocated_projects = $selected_program->projects->sum('allocated_fund');
                                                $remaining_budget = $selected_program->total_budget - $total_allocated_projects;
                                                $allocatedFund = (float) str_replace(',', '',  $get('allocated_fund'));
                                                $current_allocated_budget = (float) str_replace(',', '',  $get('current_allocated_budget'));
                                                $max = $current_allocated_budget + $remaining_budget;
                                                if ($operation === 'edit') {

                                                    //ignore if the same value

                                                    if ($allocatedFund === $current_allocated_budget) {
                                                        // New value is equal to the current budget, no error
                                                    } elseif ($allocatedFund > $max) {
                                                        // New value is greater than the current budget
                                                        $fail("The allocated amount should not exceed the remaining budget of the selected program");
                                                    }
                                                } else {


                                                    if (!empty($selected_program)) {



                                                        if ($value > $remaining_budget) {
                                                            $fail("The allocated amount should not exceed the remaining budget of the selected program");
                                                        }
                                                    } else {
                                                        $fail("Program not found");
                                                    }
                                                }
                                            }
                                        },
                                    ]),



                                DatePicker::make('start_date')->date()
                                    ->columnSpan(3)
                                    ->live()
                                    ->debounce(700)
                                    ->afterStateUpdated(function (Get $get, Set $set) {

                                        self::calculateTotalMonthDurationn($get, $set);
                                        self::setCurrentDuration($get, $set);
                                    })
                                    ->readOnly(function (Get $get, Set $set) {
                                        return self::disabledDate($get, $set);
                                    })
                                    ->native(function (Get $get, Set $set) {
                                        return self::disabledDate($get, $set);
                                    })
                                    ->suffixIcon('heroicon-m-calendar-days')

                                    ->required(),


                                DatePicker::make('end_date')->date()
                                    ->columnSpan(3)
                                    ->live()
                                    ->debounce(700)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::calculateTotalMonthDurationn($get, $set);
                                        self::setCurrentDuration($get, $set);
                                    })
                                    ->readOnly(function (Get $get, Set $set) {
                                        return self::disabledDate($get, $set);
                                    })
                                    ->native(function (Get $get, Set $set) {
                                        return self::disabledDate($get, $set);
                                    })
                                    ->suffixIcon('heroicon-m-calendar-days')

                                    ->required(),
                                TextInput::make('duration_overview')
                                    ->disabled()
                                    ->label('Total Duration')
                                    // ->prefix('₱ ')
                                    // ->numeric()

                                    ->columnSpan(3)
                                    // ->maxLength(191)
                                    ->readOnly(),


                                // Select::make('status')
                                //     ->options([
                                //         'Not Started' => 'Not Started',
                                //         'Planning' => 'Planning',
                                //         'In Progress' => 'In Progress',
                                //         'On Hold' => 'On Hold',
                                //         'Cancelled' => 'Cancelled',
                                //         'Under Revision' => 'Under Revision',
                                //     ])
                                //     ->default('In Progress')
                                //     ->searchable()
                                //     ->native(false)
                                //     ->columnSpanFull(),

                            ])

                            ->collapsible(),




                        Section::make('Project Documents')
                            ->icon('heroicon-m-folder')
                            ->description('Manage and organize your Project documents. Upload files here')
                            ->columnSpanFull()
                            ->schema([
                                Repeater::make('files')

                                    ->relationship()
                                    ->label('Documents')
                                    ->schema([
                                        TextInput::make('file_name')
                                            ->label('Name')
                                            ->maxLength(191)
                                            ->required(),
                                        FileUpload::make('file')
                                            ->required()

                                            // ->columnSpanFull()
                                            // ->image()
                                            ->preserveFilenames()

                                            ->label('File')
                                            ->disk('public')
                                            ->directory('program-files')
                                    ])
                                    ->deleteAction(
                                        fn (Action $action) => $action->requiresConfirmation(),
                                    )
                                    ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                                        // $data['user_id'] = auth()->id();

                                        return $data;
                                    })
                                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                        // $filePath = storage_path('app/public/' . $data['file']);


                                        $filePath = storage_path('app/public/' . $data['file']);

                                        $fileInfo = [
                                            'file' => $data['file'],
                                            'file_name' => $data['file_name'],
                                            'file_type' => mime_content_type($filePath),
                                            'file_size' => call_user_func(function ($bytes) {
                                                $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                                                $i = 0;

                                                while ($bytes >= 1024 && $i < count($units) - 1) {
                                                    $bytes /= 1024;
                                                    $i++;
                                                }

                                                return round($bytes, 2) . ' ' . $units[$i];
                                            }, filesize($filePath)),
                                        ];
                                        return $fileInfo;
                                        // $data['user_id'] = auth()->id();

                                        // return $data;
                                    })
                                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {


                                        $filePath = storage_path('app/public/' . $data['file']);

                                        $fileInfo = [
                                            'file' => $data['file'],
                                            'file_name' => $data['file_name'],
                                            'file_type' => mime_content_type($filePath),
                                            'file_size' => call_user_func(function ($bytes) {
                                                $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                                                $i = 0;

                                                while ($bytes >= 1024 && $i < count($units) - 1) {
                                                    $bytes /= 1024;
                                                    $i++;
                                                }

                                                return round($bytes, 2) . ' ' . $units[$i];
                                            }, filesize($filePath)),
                                        ];

                                        // dd($fileInfo);
                                        // dd($data);

                                        return $fileInfo;
                                    })
                                    // ->collapsed()
                                    // ->collapsible()
                                    ->reorderable(true)
                                    ->columnSpanFull()
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Documents')


                            ])
                            ->collapsed()
                            ->collapsible(),


                    ])->columnSpan(['lg' => 2]),
                Group::make()
                    ->schema([

                        Section::make('Overview')
                            ->columns([
                                'sm' => 3,
                                'xl' => 6,
                                '2xl' => 8,
                            ])
                            //  ->icon('heroicon-m-chart-bar')
                            // ->description('Manage and organize project expenses here. You can only add expense in edit')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('program_name_overview')
                                    ->label('Selected Program')
                                    // ->prefix('₱ ')
                                    // ->numeric()

                                    ->columnSpanFull()
                                    // ->maxLength(191)
                                    ->disabled()
                                    ->readOnly(),
                                TextInput::make('program_budget_overview')
                                    ->label('Program Budget')
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
                                TextInput::make('program_remaining_budget_overview')
                                    ->label('Program Remaining Budget')
                                    ->prefix('₱ ')
                                    // ->numeric()
                                    ->columnSpan(4)
                                    ->disabled()
                                    // ->maxLength(191)
                                    ->readOnly(),




                                TextInput::make('current_allocated_budget')
                                    ->label('Current Project Budget')
                                    ->prefix('₱ ')
                                    // ->numeric()
                                    ->columnSpanFull(4)
                                    ->disabled()
                                    // ->maxLength(191)
                                    ->readOnly()
                                    ->hidden(function (string $operation) {
                                        return $operation === 'edit' ? false : true;
                                    }),

                            ]),

                        Section::make('Financial Summary')
                            ->description('Live calculations based on your inputs')

                            ->columns([
                                'sm' => 3,
                                'xl' => 6,
                                '2xl' => 8,
                            ])
                            ->columnSpanFull()
                            ->schema([


                                // TextInput::make('project_fund')
                                //     ->label(function (string $operation) {
                                //         return $operation === 'edit' ? 'New Project Budget' : 'Current Project Budget';
                                //     })
                                //     ->mask(RawJs::make('$money($input)'))
                                //     ->stripCharacters(',')
                                //     ->prefix('-')
                                //     ->numeric()
                                //     ->columnSpanFull()
                                //     ->default(0)
                                //     ->disabled()
                                //     // ->maxLength(191)
                                //     ->readOnly(),
                                TextInput::make('left_budget')
                                    ->prefix('=')
                                    ->label('Remaining Budget of Program After Project Deduction')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->numeric()
                                    ->columnSpanFull()
                                    ->default(0)
                                    ->disabled()
                                    // ->maxLength(191)
                                    ->readOnly(),
                            ]),


                    ])
                    ->hidden(function (Get $get, Set $set) {

                        if (!empty($get('program_id')) && $get('project_type')  ==  'Dependent') {
                            return false;
                        } else {
                            self::resetSelectedProgram($get, $set);
                            return true;
                        }
                    })
                    ->columnSpan(['lg' => 1])



            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                //    TBAction::make('dasd'),
            ], position: HeaderActionsPosition::Bottom)
            ->columns([
                TextColumn::make('program.title')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('title')
                    ->searchable()->label('Project Title')->wrap(),
                TextColumn::make('allocated_fund')
                    ->money('PHP')
                    ->numeric(
                        decimalPlaces: 0,
                    )
               
                    ->prefix('₱ ')
                    ->sortable(),

                TextColumn::make('start_date')
                    ->date()
              
                    ->label('Start Date')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable(),


                // TextColumn::make('allocated_fund')
                //     ->summarize(Sum::make()->label('total')->money('PHP')),

                // TextColumn::make('status')
                //     ->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([

                ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label('View Details'),
                    EditAction::make()->label('Update Basic Information'),
                    // Tables\Actions\Action::make('Manage Quarter')->label('Manage Quarters')->icon('heroicon-m-pencil-square')->url(fn (Model $record): string => ProjectResource::getUrl('manage-quarter-year', ['record'=> $record])),
                    Tables\Actions\Action::make('budget_division_old')->label('Divide Budget')->icon('heroicon-m-table-cells')->url(fn (Model $record): string => ProjectResource::getUrl('budget-division', ['record' => $record])),
                    Tables\Actions\Action::make('budget_division')->label('Divide Budget')->icon('heroicon-m-table-cells')->url(fn (Model $record): string => ProjectResource::getUrl('project-table-division', ['record' => $record])),
                    // Tables\Actions\Action::make('quarter_budget')->label('Quarter Expenses')->icon('heroicon-m-chart-bar')->url(fn (Model $record): string => ProjectResource::getUrl('quarter-budget', ['record'=> $record])),

                    Tables\Actions\Action::make('year_quarter_budget')->label('Expenses')->icon('heroicon-m-banknotes')->url(fn (Model $record): string => ProjectResource::getUrl('manage-quarter-year', ['record' => $record])),



                ]),
            ])
            ->recordUrl(null)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
                    ->label('Actions'),
            ])

            ->groups([
                TGroup::make('program.title')
                    ->titlePrefixedWithLabel(false)
                    ->getTitleFromRecordUsing(fn (Project $record): string => $record->program ?  ucfirst($record->program->title) : '')
                    ->label('Program')
                    ->collapsible(),


            ])
            // ->defaultGroup('program.title')
            // ->groupsOnly()
            ->modifyQueryUsing(fn (Builder $query) => $query->latest());
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [

            // 'project' => Pages\Project::route('/project'),
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
            'budget-division' => Pages\EditProjectQuarter::route('/{record}/edit/budget-division'),
            'project-table-division' => Pages\ProjectTableDivision::route('/{record}/edit/project-table-division'),
            'project-table-division-category' => Pages\ProjectTableDivisionCategory::route('/{record}/edit/project-table-division'),
            'quarter-budget' => Pages\ProjectQuarterBudjet::route('/{record}/edit/quarter-budget'),
            // 'budget-division' => Pages\BudgetDivision::route('/{record}/budget-division'),
            'manage-quarter-year' => Pages\ManageYearQuarter::route('/{record}/year-quarters'),
            'create-quarter' => Pages\ManageQuarter::route('/{record}/quarter/create'),
            'quarter-list' => Pages\ProjectYearQuarterList::route('/{record}/quarters'),
            'view' => Pages\ViewProject::route('/{record}'),
        ];
    }


    public static function resetSelectedProgram(Get $get, Set $set)
    {
        if (!empty($get('program_id'))) {
            $set('program_id', null);
            $set('left_budget', null);
        }
    }
    public static function disabledDate(Get $get, Set $set)
    {

        if (!empty($get('program_id'))) {
            return true;
        } else {
            return false;
        }
    }

    public static function updateProgramOverviewDetails(Get $get, Set $set)
    {

        //  dd($get('program_id'));
        $allocatedFund = floatval(str_replace(',', '', $get('allocated_fund')));
        $set('project_fund', number_format($allocatedFund));
        if (!empty($get('program_id'))) {
            $program = Program::find($get('program_id'));
            if (!empty($program)) {

                $total_allocated_projects = $program->projects->sum('allocated_fund');
                $remaining_budget =  floatval(str_replace(',', '', $program->total_budget)) - $total_allocated_projects;
                $left_budget = $remaining_budget - $allocatedFund;

                $set('start_date', $program->start_date);
                $set('end_date', $program->end_date);
                $set('implementing_agency', $program->implementing_agency);
                $set('monitoring_agency', $program->monitoring_agency);
                $set('program_name_overview', $program->title);
                $set('program_budget_overview', number_format($program->total_budget));
                $set('program_use_budget_overview', $program->total_usage);
                $set('program_remaining_budget_overview', number_format($remaining_budget));
                $set('left_budget', number_format($left_budget));
            } else {

                $set('start_date', null);
                $set('end_date', null);
                $set('implementing_agency', null);
                $set('monitoring_agency', null);
                $set('program_name_overview', null);
                $set('program_budget_overview', null);
                $set('program_use_budget_overview', null);
                $set('program_remaining_budget_overview', null);
                $set('left_budget', null);
            }
        } else {
            $set('start_date', null);
            $set('end_date', null);
            $set('implementing_agency', null);
            $set('monitoring_agency', null);
            $set('program_name_overview', null);
            $set('program_budget_overview', null);
            $set('program_use_budget_overview', null);
            $set('program_remaining_budget_overview', null);
            $set('left_budget', null);
        }
    }

    public static function updateLeftAllocated(Get $get, Set $set, string $operation)
    {

        $allocatedFund = (float) str_replace(',', '', $get('allocated_fund'));


        if (!empty($get('program_id'))) {
            $program = Program::find($get('program_id'));

            if (!empty($program)) {
                $total_allocated_projects = $program->projects->sum('allocated_fund');
                $remaining_budget = floatval(str_replace(',', '', $program->total_budget)) - $total_allocated_projects;

                // Check if allocatedFund is smaller than current_allocated_budget
                $current_allocated_budget = floatval(str_replace(',', '', $get('current_allocated_budget')));
                $allocatedFund = floatval(str_replace(',', '', $get('allocated_fund')));

                if ($allocatedFund < $current_allocated_budget) {

                    $remaining_budget += ($current_allocated_budget - $allocatedFund);
                }

                $left_budget = $remaining_budget - $allocatedFund;

                $set('left_budget', number_format(max(0, $left_budget)));
            } else {
                $set('left_budget', null);
            }
        } else {
            $set('left_budget', null);
        }


        // self::updateTotal($get, $set);
    }

    public static function updateTotal(Get $get, Set $set)
    {


        $current_fund = (float) str_replace(',', '', $get('allocated_fund')); // Convert to float
        $expenses = collect($get('expenses'))->filter(fn ($item) => !empty($item['amount']));

        $totalAmount = $expenses->sum(function ($item) {
            return (float) str_replace(',', '', $item['amount']);
        });


        $set('total_expenses', number_format($totalAmount, 2));
    }
    public static function setCurrentDuration(Get $get, Set $set)
    {

        $startDate = $get('start_date');
        $endDate = $get('end_date');
        if (!empty($startDate) && !empty($endDate)) {

            // dd(Carbon::parse($startDate)->format('F d, Y'), Carbon::parse($startDate)->format('F d, Y'));

            $currentDuration = Carbon::parse($startDate)->format('F d, Y') . ' - ' . Carbon::parse($endDate)->format('F d, Y');

            $set('current_duration_overview', $currentDuration);
        }
    }

    public static function calculateTotalMonthDurationn(Get $get, Set $set)
    {


        $startDate = $get('start_date');
        $endDate = $get('end_date');

        if (!empty($startDate) && !empty($endDate)) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);

            // Calculate the difference in months
            $totalMonths = $endDate->diffInMonths($startDate);

            // Set the duration in months
            $set('duration_overview', $totalMonths . ' months');
        }
        // $set('project_fund', number_format($get('allocated_fund')));
        // // $set('total_expenses', (int)$get('allocated_fund'));
        // self::updateTotal($get, $set);
    }
}
