<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Division;
use Filament\Forms\Form;
use App\Models\FourthLayer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProjectResource;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use Illuminate\Database\Eloquent\Model;


class ProjectQuarterBudjet extends EditRecord
{
    protected static string $resource = ProjectResource::class;



    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
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
                        ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get,  ) {
                            return $rule->where('year_id', $get('year_id'))->where('project_id',$this->getRecord()->id);
                        })
                        // ->options(Quarter::pluck('title','id'))
                        ->relationship(name: 'year', titleAttribute: 'title')
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                        ->searchable()
                        ->label('Year')
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
                            ->unique( ignoreRecord: true,modifyRuleUsing: function (Unique $rule, Get $get,  Model $record) {
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
                            // ->disabled()
                            ,

                            Repeater::make('project_divisions')

                            ->relationship()

                            ->label('Budget Divisions')

                            ->schema([

                                Select::make('division_id')
                                    ->live()
                                    ->options(Division::pluck('title', 'id'))
                                    // ->relationship(name: 'division', titleAttribute: 'title')
                                    // ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title} - {$record->abbreviation}")
                                    ->searchable()
                                    ->label('Choose Budget Division')
                                    ->preload()
                                    ->native(false)
                                    ->columnSpanFull()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                Repeater::make('project_division_categories')

                                    ->relationship()

                                    ->label('Division Category')
                                    ->columns([
                                        'sm' => 3,
                                        'xl' => 6,
                                        '2xl' => 9,
                                    ])
                                    ->schema([


                                        Select::make('from')
                                        ->label('Costing Type')
                                            ->options([

                                                'Direct Cost' => 'Direct Cost',
                                                'Indirect Cost' => 'Indirect Cost',
                                            ])
                                            ->live()
                                            ->afterStateUpdated(function (Get $get, Set $set) {

                                                $set('project_division_sub_category_expenses', []);
                                            })
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

                                            ->label('Budget Division Expenses')
                                            ->columns([
                                                'sm' => 3,
                                                'xl' => 6,
                                                '2xl' => 9,
                                            ])
                                            ->schema([


                                                Select::make('fourth_layer_id')
                                                ->live()
                                                ->afterStateUpdated(function (Get $get, Set $set) {

                                                    // $data = FourthLayer::whereHas('project_division_sub_category_expense.project_division_category', function($query)use($get){
                                                    //     $query->where('from', $get('../../form'));
                                                    // })->pluck('title', 'id');
                                                    // $model = FourthLayer::with('project_division_sub_category_expense.project_division_category')->first(); // You need to retrieve a specific model instance
                                                    // $relationshipData = $model->project_division_sub_category_expense;

                                                    // dd($data, $relationshipData);


                                                    // self::updateLeftAllocated($get, $set);
                                                    // dd($this->getRecord()->id);
                                                })
                                                ->options(function (Get $get, Set $set) {
                                                    return FourthLayer::whereHas('project_division_sub_category_expense.project_division_category', function ($query) use ($get) {
                                                        $query->where('from', $get('../../from'))->whereHas('project_devision', function($query){
                                                            $query->where('project_id',$this->getRecord()->id);
                                                        });
                                                    })->pluck('title', 'id');
                                                })

                                                // ->options(function (Get $get, Set $set, Model $model) {
                                                //     return FourthLayer::whereHas('project_division_sub_category_expense', function ($query) use ($get, $set, $model) {
                                                //         $query->where('from', $get('..../from')) // Adjust the path based on your actual field structure
                                                //             ->whereHas('project_devision', function ($query) use ($get, $set, $model) {
                                                //                 $query->where('project_id', $model->id);
                                                //             });
                                                //     })->pluck('title', 'id');
                                                // })

                                                // ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title} - {$record->abbreviation}")
                                                ->searchable()
                                                ->label('Choose Budget Division')
                                                ->preload()
                                                ->native(false)
                                                ->columnSpanFull()
                                                ->distinct()
                                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                                // Select::make('parent_title')
                                                // ->options([

                                                //     'SKSU' => 'SKSU',
                                                //     'PCAARRD' => 'PCAARRD',
                                                // ])
                                                // ->label('Source')
                                                // ->columnSpanFull()

                                                //     ->hidden(fn (Get $get) => $get('../../from') === 'Indirect Cost' ? false : true),

                                                // TextInput::make('title')
                                                //     ->label('Title')
                                                //     ->required()
                                                //     ->live()
                                                //     ->maxLength(191)
                                                //     ->columnSpanFull(),


                                                // TableRepeater::make('fourth_layers')
                                                //     ->live()
                                                //     ->relationship()

                                                //     ->label('Expenses')
                                                //     ->columns([
                                                //         'sm' => 3,
                                                //         'xl' => 6,
                                                //         '2xl' => 9,
                                                //     ])
                                                //     ->schema([
                                                //         TextInput::make('title')
                                                //             ->label('Expenses Desciption')
                                                //             ->required()
                                                //             ->maxLength(191)
                                                //             ->columnSpanFull(),


                                                //     ])
                                                //     ->withoutheader()
                                                //     ->columnSpanFull(),
                                            ])

                                            ->columnSpanFull()
                                            ->visible(fn (Get $get) => !empty($get('from')) ? true : false)


                                    ])
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->visible(fn (Get $get) => !empty($get('division_id')) ? true : false),



                            ]) ->columnSpanFull(),

                            // Select::make('parent_title')
                            // ->options([

                            //     'SKSU' => 'SKSU',
                            //     'PCAARRD' => 'PCAARRD',
                            // ])
                            // ->label('Source')
                            // ->columnSpanFull(),



                        ]),

                    ])
                     ->columnSpanFull(),
                ]);
            }

}
