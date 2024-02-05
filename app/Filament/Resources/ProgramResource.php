<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Program;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Models\MonitoringAgency;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\ImplementingAgency;

use Filament\Forms\Components\Group;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;

use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Forms\Components\Actions\Action;

use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\Summarizers\Average;
use App\Filament\Resources\ProgramResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use App\Filament\Resources\ProgramResource\RelationManagers;


class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Program Management';


// public function getMaxContentWidth(): MaxWidth
// {
//     return MaxWidth::Full;
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

                        TextEntry::make('title')
                            ->label('Program Title')



                            ->columnSpanFull(),
                        TextEntry::make('program_leader')


                            ->label('Program Leader')


                            ->columnSpanFull(),
                        TextEntry::make('start_date')
                            ->label('Program Start')
                            ->date(),


                        TextEntry::make('end_date')
                            ->label('Program End')
                            ->date(),
                        TextEntry::make('total_budget')
                            ->money('PHP')
                            ->label('Program Budget')
                            ->size(TextEntry\TextEntrySize::Large),

                        TextEntry::make('total_usage')
                            ->money('PHP')
                            ->label('Program Budget')
                            ->size(TextEntry\TextEntrySize::Large),
                    ]),


                Fieldset::make('Duration')
                    ->columns([
                        'sm' => 3,
                        'xl' => 6,
                        '2xl' => 9,
                    ])
                    ->schema([
                        ViewEntry::make('Total Duration')
                        ->view('infolists.components.project-duration')
                        ->columnSpanFull(),
                    ]),


                    Tabs::make('Tabs')
->tabs([
    Tabs\Tab::make('Summary Budget')
        ->schema([
            ViewEntry::make('')
                ->view('infolists.components.program-summary-budget')
                ->columnSpanFull(),
        ]),
        Tabs\Tab::make('PCAARRD IC')
        ->schema([

            ViewEntry::make('')
                ->view('infolists.components.summary-pcaardic')
                ->columnSpanFull(),
    ]),
    Tabs\Tab::make('Files')
        ->schema([
            ViewEntry::make('')
            ->view('infolists.components.files')
            ->columnSpanFull(),
        ]),


])
->activeTab(2)
->columnSpanFull(),







            ]);

  }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Group::make()
                ->schema([

                    Section::make('Program Information')
                    ->icon('heroicon-m-pencil-square')

                    ->description('Provide program details below to better understand and support funding needs.')

                    ->columns([
                        'sm' => 3,
                        'xl' => 6,
                        '2xl' => 9,
                    ])


                    ->schema([
                        TextInput::make('title')
                            ->label('Program Title')
                            ->maxLength(191)
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('program_leader')

                        ->live()
                        ->debounce(700)
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::setProgramLeader($get ,$set);
                        })
                            ->label('Program Leader')
                            ->maxLength(191)
                            ->required()
                            ->columnSpanFull(),


                        // Select::make('status')
                        //     ->options([
                        //         'Pending' => 'Pending',
                        //         'Planning' => 'Planning',
                        //         'Active' => 'Active',
                        //         'Cancelled' => 'Cancelled',
                        //         'On Hold' => 'On Hold',
                        //         'Completed' => 'Completed',
                        //     ])
                        //     ->required()
                        //     ->native(false)
                        //     ->columnSpan(3),

                        DatePicker::make('start_date')->date()->native(false)->columnSpan(3)
                        ->live()
                        ->suffixIcon('heroicon-m-calendar-days')
                        ->debounce(700)
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::calculateTotalMonthDurationn($get, $set);
                            self::setCurrentDuration($get, $set);
                        })
                            ->required(),

                        DatePicker::make('end_date')->date()->native(false)->columnSpan(3)
                        ->live()
                        ->suffixIcon('heroicon-m-calendar-days')
                        ->debounce(700)
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::calculateTotalMonthDurationn($get, $set);
                            self::setCurrentDuration($get, $set);
                        })
                            ->required(),

                            TextInput::make('duration_overview')
                            ->disabled()
                            ->label('Total Duration')
                            // ->prefix('₱ ')
                            // ->numeric()

                            ->columnSpan(3)
                            // ->maxLength(191)
                            ->readOnly(),

                            Select::make('implementing_agency')
                            ->label('Implementing Agency')
                            ->options(ImplementingAgency::all()->pluck('title', 'title'))
                            ->hint(function(){
                                if(ImplementingAgency::count() > 0){
                                    return '';
                                }else{
                                    return 'No implementing agency agency found';
                                }
                            })
                            ->searchable()
                            ->columnSpan(3)
                            ->required()
                            ->native(false),
                                            Select::make('monitoring_agency')
                            ->label('Monitoring Agency')
                            ->options(MonitoringAgency::all()->pluck('title', 'title'))
                            ->required()
                            ->hint(function(){
                                if(MonitoringAgency::count() > 0){
                                    return '';
                                }else{
                                    return 'No monitoring agency found';
                                }
                            })
                            ->columnSpan(3)
                            ->searchable()
                            ->native(false),

                            TextInput::make('total_budget')

                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')

                                // ->mask(RawJs::make('$money($input)'))
                                // ->stripCharacters(',')
                                ->prefix('₱')
                                ->numeric()
                                // ->maxValue(9999999999)
                                ->default(0)
                                ->columnSpan(3)
                                ->required(),



                    ]),


        Section::make('Programs Documents')

        ->columnSpanFull()

        ->icon('heroicon-m-folder')
        ->description('Manage and organize your program documents. Upload files here')
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

        ->collapsible(),


                ])->columnSpan(['lg' => 4]),

                Group::make()
                ->schema([

                    // Section::make('Overview')

                    // ->columnSpanFull()
                    // ->schema([

                    //     TextInput::make('program_leader_overview')
                    //     ->label('Program Leader')
                    //     // ->prefix('₱ ')
                    //     // ->numeric()
                    //     ->columnSpan(3)

                    //     ->columnSpanFull()
                    //     // ->maxLength(191)
                    //     ->readOnly(),
                    //     TextInput::make('current_duration_overview')
                    //     ->label('Current Duration')
                    //     // ->prefix('₱ ')
                    //     // ->numeric()
                    //     ->columnSpan(3)

                    //     ->columnSpanFull()
                    //     // ->maxLength(191)
                    //     ->readOnly(),
                    //     // Placeholder::make('duration')





                    // ])

                ])->columnSpan(['lg' => 1]),



            ])
            ->columns(4)
            ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable(
                        // isIndividual:true,
                    )

                    ,
                TextColumn::make('program_leader')
                ->wrap()
                    ->label('Program Leader')
                    ->searchable(
                        // isIndividual:true,
                    ),

                    TextColumn::make('total_budget')
                    ->label('Total Budget')
                    ->prefix('₱ ')
                    ->numeric()
                    // ->weight(FontWeight::Bold)
                    // ->badge()
                    ->sortable(),


                    // TextColumn::make('projects_count')
                    // ->counts('projects')
                    // ->badge()
                    // ->tooltip(function (Model $record): string {
                    //     return "\n" . $record->projects->map(function ($project, $index) {
                    //         return ($index + 1) . ". {$project->title}";
                    //     })->implode("\n");
                    // }),

                    // ViewColumn::make('projects')->view('tables.columns.project-list'),





                    // TextColumn::make('total_usage')
                    //     ->label('Total Usage')
                    //     ->prefix('₱ ')
                    //     ->numeric()
                    //     // ->badge()
                    //     // ->color('info')
                    //     ->sortable(),



                // TextColumn::make('total_usage')
                // ->label('Total Distribute')
                // ->getStateUsing(function(Model $record) {

                //     $totalAmount = $record->projects->sum(function ($item) {
                //         return (float)$item['allocated_fund'];
                //     });

                //     return '₱ '.number_format($totalAmount);
                //     // return whatever you need to show
                //     // return $record;
                //     // return  number_format($record->total_budget - $record->total_usage);
                //     // // number_format($program->total_budget - $program->total_usage)
                // }),








                // TextColumn::make('files')
                //     ->formatStateUsing(fn ($state) => $state->file ? $state->file_name : null)
                //     ->label('Documents')
                //     ->listWithLineBreaks()
                //     // ->wrap()
                //     ->limitList(1)
                //     ->expandableLimitedList()
                //     // ->badge()
                //     // ->bulleted()
                //     ,
                // // ->badge()
                // TextColumn::make('status')
                // ->label('Status')
                // // ->badge()
                // ->color(fn (string $state): string => match ($state) {
                //     'Pending' => 'gray',
                //     'Planning' => 'info',
                //     'Active' => 'success',
                //     'Cancelled' => 'danger',
                //     'On Hold' => 'warning',
                //     'Completed' => 'sucess',
                //     default => 'gray',
                // })

                // ->searchable(),




                    TextColumn::make('projects')
                    ->listWithLineBreaks()

                    ->label('Project & Allocated Fund')
                    ->wrap()
                    ->color('primary')
                    ->separator(',')
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->formatStateUsing(function($state) {
                        return $state->title.' - ₱'.number_format($state->allocated_fund);
                        // return $state->title;
                    })
                    ->tooltip(function (Model $record): string {
                        return "\n" . $record->projects->map(function ($project, $index) {
                            return ($index + 1) . ". {$project->title}";
                        })->implode("\n");
                    })
                    ,

                        TextColumn::make('start_date')

                        ->date()
                        ->label('Start')

                        ,
                    TextColumn::make('end_date')


                        ->date()
                        ->label('End')

                        ,



TextColumn::make('total_budget')
->numeric()
->summarize([
    Sum::make()->label('Total')
]),






            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label('View Details'),
                    // Tables\Actions\Action::make('Summary Budget')->label('Summary Budget')->icon('heroicon-o-banknotes')->url(fn (Model $record): string => ProgramResource::getUrl('summary-budget', ['record'=> $record])),
                    Tables\Actions\EditAction::make()->label('Update Program Details'),
                    Tables\Actions\DeleteAction::make(),
                ])
                ],
                // position: ActionsPosition::BeforeColumns
                )
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            // ->heading('Table')
            // ->description('List of programs and their scope projects with allocated budget')
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)
            ->recordUrl(null)
            ->modifyQueryUsing(fn (Builder $query) => $query->latest())
            // ->groups([
            //     'status',

            // ])
            // ->defaultGroup('status')
            ;
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
            'index' => Pages\ListPrograms::route('/'),
            'create' => Pages\CreateProgram::route('/create'),
            'summary-budget' => Pages\ProgramSummaryBudget::route('/summary-budget/{record}'),
            'edit' => Pages\EditProgram::route('/{record}/edit'),
            'view' => Pages\ViewProgram::route('/{record}'),
        ];
    }
    public static function setProgramLeader(Get $get, Set $set)
    {

         $set('program_leader_overview',$get('program_leader'));
        //   $set('program_leader_overview', $get('project_leader'));
         //  $set('project_leader_overview'. $get('project_leader'));
    }
    public static function setCurrentDuration(Get $get, Set $set)
    {

        $startDate = $get('start_date');
        $endDate = $get('end_date');
        if (!empty($startDate) && !empty($endDate)) {

            // dd(Carbon::parse($startDate)->format('F d, Y'), Carbon::parse($startDate)->format('F d, Y'));

        $currentDuration = Carbon::parse($startDate)->format('F d, Y') . ' - '. Carbon::parse($endDate)->format('F d, Y');

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

