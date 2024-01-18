<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\DivisionCategory;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DivisionCategoryResource\Pages;
use App\Filament\Resources\DivisionCategoryResource\RelationManagers;

class DivisionCategoryResource extends Resource
{
    protected static ?string $model = DivisionCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?int $navigationSort = 3;
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Division Category')
                ->icon('heroicon-m-pencil-square')
                ->description('This will be displayed in project expenses form under division')


                ->columns([
                    'sm' => 3,
                    'xl' => 6,
                    '2xl' => 8,
                ])


                ->schema([

                    TextInput::make('title')
                          ->maxLength(191)
                          ->columnSpanFull()
                          ,
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
              TextColumn::make('title')
                    ->searchable()


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

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
            'index' => Pages\ListDivisionCategories::route('/'),
            'create' => Pages\CreateDivisionCategory::route('/create'),
            'edit' => Pages\EditDivisionCategory::route('/{record}/edit'),
        ];
    }
}
