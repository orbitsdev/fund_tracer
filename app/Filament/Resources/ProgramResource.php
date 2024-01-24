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
use Filament\Resources\Resource;
use App\Models\ImplementingAgency;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Group;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;

use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\ProgramResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProgramResource\RelationManagers;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Awcodes\FilamentTableRepeater\Components\TableRepeater as TBlRepeater;

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
                            ->options(ImplementingAgency::all()->pluck('title', 'id'))
                            ->searchable()
                            ->columnSpan(3)
                            ->required()
                            ->native(false),
                                            Select::make('monitoring_agency')
                            ->label('Monitoring Agency')
                            ->options(MonitoringAgency::all()->pluck('title', 'id'))
                            ->required()

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
            TBlRepeater::make('files')
            ->withoutHeader()
            ->emptyLabel('No File')
            ->columnWidths([

                'file' => '250px',
            ])
                ->relationship()
                ->label('Documents')
                ->schema([
                    TextInput::make('file_name')
                        ->label('File Description')
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
                    ->label('Program Leader')
                    ->searchable(
                        // isIndividual:true,
                    ),
                    TextColumn::make('total_budget')
                    ->label('Total Budget')
                    ->prefix('₱ ')
                    ->numeric()
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



                    TextColumn::make('projects')
                    ->listWithLineBreaks()
                    ->label('Project & Allocated Fund')
                    ->wrap()
                    ->badge()
                    ->separator(',')
                    // ->limitList(2)
                    ->words(8)
                    ->listWithLineBreaks()
                    ->expandableLimitedList()
                    ->formatStateUsing(function($state) {
                        // return $state->title.' - ₱'.number_format($state->allocated_fund);
                        return $state->title;
                    })
                    ->tooltip(function (Model $record): string {
                        return "\n" . $record->projects->map(function ($project, $index) {
                            return ($index + 1) . ". {$project->title}";
                        })->implode("\n");
                    })
                    ,



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


                TextColumn::make('start_date')

                    ->date()
                    ->label('Start')

                    ,
                TextColumn::make('end_date')


                    ->date()
                    ->label('End')

                    ,




            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('Summary Budget')->label('Summary Budget')->icon('heroicon-o-banknotes')->url(fn (Model $record): string => ProgramResource::getUrl('summary-budget', ['record'=> $record])),
                    Tables\Actions\EditAction::make(),
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

