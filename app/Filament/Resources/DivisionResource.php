<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Division;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DivisionResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DivisionResource\RelationManagers;

class DivisionResource extends Resource
{
    protected static ?string $model = Division::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $navigationGroup = 'Content Management';
     protected static ?string $navigationLabel = 'Expenses Divisions';
    protected static ?string $modelLabel = 'Expenses Divisions';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Division Information')
                ->icon('heroicon-m-pencil-square')
                ->description('This will be displayed in project expenses form as division name')


                ->columns([
                    'sm' => 3,
                    'xl' => 6,
                    '2xl' => 8,
                ])


                ->schema([

                    TextInput::make('title')->maxLength(191)->required()->columnSpan(6)
                    ->live()
                    ->debounce(700)

                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::setRecommendedAbbreviation ($get, $set);
                    })
                    ,
                    TextInput::make('abbreviation')->maxLength(191)->required()->columnSpan(2),

                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
             TextColumn::make('title')
                    ->searchable(
                        // isIndividual: true,
                    ),

             TextColumn::make('abbreviation')
                    ->searchable(),

                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListDivisions::route('/'),
            'create' => Pages\CreateDivision::route('/create'),
            'edit' => Pages\EditDivision::route('/{record}/edit'),
        ];
    }

    public static function setRecommendedAbbreviation(Get $get , Set $set){
        // dd($get('title'));

        if (!empty($get('title'))) {

            $set('abbreviation', Str::acronym($get('title')));
        }

    }
}
