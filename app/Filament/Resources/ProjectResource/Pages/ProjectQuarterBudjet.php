<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Division;
use Filament\Forms\Form;
use App\Models\FourthLayer;
use Filament\Support\RawJs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProjectResource;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;

use Illuminate\Database\Eloquent\Builder;
class ProjectQuarterBudjet extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected static ?string $title = 'Quarters Expenses';



    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {

        // $data['last_edited_by_id'] = auth()->id();

        return $data;
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema(
                [

                    Repeater::make('project_years')

                        ->relationship()

                        ->label('Years')

                        ->schema([

                            Select::make('year_id')
                                ->required()
                                ->live()

                                // ->options(Quarter::pluck('title','id'))
                                ->relationship(name: 'year', titleAttribute: 'title')
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                                ->searchable()
                                ->label('Select Year')
                                ->preload()
                                ->native(false)
                                ->columnSpanFull()
                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                            Repeater::make('project_quarters')

                                ->relationship()

                                ->label('Quarters')

                                ->schema([
                                    Select::make('quarter_id')

                                        // ->required()
                                        // ->unique( ignoreRecord: true,modifyRuleUsing: function (Unique $rule, Get $get,  Model $record) {
                                        //     return $rule->where('quarter_id', $get('quarter_id'))->where('project_year_id', $record->id);
                                        // })
                                        ->required()
                                        ->live()
                                        // ->options(Quarter::pluck('title','id'))
                                        ->relationship(name: 'quarter', titleAttribute: 'title')
                                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                                        ->searchable()
                                        ->label('Select Quarter')
                                        ->preload()
                                        ->native(false)
                                        ->columnSpanFull()
                                        ->distinct()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    // ->disable()
                                    // ->disabled()
                                    ,
                                    Repeater::make('quarter_expense_budget_divisions')

                                        ->relationship()

                                        ->label('Budget Divisions')

                                        ->schema([
                                            Select::make('project_devision_id')
                                                ->required()
                                                ->required()
                                                ->live()
                                                ->relationship(name: 'project_division', titleAttribute: 'title')
                                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->division->title}")
                                                ->searchable()
                                                ->label('Division')
                                                ->preload()
                                                ->native(false)
                                                ->columnSpanFull()
                                                ->distinct()
                                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                                Repeater::make('direct_cost_expenses')
                                                ->relationship('quarter_expenses')
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
                                                         modifyQueryUsing: fn (Builder $query , Get $get, Set $set) => $query->whereHas('project_division_sub_category_expense.project_division_category', function($query) use($get ,$set){
                                                            $query->where('from', 'Direct Cost')

                                                            // ->where('project_devision_id',   $get('../../project_devision_id'))

                                                            ;
                                                        }),
                                                         )
                                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                                                    ->searchable()
                                                    ->label('Expenses')
                                                    ->preload()
                                                    ->native(false)
                                                    ->columnSpan(4)

                                                    ->distinct()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

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
                                                ->visible(fn (Get $get) => !empty($get('project_devision_id')) ? true : false),

                                                Repeater::make('indirect_cost_expenses')
                                                ->relationship('quarter_expenses')
                                                ->label('Indrect Cost Expenses')
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
                                                        modifyQueryUsing: fn (Builder $query , Get $get, Set $set) => $query->whereHas('project_division_sub_category_expense.project_division_category', function($query) use($get,$set){
                                                            $query->where('from', 'Indirect Cost')->where('project_devision_id', $get('../../project_devision_id'));;
                                                        }),
                                                        )
                                                    ->getOptionLabelFromRecordUsing(function (Model $record) {
                                                        return $record->title;
                                                    })
                                                    ->searchable()
                                                    ->label('Expenses')
                                                    ->preload()
                                                    ->native(false)
                                                    ->columnSpan(4)

                                                    ->distinct()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

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
                                                ->visible(fn (Get $get) => !empty($get('project_devision_id')) ? true : false)
                                                ,


                                        ])
                                        ->columnSpanFull()
                                        ->visible(fn (Get $get) => !empty($get('quarter_id')) ? true : false),




                                    //         Select::make('project_division_id')

                                    //         // ->required()
                                    //         // ->unique( ignoreRecord: true,modifyRuleUsing: function (Unique $rule, Get $get,  Model $record) {
                                    //         //     return $rule->where('quarter_id', $get('quarter_id'))->where('project_year_id', $record->id);
                                    //         // })
                                    //         ->required()
                                    //         ->live()
                                    //         // ->options(Quarter::pluck('title','id'))
                                    //         ->relationship(name: 'project_division', titleAttribute: 'title')
                                    //         ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->division->title}")
                                    //         ->searchable()
                                    //         ->label('Quarter')
                                    //         ->preload()
                                    //         ->native(false)
                                    //         ->columnSpanFull()
                                    //         ->distinct()
                                    //         ->disableOptionsWhenSelectedInSiblingRepeaterItems()


                                    //     ])->columnSpanFull(),





                                    // Repeater::make('project_divisions')

                                    //     ->relationship()

                                    //     ->label('Budget Divisions')

                                    //     ->schema([

                                    //         Select::make('division_id')
                                    //             ->live()
                                    //             ->options(Division::pluck('title', 'id'))
                                    //             // ->relationship(name: 'division', titleAttribute: 'title')
                                    //             // ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title} - {$record->abbreviation}")
                                    //             ->searchable()
                                    //             ->label('Choose Budget Division')
                                    //             ->preload()
                                    //             ->native(false)
                                    //             ->columnSpanFull()
                                    //             ->distinct()
                                    //             ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                    //         Repeater::make('project_division_categories')

                                    //             ->relationship()

                                    //             ->label('Division Category')
                                    //             ->columns([
                                    //                 'sm' => 3,
                                    //                 'xl' => 6,
                                    //                 '2xl' => 9,
                                    //             ])
                                    //             ->schema([


                                    //                 Select::make('from')
                                    //                     ->label('Costing Type')
                                    //                     ->options([

                                    //                         'Direct Cost' => 'Direct Cost',
                                    //                         'Indirect Cost' => 'Indirect Cost',
                                    //                     ])
                                    //                     ->live()
                                    //                     ->afterStateUpdated(function (Get $get, Set $set) {

                                    //                         $set('project_division_sub_category_expenses', []);
                                    //                     })
                                    //                     ->distinct()
                                    //                     ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    //                     ->columnSpanFull()
                                    //                     ->native(false)
                                    //                     ->searchable(),
                                    //                 // Select::make('division_category_id')
                                    //                 //     ->relationship(name: 'division_category', titleAttribute: 'title')
                                    //                 //     ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                                    //                 //     ->searchable()
                                    //                 //     ->label('Choose Category')
                                    //                 //     ->preload()
                                    //                 //     ->native(false)
                                    //                 //     ->columnSpanFull()

                                    //                 //     ->distinct()
                                    //                 //     ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    //                 //     ->live()
                                    //                 //     ->createOptionForm([
                                    //                 //         TextInput::make('title')
                                    //                 //             ->required(),
                                    //                 //     ])
                                    //                 //     ,



                                    //                 Repeater::make('project_division_sub_category_expenses')
                                    //                     ->live()
                                    //                     ->relationship()

                                    //                     ->label('Budget Division Expenses')
                                    //                     ->columns([
                                    //                         'sm' => 3,
                                    //                         'xl' => 6,
                                    //                         '2xl' => 8,
                                    //                     ])
                                    //                     ->schema([


                                    //                         Select::make('fourth_layer_id')
                                    //                             ->live()
                                    //                             ->afterStateUpdated(function (Get $get, Set $set) {




                                    //                                 // self::updateLeftAllocated($get, $set);
                                    //                                 // dd($this->getRecord()->id);
                                    //                             })
                                    //                             ->options(function (Get $get, Set $set) {
                                    //                                 return FourthLayer::whereHas('project_division_sub_category_expense.project_division_category', function ($query) use ($get) {
                                    //                                     $query->where('from', $get('../../from'))->whereHas('project_devision', function ($query) {
                                    //                                         $query->where('project_id', $this->getRecord()->id);
                                    //                                     });
                                    //                                 })->pluck('title', 'id');
                                    //                             })

                                    //                             ->required()
                                    //                             ->searchable()
                                    //                             ->label('Choose Budget Division')
                                    //                             ->preload()
                                    //                             ->native(false)
                                    //                             ->columnSpan(4)
                                    //                             ->distinct()
                                    //                             ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                    //                             TextInput::make('amount')

                                    //                             ->mask(RawJs::make('$money($input)'))
                                    //                             ->stripCharacters(',')

                                    //                             // ->mask(RawJs::make('$money($input)'))
                                    //                             // ->stripCharacters(',')
                                    //                             ->prefix('₱')
                                    //                             ->numeric()
                                    //                             // ->maxValue(9999999999)
                                    //                             ->default(0)
                                    //                             ->columnSpan(4)
                                    //                             ->required(),



                                    //                     ])
                                    //                     // ->collapsed()
                                    //                     // ->collapsible()
                                    //                     ->minItems(0)
                                    //                     ->columnSpanFull()
                                    //                     ->visible(fn (Get $get) => !empty($get('from')) ? true : false)


                                    //             ])
                                    //             ->columns(2)
                                    //             ->columnSpanFull()
                                    //             ->visible(fn (Get $get) => !empty($get('division_id')) ? true : false),



                                    //     ])->columnSpanFull(),

                                    // Select::make('parent_title')
                                    // ->options([

                                    //     'SKSU' => 'SKSU',
                                    //     'PCAARRD' => 'PCAARRD',
                                    // ])
                                    // ->label('Source')
                                    // ->columnSpanFull(),



                                ])
                                ->visible(fn (Get $get) => !empty($get('year_id')) ? true : false),

                        ])
                        ->columnSpanFull(),
                ]
            );
    }
}
