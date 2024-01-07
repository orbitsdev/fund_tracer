<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ProjectYear;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProjectYearResource\Pages;
use App\Filament\Resources\ProjectYearResource\RelationManagers;

class ProjectYearResource extends Resource
{
    protected static ?string $model = ProjectYear::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Program Management';

    protected static ?string $navigationLabel = 'Project Quarters';
    protected static ?string $modelLabel = 'Project Quarters';
    protected static ?int $navigationSort = 5;
    protected static bool $shouldRegisterNavigation = false;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('year_id')
                    ->relationship(name: 'year', titleAttribute: 'title')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->label('Choose Year'),


                // Forms\Components\TextInput::make('year_id')
                //     ->numeric(),
                // Forms\Components\TextInput::make('project_id')
                //     ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListProjectYears::route('/'),
            'create' => Pages\CreateProjectYear::route('/create'),
            'edit' => Pages\EditProjectYear::route('/{record}/edit'),
        ];
    }
}
