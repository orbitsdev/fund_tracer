<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Quarter;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\QuarterResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\QuarterResource\RelationManagers;

class QuarterResource extends Resource
{
    protected static ?string $model = Quarter::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?int $navigationSort = 4;
    // protected static bool $shouldRegisterNavigation = false;
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
                    Forms\Components\TextInput::make('title')
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
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->button(),
                Tables\Actions\DeleteAction::make()->button(),
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
            'index' => Pages\ListQuarters::route('/'),
            'create' => Pages\CreateQuarter::route('/create'),
            'edit' => Pages\EditQuarter::route('/{record}/edit'),
        ];
    }
}
