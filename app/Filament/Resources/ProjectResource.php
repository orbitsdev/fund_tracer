<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Program;
use App\Models\Project;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\ProjectResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProjectResource\RelationManagers;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Closure;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Management';


    public $data;
    // protected static bool $shouldRegisterNavigation = false;


    // public function mount(){
    //     dd('dsd');
    // }

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
                                '2xl' => 8,
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
                                    ->required()
                                    ->label('Choose Program')
                                    ->relationship(
                                        name: 'program',
                                        titleAttribute: 'title'
                                    )
                                    ->helperText(new HtmlString('Program  & Available-Budget'))
                                    // ->hintColor('primary')
                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title} - ₱ " . number_format($record->total_budget - $record->total_usage))

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
                                    ->columnSpan(4),
                                Select::make('user_id')
                                    ->relationship(
                                        name: 'manager',
                                        titleAttribute: 'first_name',
                                        modifyQueryUsing: fn (Builder $query) => $query->whereDoesntHave('managerProject'),
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}")
                                    ->searchable(['first_name', 'last_name'])
                                    // ->searchable()
                                    ->preload()
                                    ->columnSpan(4)
                                    ->required()

                                    ->native(false),

                                TextInput::make('allocated_fund')
                                    ->label('Allocated Amount')
                                    ->live()
                                    ->debounce(700)
                                    ->required()

                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateLeftAllocated($get, $set);
                                    })
                                    ->prefix('₱ ')
                                    ->numeric()
                                    ->default(0)
                                    ->columnSpan(4),



                                DatePicker::make('start_date')->date()->native(false)->columnSpan(2)
                                    ->required(),
                                DatePicker::make('end_date')->date()->native(false)->columnSpan(2)
                                    ->required(),


                                Select::make('status')
                                    ->options([
                                        'Not Started' => 'Not Started',
                                        'Planning' => 'Planning',
                                        'In Progress' => 'In Progress',
                                        'On Hold' => 'On Hold',
                                        'Cancelled' => 'Cancelled',
                                        'Under Revision' => 'Under Revision',
                                    ])
                                    ->default('In Progress')
                                    ->searchable()
                                    ->native(false)
                                    ->columnSpanFull(),

                            ])
                            ->collapsed()
                            ->collapsible(),

                        Section::make('Project Expenses')
                            ->icon('heroicon-m-banknotes')
                            ->description('Manage and organize project expenses here. You can only add expense in edit')
                            ->columnSpanFull()
                            ->schema([
                                Repeater::make('expenses')

                                    ->relationship()
                                    ->label('Expenses')
                                    ->columns([
                                        'sm' => 3,
                                        'xl' => 6,
                                        '2xl' => 9,
                                    ])
                                    ->schema([
                                        TextInput::make('description')
                                            ->label('Description')
                                            ->columnSpan(3)
                                            ->required()
                                            ->maxLength(191),
                                        TextInput::make('amount')
                                            ->required()
                                            ->live()
                                            ->debounce(1000)
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                self::updateTotal($get, $set);
                                            })
                                            ->prefix('₱ ')
                                            ->numeric()
                                            ->columnSpan(3)
                                            ->default(0),


                                        FileUpload::make('financial_statements')
                                            ->columnSpan(3)

                                            // ->columnSpanFull()
                                            // ->image()
                                            ->preserveFilenames()

                                            ->label('Financial Statement')
                                            ->disk('public')
                                            ->directory('project-expenses-files')



                                    ])
                                    ->deleteAction(
                                        fn (Action $action) => $action->requiresConfirmation(),
                                        fn (Get $get, Set $set) => self::updateTotals($get, $set)
                                    )
                                    ->columnSpanFull()
                                    ->columns(2)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateTotal($get, $set);
                                    })
                                    // ->collapsed()
                                    // ->collapsible()
                                    ->reorderable(true),
                            ])
                            //  ->hidden(fn (string $operation): bool => $operation === 'create')
                            ->columnSpanFull()
                            ->collapsed()
                            ->collapsible(),


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
                                            ->maxLength(191),
                                        FileUpload::make('file')

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
                            // ->icon('heroicon-m-banknotes')
                            // ->description('Manage and organize project expenses here. You can only add expense in edit')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('program_name_overview')
                                    ->label('Program')
                                    // ->prefix('₱ ')
                                    // ->numeric()
                                    ->columnSpan(3)
                                  
                                    // ->maxLength(191)
                                    ->readOnly(),
                                TextInput::make('program_budget_overview')
                                    ->label('Budget')
                                    ->default(0)    
                                    ->prefix('₱ ')
                                    // ->numeric()
                                    ->columnSpan(3)
                                  
                                    // ->maxLength(191)
                                    ->readOnly(),
                                TextInput::make('program_use_budged')
                                    ->label('Used Budget')
                                    // ->prefix('₱ ')
                                    // ->numeric()
                                    ->columnSpan(3)
                                  
                                    // ->maxLength(191)
                                    ->readOnly(),
                            ]),
                        Section::make('Project Overview')
                            // ->icon('heroicon-m-banknotes')
                            // ->description('Manage and organize project expenses here. You can only add expense in edit')
                            ->columnSpanFull()
                            ->schema([

                                TextInput::make('project_fund')
                                    ->label('Project Fund')
                                    // ->prefix('₱ ')
                                    // ->numeric()
                                    ->columnSpan(3)
                                    ->default(0)
                                    // ->maxLength(191)
                                    ->readOnly(),
                                TextInput::make('total_expenses')
                                    ->label('Total Expenses')
                                    // ->prefix('₱ ')
                                    // ->numeric()
                                    ->columnSpan(3)
                                    ->default(0)
                                    // ->maxLength(191)
                                    ->readOnly()
                                    ->rules([
                                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            if ($value > $get('allocated_fund') || $value < 0) {
                                                $fail("The expenses amount should not exceed the allocated fund or be less than 0");
                                            }
                                        },
                                    ]),

                            ]),
                    ])
                    // ->hidden(fn (string $operation): bool => $operation === 'create')
                    ->columnSpan(['lg' => 1]),
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
                    ->searchable(),

                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('allocated_fund')
                    // ->numeric()
                    ->prefix('₱ ')
                    ->sortable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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

            'project' => Pages\Project::route('/project'),

            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function updateProgramOverviewDetails(Get $get, Set $set)
    {

        //  dd($get('program_id'));
         $program = Program::find($get('program_id'));
        if(!empty($program)){
             $set('program_name_overview', $program->title);
             $set('program_budget_overview', number_format($program->total_budget));
             $set('program_use_budged', $program->total_usage);
        }
     
    }
    public static function updateLeftAllocated(Get $get, Set $set)
    {
        $set('project_fund', (int)$get('allocated_fund'));
        // $set('total_expenses', (int)$get('allocated_fund'));
        self::updateTotal($get, $set);
    }

    public static function updateTotal(Get $get, Set $set)
    {


        $current_fund = (float)$get('allocated_fund'); // Convert to float
        $expenses = collect($get('expenses'))->filter(fn ($item) => !empty($item['amount']));


        $totalAmount = $expenses->sum(function ($item) {
            return (float)$item['amount'];
        });

        // $left_fund = $current_fund - $totalAmount;

        $set('total_expenses', number_format($totalAmount));

        // $current_fund = $get('allocated_fund');
        // $selectedProducts = collect($get('expenses'))->filter(fn($item) => !empty($item['amount']));

        // $totalAmount = $selectedProducts->sum(function ($item) {
        //     return (float) $item['amount'];
        // });

        // $left_fund = $current_fund - $totalAmount;

        // $set('total_expenses', number_format($left_fund));



    }
}
