<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
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

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?int $navigationSort = 3;

    public $data;
    // protected static bool $shouldRegisterNavigation = false;


    // public function mount(){
    //     dd('dsd');
    // }

   
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Placeholder::make('documentation')
                // // ->live()
                // ->content(function(Get $get){
                  
                //     return new HtmlString("<h1 style='font: size 100rem; color:red'>".$get('program_id')."</h1>");
                // }),
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
                            ->label('Choose Program')
                            ->relationship(name: 'program', titleAttribute: 'title')
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

                            ->native(false),

                        TextInput::make('allocated_fund')
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
                    ->collapsible()
                    ,

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
                    ->collapsible()
                    ,

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

                                ->maxLength(191),
                                TextInput::make('amount')
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
                            )
                            ->columnSpanFull()
                            ->columns(2)
                            // ->collapsed()
                            // ->collapsible()
                            ->reorderable(true)
                            ,
                    ])
                    ->hidden(fn (string $operation): bool => $operation === 'create')
                    ->columnSpanFull()
                    ->collapsed()
                    ->collapsible()

            ]);
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
                    ->numeric()
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
}
