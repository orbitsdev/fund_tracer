<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonitoringAgencyResource\Pages;
use App\Filament\Resources\MonitoringAgencyResource\RelationManagers;
use App\Models\MonitoringAgency;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MonitoringAgencyResource extends Resource
{
    protected static ?string $model = MonitoringAgency::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static bool $canCreateAnother = false;
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?int $navigationSort = 6;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->columnSpanFull()->unique(ignoreRecord:true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->button()->outlined(),
                Tables\Actions\DeleteAction::make()->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMonitoringAgencies::route('/'),
        ];
    }
}
