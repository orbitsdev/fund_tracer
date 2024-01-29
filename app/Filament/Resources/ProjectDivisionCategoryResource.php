<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectDivisionCategoryResource\Pages;
use App\Filament\Resources\ProjectDivisionCategoryResource\RelationManagers;
use App\Models\ProjectDivisionCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectDivisionCategoryResource extends Resource
{
    protected static ?string $model = ProjectDivisionCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('project_devision_id')
                    ->numeric(),
                Forms\Components\TextInput::make('division_category_id')
                    ->numeric(),
                Forms\Components\TextInput::make('from')
                    ->maxLength(191),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project_devision_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('division_category_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('from')
                    ->searchable(),
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
            'index' => Pages\ListProjectDivisionCategories::route('/'),
            // 'create' => Pages\CreateProjectDivisionCategory::route('/create'),
            'edit' => Pages\EditProjectDivisionCategory::route('/{record}/edit'),
            'edit-category-expenses' => Pages\EditDivisionCategoryExpenses::route('/{record}/edit/expenses'),

        ];
    }
}
