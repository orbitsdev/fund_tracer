<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectQuarterResource\Pages;
use App\Filament\Resources\ProjectQuarterResource\RelationManagers;
use App\Models\ProjectQuarter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectQuarterResource extends Resource
{
    protected static ?string $model = ProjectQuarter::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('project_year_id')
                    ->numeric(),
                Forms\Components\TextInput::make('quarter_id')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project_year_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quarter_id')
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
            'index' => Pages\ListProjectQuarters::route('/'),
            'create' => Pages\CreateProjectQuarter::route('/create'),
            'edit' => Pages\EditProjectQuarter::route('/{record}/edit'),
        ];
    }
}
