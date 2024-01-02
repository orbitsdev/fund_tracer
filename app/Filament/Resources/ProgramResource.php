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
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
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

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Management';


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
                            ->label('Title')
                            ->maxLength(191)
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('total_budget')

                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()
                            // ->mask(RawJs::make('$money($input)'))
                            // ->stripCharacters(',')
                            ->prefix('₱')
                            ->numeric()
                            // ->maxValue(9999999999)
                            ->default(0)
                            ->columnSpanFull()
                            ->required(),
                        Select::make('status')
                            ->options([
                                'Pending' => 'Pending',
                                'Planning' => 'Planning',
                                'Active' => 'Active',
                                'Cancelled' => 'Cancelled',
                                'On Hold' => 'On Hold',
                                'Completed' => 'Completed',
                            ])
                            ->required()
                            ->native(false)
                            ->columnSpan(3),

                        DatePicker::make('start_date')->date()->native(false)->columnSpan(3)
                        ->live()
                        ->debounce(700)
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::calculateTotalMonthDurationn($get, $set);
                        })
                            ->required(),
                            
                        DatePicker::make('end_date')->date()->native(false)->columnSpan(3)
                        ->live()
                        ->debounce(700)
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::calculateTotalMonthDurationn($get, $set);
                        })
                            ->required(),



                    ])
                    ->collapsed()
                    ->collapsible(),

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
            
                    ->columnSpanFull()
                    ->schema([
                        
                        // Placeholder::make('duration')
                        TextInput::make('duration')
                        ->label('Program Total Months Duration')
                        // ->prefix('₱ ')
                        // ->numeric()
                        ->columnSpan(3)
                        ->columnSpanFull()
                        // ->maxLength(191)
                        ->readOnly(), 
                       
                    

                    ])

                ])->columnSpan(['lg' => 1]),

                

            ])
            ->columns(3)
            ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable(
                        isIndividual:true,

                    ),
                    TextColumn::make('total_budget')
                    ->label('Total Budget')
                    ->prefix('₱ ')
                    ->numeric()
                    // ->badge()
                    ->sortable(),
                    TextColumn::make('projects')
                    ->listWithLineBreaks()
                    ->label('Project & Allocated Fund')
                    ->wrap()
                    ->limitList(5)
                    ->expandableLimitedList()
                    ->formatStateUsing(function($state) {
                        return $state->title.' - ₱'.number_format($state->allocated_fund);
                    })
                    ->badge()
                    // ->bulleted()
                        ,

                    
                    // TextColumn::make('total_usage')
                    //     ->label('Total Usage')
                    //     ->prefix('₱ ')
                    //     ->numeric()
                    //     // ->badge()
                    //     // ->color('info')
                    //     ->sortable(),
               
               

                TextColumn::make('total_usage')
                ->label('Total Distribute')
                ->getStateUsing(function(Model $record) {

                    $totalAmount = $record->projects->sum(function ($item) {
                        return (float)$item['allocated_fund'];
                    });

                    return '₱ '.number_format($totalAmount);
                    // return whatever you need to show
                    // return $record;
                    // return  number_format($record->total_budget - $record->total_usage);
                    // // number_format($program->total_budget - $program->total_usage)
                }),





             


                TextColumn::make('files')
                    ->formatStateUsing(fn ($state) => $state->file ? $state->file_name : null)
                    ->label('Documents')
                    ->listWithLineBreaks()
                    // ->wrap()
                    ->limitList(1)
                    ->expandableLimitedList()
                    // ->badge()
                    // ->bulleted()
                    ,
                // ->badge()
                TextColumn::make('status')
                ->label('Status')
                // ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Pending' => 'gray',
                    'Planning' => 'info',
                    'Active' => 'success',
                    'Cancelled' => 'danger',
                    'On Hold' => 'warning',
                    'Completed' => 'sucess',
                    default => 'gray',
                })

                ->searchable(),

                
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
            ->groups([
                'status',
                
            ])
            ->defaultGroup('status')
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
            'edit' => Pages\EditProgram::route('/{record}/edit'),
        ];
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
            $set('duration', $totalMonths . ' months');
        }
        // $set('project_fund', number_format($get('allocated_fund')));
        // // $set('total_expenses', (int)$get('allocated_fund'));
        // self::updateTotal($get, $set);
    }
}

