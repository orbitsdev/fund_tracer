<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Models\ProjectQuarter;
use Filament\Resources\Resource;
use Illuminate\Contracts\View\View;
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

    // public function getHeader(): ?View
    // {
    //     return view('filament.settings.custom-header',['title'=> 'Edit Project Quarter', 'first'=> 'Project Quarters' ,'second'=> 'Edit']);
    // }

public static function form(Form $form): Form
{


    return $form
        ->schema([

                Hidden::make('project_year_id'),





                Select::make('quarter_id')

                    // ->required()
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get,  Model $record) {
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
                    ->disabled(),


                Repeater::make('quarter_expense_budget_divisions')

                    ->relationship()
                    ->label('Budget Divisions')
                    ->addActionLabel('Budget Division')
                    ->schema([
                        Select::make('project_devision_id')
                            ->required()
                            ->required()
                            ->live()
                            ->relationship(
                                name: 'project_division',
                                titleAttribute: 'title',
                                // modifyQueryUsing: fn (Builder $query, Get $get, Set $set, Model $record) => $query->where('project_id',      $record->project_quarter->project_year->project_id)
                            )
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->division->title}")
                            ->searchable()
                            ->label('Division')
                            ->preload()
                            ->native(false)
                            ->columnSpanFull()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),



                        Repeater::make('direct_cost_expenses')
                            ->addActionLabel('Direct Cost')
                            ->relationship(
                                'quarter_expenses',
                                modifyQueryUsing: fn (Builder $query, Get $get, Set $set) =>

                                $query->whereHas('fourth_layer.project_division_sub_category_expense.project_division_category', function ($query) {
                                    $query->where('from', 'Direct Cost');
                                })



                            )
                            ->label('Direct Cost Expenses')
                            ->columns([
                                'sm' => 3,
                                'xl' => 6,
                                '2xl' => 8,
                            ])
                            ->schema([

                                Select::make('fourth_layer_id')
                                    ->required()
                                    ->required()
                                    ->live()
                                    ->relationship(
                                        name: 'fourth_layer',
                                        titleAttribute: 'title',
                                        modifyQueryUsing: fn (Builder $query, Get $get, Set $set) =>

                                        $query->whereHas('project_division_sub_category_expense.project_division_category', function ($query) use ($get) {
                                            $query->where('from', 'Direct Cost');
                                        }),
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                                    ->searchable()
                                    ->label('Expenses')
                                    ->preload()
                                    ->native(false)
                                    ->columnSpan(4)

                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {

                                    }),


                                TextInput::make('amount')

                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')

                                    // ->mask(RawJs::make('$money($input)'))
                                    // ->stripCharacters(',')
                                    ->prefix('₱')
                                    ->numeric()
                                    // ->maxValue(9999999999)
                                    ->default(0)
                                    ->columnSpan(4)
                                    ->required(),

                            ])
                            ->collapsible()
                            ->columnSpanFull()
                            ->visible(function (Get $get, $record) {
                                if(!empty($get('project_devision_id'))){
                                    dd($record);
                                    return true;
                                }else{
                                    return false;
                                }
                            }),
                            // ->visible(fn (Get $get) => !empty($get('project_devision_id')) ? true : false),

                                    // Repeater::make('indirect_cost_expenses_sksu')
                                    // ->addActionLabel('IC SKSU')

                                    //     ->relationship(
                                    //         'quarter_expenses',
                                    //         modifyQueryUsing: fn (Builder $query, Get $get, Set $set) =>

                                    //         $query->whereHas('fourth_layer.project_division_sub_category_expense', function ($query) {
                                    //             $query
                                    //                 ->where('parent_title', 'SKSU')
                                    //                 ->whereHas('project_division_category', function ($query) {
                                    //                     $query->where('from', 'Indirect Cost');
                                    //                 });
                                    //         })
                                    //     )
                                    //     ->label('Indrect Cost Expenses SKSU')
                                    //     ->columns([
                                    //         'sm' => 3,
                                    //         'xl' => 6,
                                    //         '2xl' => 8,
                                    //     ])
                                    //     ->schema([
                                    //         Select::make('fourth_layer_id')
                                    //             ->required()
                                    //             ->required()
                                    //             ->live()
                                    //             ->relationship(

                                    //                 name: 'fourth_layer',
                                    //                 titleAttribute: 'title',

                                    //                 modifyQueryUsing: fn (Builder $query, Get $get, Set $set, Model $record) =>

                                    //                 $query->whereHas('project_division_sub_category_expense',function($query)use ($get ,$set, $record){

                                    //                     $query->where('parent_title', 'SKSU')

                                    //                     ->whereHas('project_division_category', function ($query) use ($get, $set, $record) {
                                    //                         $query->where('from', 'Indirect Cost');

                                    //                     });
                                    //                 }),
                                    //             )
                                    //             ->getOptionLabelFromRecordUsing(function (Model $record) {
                                    //                 return $record->title;
                                    //             })
                                    //             ->searchable()
                                    //             ->label('Expenses')
                                    //             ->preload()
                                    //             ->native(false)
                                    //             ->columnSpan(4)

                                    //             ->distinct()
                                    //             ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                    //         TextInput::make('amount')

                                    //             ->mask(RawJs::make('$money($input)'))
                                    //             ->stripCharacters(',')


                                    //             ->prefix('₱')
                                    //             ->numeric()
                                    //             // ->maxValue(9999999999)
                                    //             ->default(0)
                                    //             ->columnSpan(4)
                                    //             ->required(),

                                    //     ])
                                    //     ->collapsible()

                                    //     ->columnSpanFull()
                                    //     ->visible(fn (Get $get) => !empty($get('project_devision_id')) ? true : false),
                        //             Repeater::make('indirect_cost_expenses_pcaarrd')
                        //             ->addActionLabel('IC PCAARRD')
                        //                 ->relationship(
                        //                     'quarter_expenses',
                        //                     modifyQueryUsing: fn (Builder $query, Get $get, Set $set) =>

                        //                     $query->whereHas('fourth_layer.project_division_sub_category_expense', function ($query) {
                        //                         $query
                        //                             ->where('parent_title', 'PCAARRD')
                        //                             ->whereHas('project_division_category', function ($query) {
                        //                                 $query->where('from', 'Indirect Cost');
                        //                             });
                        //                     })
                        //                 )
                        //                 ->label('Indrect Cost Expenses PCAARRD')
                        //                 ->columns([
                        //                     'sm' => 3,
                        //                     'xl' => 6,
                        //                     '2xl' => 8,
                        //                 ])
                        //                 ->schema([
                        //                     Select::make('fourth_layer_id')
                        //                         ->required()
                        //                         ->required()
                        //                         ->live()
                        //                         ->relationship(

                        //                             name: 'fourth_layer',
                        //                             titleAttribute: 'title',

                        //                             modifyQueryUsing: fn (Builder $query, Get $get, Set $set, Model $record) =>

                        //                             $query->whereHas('project_division_sub_category_expense',function($query)use ($get ,$set, $record){

                        //                                 $query->where('parent_title', 'PCAARRD')

                        //                                 ->whereHas('project_division_category', function ($query) use ($get, $set, $record) {
                        //                                     $query->where('from', 'Indirect Cost')
                        //                                         ->where('project_devision_id', $get('../../project_devision_id'));
                        //                                         // ->whereHas('project_devision', function ($query) use($record) {
                        //                                         //     $query->where('project_id', $record->quarter_expense_budget_division->project_division->project_id);
                        //                                         // });
                        //                                 });
                        //                             }),
                        //                         )
                        //                         ->getOptionLabelFromRecordUsing(function (Model $record) {
                        //                             return $record->title;
                        //                         })
                        //                         ->searchable()
                        //                         ->label('Expenses')
                        //                         ->preload()
                        //                         ->native(false)
                        //                         ->columnSpan(4)

                        //                         ->distinct()
                        //                         ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                        //                     TextInput::make('amount')

                        //                         ->mask(RawJs::make('$money($input)'))
                        //                         ->stripCharacters(',')


                        //                         ->prefix('₱')
                        //                         ->numeric()
                        //                         // ->maxValue(9999999999)
                        //                         ->default(0)
                        //                         ->columnSpan(4)
                        //                         ->required(),

                        //                 ])
                        //                 ->collapsible()

                        //                 ->columnSpanFull()
                        //                 ->visible(fn (Get $get) => !empty($get('project_devision_id')) ? true : false),

                    ])
                    ->columnSpanFull()

                    ->visible(function (Get $get, Model $record) {
                        return !empty($get('quarter_id')) && $record->project_year &&
                               $record->project_year->project &&
                               $record->project_year->project->project_divisions->isNotEmpty();
                    }),







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
            // 'create' => Pages\CreateProjectQuarter::route('/create'),
            'edit' => Pages\EditProjectQuarter::route('/{record}/edit'),
            'edit-expenses' => Pages\EditQuarterExpenses::route('/{record}/edit/expenses'),
        ];
    }
}
