<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuarterExpenseBudgetDivisionResource\Pages;
use App\Filament\Resources\QuarterExpenseBudgetDivisionResource\RelationManagers;
use App\Models\QuarterExpenseBudgetDivision;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuarterExpenseBudgetDivisionResource extends Resource
{
    protected static ?string $model = QuarterExpenseBudgetDivision::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('project_quarter_id')
                    ->numeric(),
                Forms\Components\TextInput::make('project_devision_id')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project_quarter_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project_devision_id')
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
            'index' => Pages\ListQuarterExpenseBudgetDivisions::route('/'),
            'create' => Pages\CreateQuarterExpenseBudgetDivision::route('/create'),
            'edit' => Pages\EditQuarterExpenseBudgetDivision::route('/{record}/edit'),
            'edit-division-expenses' => Pages\EditDivisionExpenses::route('/{record}/edit'),
        ];
    }
}
