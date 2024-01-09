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
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Actions\CreateAction;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Repeater;
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
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Grouping\Group as TGroup;
use App\Filament\Resources\ProjectResource\Pages;
use Filament\Infolists\Components\RepeatableEntry;
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
                             ->size(TextEntry\TextEntrySize::Large)
                            ,

                        TextEntry::make('program.total_usage')
                            ->money('PHP')
                            ->label('Program Budget')
                             ->size(TextEntry\TextEntrySize::Large)



                            ,
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





                        ViewEntry::make('')
                            ->view('infolists.components.project-division-details')
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
                                    // ->helperText(new HtmlString('Program  & Budget'))
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
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->columnSpanFull(),

                                TextInput::make('title')
                                    ->label('Project Title')
                                    ->required()
                                    ->maxLength(191)
                                    ->columnSpan(3),
                                TextInput::make('implementing_agency')
                                    ->label('Implementing Agency')
                                    ->required()
                                    ->maxLength(191)
                                    ->columnSpan(3),
                                TextInput::make('monitoring_agency')
                                    ->label('Monitorin Agency')
                                    ->required()
                                    ->maxLength(191)
                                    ->columnSpan(3),
                                // Select::make('user_id')
                                //     ->relationship(
                                //         name: 'manager',
                                //         titleAttribute: 'first_name',
                                //         modifyQueryUsing: fn (Builder $query) => $query->whereDoesntHave('managerProject'),
                                //     )
                                //     ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}")
                                //     ->searchable(['first_name', 'last_name'])
                                //     // ->searchable()
                                //     ->preload()
                                //     ->columnSpan(4)
                                //     ->required()

                                //     ->native(false),

                                TextInput::make('allocated_fund')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')

                                    // ->mask(RawJs::make('$money($input)'))
                                    // ->stripCharacters(',')
                                    ->prefix('₱')
                                    ->numeric()
                                    // ->maxValue(9999999999)
                                    ->default(0)
                                    ->label('Allocated Amount')
                                    ->live()
                                    ->debounce(900)
                                    ->required()
                                    // ->disabled(function(Get $get, Set $set){
                                    //     if(empty($get('program_id'))){
                                    //         return true;
                                    //     }

                                    //     // return false;
                                    // })



                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateLeftAllocated($get, $set);
                                    })
                                    ->prefix('₱ ')
                                    ->numeric()
                                    ->default(0)
                                    ->columnSpan(3)
                                    ->rules([
                                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {


                                            if (empty($get('program_id'))) {
                                                $fail("Program should be selected first before setting allocated fund");
                                            } else {
                                                $selected_program = Program::find($get('program_id'));

                                                if (!empty($selected_program)) {
                                                    $remaining_budget = $selected_program->total_budget - $selected_program->total_usage;

                                                    if ($value > $remaining_budget) {
                                                        $fail("The expenses amount should not exceed the remaining budget of the selected program");
                                                    }
                                                } else {
                                                    $fail("Program not found");
                                                }
                                            }
                                        },
                                    ]),



                                DatePicker::make('start_date')->date()->native(false)->columnSpan(3)
                                    ->live()
                                    ->debounce(700)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::calculateTotalMonthDurationn($get, $set);
                                        self::setCurrentDuration($get, $set);
                                    })
                                    ->required(),
                                DatePicker::make('end_date')->date()->native(false)->columnSpan(3)
                                    ->live()
                                    ->debounce(700)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::calculateTotalMonthDurationn($get, $set);
                                        self::setCurrentDuration($get, $set);
                                    })
                                    ->required(),


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
                            ->collapsed()
                            ->collapsible(),


                        // Section::make('Manage Quarters')
                        //     ->icon('heroicon-m-banknotes')
                        //     // ->description('Manage and organize particulars  ')
                        //     ->columnSpanFull()
                        //     ->schema([

                        //         Repeater::make('project_years')

                        //             ->relationship()

                        //             ->label('Year & Quarters')

                        //             ->extraAttributes([
                        //                 'class' => 'border-white',

                        //             ])
                        //             ->schema([
                        //                   Select::make('year_id')
                        //                     ->live()
                        //                     ->relationship(name: 'year', titleAttribute: 'title')
                        //                     ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                        //                     ->searchable()
                        //                     ->label('Year')
                        //                     ->preload()
                        //                     ->native(false)
                        //                     ->columnSpanFull()
                        //                     ->distinct()
                        //                     ->disableOptionsWhenSelectedInSiblingRepeaterItems(),


                        //         Repeater::make('project_quarters')

                        //         ->relationship()

                        //         ->label('Quarters')

                        //         ->extraAttributes([
                        //             'class' => 'border-white',

                        //         ])
                        //         ->schema([
                        //             Select::make('quarter_id')
                        //             ->live()
                        //             ->relationship(name: 'quarter', titleAttribute: 'title')
                        //             ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                        //             ->searchable()
                        //             ->label('Quarter')
                        //             ->preload()
                        //             ->native(false)
                        //             ->columnSpanFull()
                        //             ->distinct()
                        //             ->disableOptionsWhenSelectedInSiblingRepeaterItems(),


                        //                      Repeater::make('project_divisions')

                        //                     ->relationship()

                        //                     ->label('Divisions')

                        //                     ->schema([

                        //                           Select::make('division_id')
                        //                     ->live()
                        //                     ->relationship(name: 'division', titleAttribute: 'title')
                        //                     ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title} - {$record->abbreviation}")
                        //                     ->searchable()
                        //                     ->label('Choose Division')
                        //                     ->preload()
                        //                     ->native(false)
                        //                     ->columnSpanFull()
                        //                     ->distinct()
                        //                     ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                        //                 Repeater::make('project_division_categories')

                        //                     ->relationship()

                        //                     ->label('Division Categories')
                        //                     ->columns([
                        //                         'sm' => 3,
                        //                         'xl' => 6,
                        //                         '2xl' => 9,
                        //                     ])
                        //                     ->schema([


                        //                         Select::make('from')
                        //                             ->options([

                        //                                 'Direct Cost' => 'Direct Cost',
                        //                                 'Indirect Cost' => 'Indirect Cost',
                        //                             ])
                        //                             ->distinct()
                        //                             ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        //                             ->columnSpanFull()
                        //                             ->native(false)
                        //                             ->searchable(),
                        //                         // Select::make('division_category_id')
                        //                         //     ->relationship(name: 'division_category', titleAttribute: 'title')
                        //                         //     ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                        //                         //     ->searchable()
                        //                         //     ->label('Choose Category')
                        //                         //     ->preload()
                        //                         //     ->native(false)
                        //                         //     ->columnSpanFull()

                        //                         //     ->distinct()
                        //                         //     ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        //                         //     ->live()
                        //                         //     ->createOptionForm([
                        //                         //         TextInput::make('title')
                        //                         //             ->required(),
                        //                         //     ])
                        //                         //     ,



                        //                         Repeater::make('project_division_sub_category_expenses')
                        //                             ->live()
                        //                             ->relationship()

                        //                             ->label('Division Sub Categories')
                        //                             ->columns([
                        //                                 'sm' => 3,
                        //                                 'xl' => 6,
                        //                                 '2xl' => 9,
                        //                             ])
                        //                             ->schema([

                        //                                 TextInput::make('parent_title')
                        //                                     ->label('Parent Title')

                        //                                     ->live()
                        //                                     ->maxLength(191)
                        //                                     ->columnSpanFull()

                        //                                     ->hidden(fn (Get $get) => $get('../../from') === 'Indirect Cost' ? false : true),

                        //                                 TextInput::make('title')
                        //                                     ->label('Title')
                        //                                     ->required()
                        //                                     ->live()
                        //                                     ->maxLength(191)
                        //                                     ->columnSpanFull(),


                        //                                 Repeater::make('fourth_layers')
                        //                                     ->live()
                        //                                     ->relationship()

                        //                                     ->label('Forth Layers')
                        //                                     ->columns([
                        //                                         'sm' => 3,
                        //                                         'xl' => 6,
                        //                                         '2xl' => 9,
                        //                                     ])
                        //                                     ->schema([
                        //                                         TextInput::make('title')
                        //                                             ->label('Fourth  Title')
                        //                                             ->required()
                        //                                             ->maxLength(191)
                        //                                             ->columnSpanFull(),

                        //                                             TextInput::make('amount')

                        //                                             ->mask(RawJs::make('$money($input)'))
                        //                                             ->stripCharacters(',')
                        //                                             ->numeric()
                        //                                                 // ->mask(RawJs::make('$money($input)'))
                        //                                                 // ->stripCharacters(',')
                        //                                                 ->prefix('₱')
                        //                                                 ->numeric()
                        //                                                 // ->maxValue(9999999999)
                        //                                                 ->default(0)
                        //                                                 ->columnSpanFull()
                        //                                                 ->required(),
                        //                                     ])->columnSpanFull(),
                        //                             ])

                        //                             ->columnSpanFull()
                        //                             ->visible(fn (Get $get) => !empty($get('from')) ? true : false)


                        //                     ])
                        //                     ->columns(2)
                        //                     ->columnSpanFull()
                        //                     ->visible(fn (Get $get) => !empty($get('division_id')) ? true : false),



                        //                     ]),



                        //         ]),



                        //             ]),

                        //     ])

                        //     ->collapsed()
                        //     ->collapsible()
                        //     ->hidden(function (string $operation) {
                        //         if ($operation === 'create') {
                        //             return true;
                        //         } else {
                        //             return false;
                        //         }
                        //     }),

                        // Section::make('Other Expenses')
                        //     ->icon('heroicon-m-banknotes')
                        //     ->description('Manage other expeneses  ')
                        //     ->columnSpanFull()
                        //     ->schema([
                        //         Repeater::make('expenses')

                        //             ->relationship()
                        //             ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                        //                 return $data;
                        //             })

                        //             ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {


                        //                 return $data;
                        //             })
                        //             ->label('Expenses')
                        //             ->columns([
                        //                 'sm' => 3,
                        //                 'xl' => 6,
                        //                 '2xl' => 9,
                        //             ])
                        //             ->schema([
                        //                 TextInput::make('description')
                        //                     ->label('Description')
                        //                     ->columnSpan(3)
                        //                     ->required()
                        //                     ->maxLength(191),
                        //                 TextInput::make('amount')
                        //                     ->required()
                        //                     ->mask(RawJs::make('$money($input)'))
                        //                     ->stripCharacters(',')
                        //                     ->numeric()
                        //                     ->live()
                        //                     ->debounce(1000)
                        //                     ->afterStateUpdated(function (Get $get, Set $set) {
                        //                         self::updateTotal($get, $set);
                        //                     })
                        //                     ->prefix('₱ ')

                        //                     ->columnSpan(3)
                        //                     ->default(0),


                        //                 FileUpload::make('financial_statements')
                        //                     ->columnSpan(3)

                        //                     // ->columnSpanFull()
                        //                     // ->image()
                        //                     ->preserveFilenames()

                        //                     ->label('Financial Statement')
                        //                     ->disk('public')
                        //                     ->directory('project-expenses-files'),





                        //             ])
                        //             ->deleteAction(
                        //                 fn (Action $action) => $action->requiresConfirmation(),
                        //                 fn (Get $get, Set $set) => self::updateTotals($get, $set)
                        //             )
                        //             ->columnSpanFull()
                        //             ->columns(2)
                        //             ->afterStateUpdated(function (Get $get, Set $set) {
                        //                 self::updateTotal($get, $set);
                        //             })
                        //             // ->collapsed()
                        //             // ->collapsible()
                        //             ->reorderable(true),
                        //     ])
                        //     //  ->hidden(fn (string $operation): bool => $operation === 'create')
                        //     ->columnSpanFull()
                        //     ->collapsed()
                        //     ->collapsible()
                        //     ->hidden(function (string $operation) {
                        //         if ($operation === 'create') {
                        //             return true;
                        //         } else {
                        //             return false;
                        //         }
                        //     }),


                        Section::make('File Attachments')
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

                        Section::make('Program Overview')
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
                                    ->columnSpan(3)
                                    ->columnSpanFull()
                                    // ->maxLength(191)
                                    ->readOnly(),
                                TextInput::make('program_budget_overview')
                                    ->label('Budget')
                                    // ->default(0)
                                    ->prefix('₱ ')
                                    // ->numeric()
                                    ->columnSpanFull()

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
                                    ->label('Remaining')
                                    // ->prefix('₱ ')
                                    // ->numeric()
                                    ->columnSpanFull()

                                    // ->maxLength(191)
                                    ->readOnly(),
                            ]),

                        // Section::make('Overview')

                        // ->columnSpanFull()
                        // ->schema([

                        //     // TextInput::make('program_leader_overview')
                        //     // ->label('Program Leader')
                        //     // // ->prefix('₱ ')
                        //     // // ->numeric()
                        //     // ->columnSpan(3)

                        //     // ->columnSpanFull()
                        //     // // ->maxLength(191)
                        //     // ->readOnly(),





                        // ]),

                        Section::make('Project Overview')
                            //  ->icon('heroicon-m-square-3-stack-3d')
                            // ->description('Manage and organize project expenses here. You can only add expense in edit')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('current_duration_overview')
                                    ->label('Current Duration')
                                    // ->prefix('₱ ')
                                    // ->numeric()
                                    ->columnSpan(3)

                                    ->columnSpanFull()
                                    // ->maxLength(191)
                                    ->readOnly(),
                                // Placeholder::make('duration')
                                TextInput::make('duration_overview')
                                    ->label('Total Duration')
                                    // ->prefix('₱ ')
                                    // ->numeric()
                                    ->columnSpan(3)

                                    ->columnSpanFull()
                                    // ->maxLength(191)
                                    ->readOnly(),

                                TextInput::make('project_fund')
                                    ->label('Allocated Amount')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->numeric()
                                    ->columnSpan(3)
                                    ->default(0)
                                    // ->maxLength(191)
                                    ->readOnly(),
                                // TextInput::make('total_expenses')
                                //     ->label('Total Expenses')
                                //     ->mask(RawJs::make('$money($input)'))
                                //     ->stripCharacters(',')
                                //     ->numeric()
                                //     ->columnSpan(3)
                                //     ->default(0)
                                //     // ->maxLength(191)
                                //     ->readOnly()
                                //     ->rules([
                                //         fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                //             $allocatedFund = (float) str_replace(',', '', $get('allocated_fund'));
                                //             $cvalue = (float) str_replace(',', '', $value);

                                //             // Assuming you want to check if $cvalue is greater than the allocated fund or less than 0
                                //             if ($cvalue > $allocatedFund || $cvalue < 0) {
                                //                 $fail("The expenses amount should not exceed the allocated fund or be less than 0");
                                //             }
                                //         },
                                //     ]),

                            ]),
                    ])
                    // ->hidden(fn (string $operation): bool => $operation === 'create')
                    ->columnSpan(['lg' => 1])
                    ->hidden(function (string $operation) {
                        if ($operation === 'create') {
                            return true;
                        } else {
                            return false;
                        }
                    }),
                // Placeholder::make('documentation')
                // // ->live()
                // ->content(function(Get $get){

                //     return new HtmlString("<h1 style='font: size 100rem; color:red'>".$get('program_id')."</h1>");
                // }),







                // Section::make('Project Documents')
                // ->icon('heroicon-m-folder')
                // ->description('Manage and organize your Project documents. Upload files here')
                // ->columnSpan(2)
                // // ->aside()
                // ->schema([
                //     Repeater::make('files')

                //         ->relationship()
                //         ->label('Documents')
                //         ->schema([
                //             TextInput::make('file_name')
                //                 ->label('Name')
                //                 ->maxLength(191),
                //             FileUpload::make('file')

                //                 // ->columnSpanFull()
                //                 // ->image()
                //                 ->preserveFilenames()

                //                 ->label('File')
                //                 ->disk('public')
                //                 ->directory('program-files')
                //         ])
                //         ->deleteAction(
                //             fn (Action $action) => $action->requiresConfirmation(),
                //         )
                //         ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                //             // $data['user_id'] = auth()->id();

                //             return $data;
                //         })
                //         ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                //             // $filePath = storage_path('app/public/' . $data['file']);


                //             $filePath = storage_path('app/public/' . $data['file']);

                //             $fileInfo = [
                //                 'file' => $data['file'],
                //                 'file_name' => $data['file_name'],
                //                 'file_type' => mime_content_type($filePath),
                //                 'file_size' => call_user_func(function ($bytes) {
                //                     $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                //                     $i = 0;

                //                     while ($bytes >= 1024 && $i < count($units) - 1) {
                //                         $bytes /= 1024;
                //                         $i++;
                //                     }

                //                     return round($bytes, 2) . ' ' . $units[$i];
                //                 }, filesize($filePath)),
                //             ];
                //             return $fileInfo;
                //             // $data['user_id'] = auth()->id();

                //             // return $data;
                //         })
                //         ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {


                //             $filePath = storage_path('app/public/' . $data['file']);

                //             $fileInfo = [
                //                 'file' => $data['file'],
                //                 'file_name' => $data['file_name'],
                //                 'file_type' => mime_content_type($filePath),
                //                 'file_size' => call_user_func(function ($bytes) {
                //                     $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                //                     $i = 0;

                //                     while ($bytes >= 1024 && $i < count($units) - 1) {
                //                         $bytes /= 1024;
                //                         $i++;
                //                     }

                //                     return round($bytes, 2) . ' ' . $units[$i];
                //                 }, filesize($filePath)),
                //             ];

                //             // dd($fileInfo);
                //             // dd($data);

                //             return $fileInfo;
                //         })
                //         // ->collapsed()
                //         // ->collapsible()
                //         ->reorderable(true)
                //         ->columnSpanFull()
                //         ->columns(2)
                //         ->defaultItems(0)
                //         ->addActionLabel('Add Documents')


                // ])
                // ->collapsed()
                // ->collapsible()
                // ,

            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                    // ->numeric()
                    ->prefix('₱ ')
                    ->sortable(),
                TextColumn::make('start_date')
                    ->date()
                    ->label('Expected To Start Date')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Expected To End Date')
                    ->date()
                    ->sortable(),

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
                    Tables\Actions\Action::make('budget_division')->label('Budget Division')->icon('heroicon-m-banknotes')->url(fn (Model $record): string => ProjectResource::getUrl('budget-division', ['record'=> $record])),



                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            // ->groups([
            //     TGroup::make('program.title')
            //         ->titlePrefixedWithLabel(false)
            //         ->getTitleFromRecordUsing(fn (Model $record): string => $record->title ?  ucfirst($record->title) : '')
            //         ->label('Program')
            //         ->collapsible()
            //         ,


            // ])
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
            // 'budget-division' => Pages\BudgetDivision::route('/{record}/budget-division'),
            'manage-quarter-year' => Pages\ManageYearQuarter::route('/{record}/year-quarters'),
            'create-quarter' => Pages\ManageQuarter::route('/{record}/quarter/create'),
            'quarter-list' => Pages\ProjectYearQuarterList::route('/{record}/quarters'),
            'view' => Pages\ViewProject::route('/{record}'),
        ];
    }

    public static function updateProgramOverviewDetails(Get $get, Set $set)
    {

        //  dd($get('program_id'));

        if (!empty($get('program_id'))) {
            $program = Program::find($get('program_id'));
            if (!empty($program)) {
                $set('program_name_overview', $program->title);
                $set('program_budget_overview', number_format($program->total_budget));
                $set('program_use_budget_overview', $program->total_usage);
                $set('program_remaining_budget_overview', number_format($program->total_budget - $program->total_usage));
            } else {
                $set('program_name_overview', null);
                $set('program_budget_overview', null);
                $set('program_use_budget_overview', null);
                $set('program_remaining_budget_overview', null);
            }
        } else {
            $set('program_name_overview', null);
            $set('program_budget_overview', null);
            $set('program_use_budget_overview', null);
            $set('program_remaining_budget_overview', null);
        }
    }
    public static function updateLeftAllocated(Get $get, Set $set)
    {

        $allocatedFund =  (float) str_replace(',', '', $get('allocated_fund'));
        $set('project_fund', number_format($allocatedFund));
        self::updateTotal($get, $set);

        // $set('total_expenses', (int)$allocatedFund);
        //     $allocatedFund = floatval($get('allocated_fund'));
        // $set('project_fund', number_format($allocatedFund));
        // $set('total_expenses', (int)$allocatedFund);
        //self::updateTotal($get, $set);


    }

    public static function updateTotal(Get $get, Set $set)
    {


        $current_fund = (float) str_replace(',', '', $get('allocated_fund')); // Convert to float
        $expenses = collect($get('expenses'))->filter(fn ($item) => !empty($item['amount']));

        $totalAmount = $expenses->sum(function ($item) {
            return (float) str_replace(',', '', $item['amount']);
        });

        // $left_fund = $current_fund - $totalAmount;

        $set('total_expenses', number_format($totalAmount, 2));

        // $current_fund = (float)$get('allocated_fund'); // Convert to float
        // $expenses = collect($get('expenses'))->filter(fn ($item) => !empty($item['amount']));


        // $totalAmount = $expenses->sum(function ($item) {
        //     return (float)$item['amount'];
        // });

        // // $left_fund = $current_fund - $totalAmount;

        // $set('total_expenses', number_format($totalAmount));




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
