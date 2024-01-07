<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Models\ProjectQuarter;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProjectQuarterResource\Pages;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use App\Filament\Resources\ProjectQuarterResource\RelationManagers;

class ProjectQuarterResource extends Resource
{
    protected static ?string $model = ProjectQuarter::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;



    public static function form(Form $form): Form
    {


        return $form
            ->schema([

                Hidden::make('project_year_id'),

// TextInput::make('quarter_id')
// ->readonly()
// ->columnSpanFull()
// ->disabled()



                Select::make('quarter_id')

                    // ->required()
                    ->unique(modifyRuleUsing: function (Unique $rule, Get $get,  Model $record) {
                        return $rule->where('quarter_id', $get('quarter_id'))->where('project_year_id', $record->id);
                    })

                    ->live()
                    // ->options(Quarter::pluck('title','id'))
                    ->relationship(name: 'quarter', titleAttribute: 'title')
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                    ->searchable()
                    ->label('Quarter')
                    ->preload()
                    ->native(false)
                    ->columnSpanFull()
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    // ->disable()
                    ->disabled()
                    ,


                 Repeater::make('project_divisions')

                    ->relationship()

                    ->addActionLabel('Add Category')
                    ->label('Expenses By Categories')
                    ->schema([
                        Hidden::make('project_id'),
                        Select::make('division_id')
                            ->live()
                            // ->options(Division::pluck('title','id'))
                            ->relationship(name: 'division', titleAttribute: 'title')
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title} - {$record->abbreviation}")
                            ->searchable()
                            ->label('Choose Category')
                            ->preload()
                            ->native(false)
                            ->columnSpanFull()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                        Repeater::make('project_division_categories')

                            ->relationship()

                            ->columns([
                                'sm' => 3,
                                'xl' => 6,
                                '2xl' => 9,
                            ])
                            ->schema([


                                Select::make('from')
                                ->label('Cost Type')
                                    ->options([

                                        'Direct Cost' => 'Direct Cost',
                                        'Indirect Cost' => 'Indirect Cost',
                                    ])
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->columnSpanFull()
                                    ->native(false)
                                    ->searchable(),
                                // Select::make('division_category_id')
                                //     ->relationship(name: 'division_category', titleAttribute: 'title')
                                //     ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                                //     ->searchable()
                                //     ->label('Choose Category')
                                //     ->preload()
                                //     ->native(false)
                                //     ->columnSpanFull()

                                //     ->distinct()
                                //     ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                //     ->live()
                                //     ->createOptionForm([
                                //         TextInput::make('title')
                                //             ->required(),
                                //     ])
                                //     ,



                                Repeater::make('project_division_sub_category_expenses')
                                    ->live()
                                    ->relationship()

                                    ->label('Expense Categories')
                                    ->columns([
                                        'sm' => 3,
                                        'xl' => 6,
                                        '2xl' => 9,
                                    ])
                                    ->schema([

                                        TextInput::make('parent_title')
                                            ->label('Source')

                                            ->live()
                                            ->maxLength(191)
                                            ->columnSpanFull()

                                            ->hidden(fn (Get $get) => $get('../../from') === 'Indirect Cost' ? false : true),

                                        TextInput::make('title')
                                            ->label('Expense Title')
                                            ->required()
                                            ->live()
                                            ->maxLength(191)
                                            ->columnSpanFull(),


                                        TableRepeater::make('fourth_layers')
                                            ->live()
                                            ->relationship()
                                            ->label('Expenses')
                                            ->columns([
                                                'sm' => 3,
                                                'xl' => 6,
                                                '2xl' => 9,
                                            ])
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Expenses Description')
                                                    ->required()
                                                    ->maxLength(191)
                                                    ->columnSpanFull(),

                                                TextInput::make('amount')

                                                    ->mask(RawJs::make('$money($input)'))
                                                    ->stripCharacters(',')
                                                    ->numeric()
                                                    // ->mask(RawJs::make('$money($input)'))
                                                    // ->stripCharacters(',')
                                                    ->prefix('₱')
                                                    ->numeric()
                                                    // ->maxValue(9999999999)
                                                    ->default(0)
                                                    ->columnSpanFull()
                                                    ->required(),
                                            ])->columnSpanFull()
                                            ->withoutHeader()
                                            ->reorderable(true)
                                            ,
                                    ])
                                    // ->withoutHeader()
                                    ->columnSpanFull()
                                    ->visible(fn (Get $get) => !empty($get('from')) ? true : false)


                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => !empty($get('division_id')) ? true : false),



                    ]) ->columnSpanFull()


                    ->visible(fn (Get $get) => !empty($get('quarter_id')) ? true : false),

                // ...
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
