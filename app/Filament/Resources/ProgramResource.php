<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Program;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\ProgramResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Filament\Resources\ProgramResource\RelationManagers;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Program Information')
                    ->icon('heroicon-m-pencil-square')
                    ->description('Provide program details below to better understand and support funding needs.')




                    ->schema([
                      TextInput::make('name')
                            ->label('Title')
                            ->maxLength(191),
                      Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                      TextInput::make('budget')
                            ->prefix('₱ ')
                            ->numeric()
                            ->default(0),
                           
                            Section::make('Programs Documents')
                            ->icon('heroicon-m-folder')
                            ->description('Manage and organize your program documents. Upload files here')
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
                                            'file_name'=> $data['file_name'],
                                            'file_type' => mime_content_type($filePath),
                                            'file_size' => call_user_func(function($bytes) {
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
                                            'file_name'=> $data['file_name'],
                                            'file_type' => mime_content_type($filePath),
                                            'file_size' => call_user_func(function($bytes) {
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
                                    ->columnSpanFull()
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Documents')
                                    
                            ]),
                       
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
              TextColumn::make('name')
                    ->label('Program Name')
                    ->searchable(),
              TextColumn::make('budget')
                    ->prefix('₱ ')
                    ->numeric()
                    // ->badge()
                    ->sortable(),
              TextColumn::make('description')
                    ->wrap(),


              TextColumn::make('created_at')
                    ->date()
                    ->label('Date Created')
                    ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('files')
                    ->formatStateUsing(fn($state) => $state->file ? $state->file_name : null)
                    ->label('Documents')
    ->listWithLineBreaks()
    ->wrap()
    ->bulleted()
    // ->badge()



            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            'index' => Pages\ListPrograms::route('/'),
            'create' => Pages\CreateProgram::route('/create'),
            'edit' => Pages\EditProgram::route('/{record}/edit'),
        ];
    }
}
