<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Models\Year;
use Filament\Actions;
use App\Models\Quarter;
use Filament\Forms\Get;
use App\Models\Division;
use Filament\Forms\Form;
use Filament\Support\RawJs;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProjectResource;
use Filament\Forms\Components\CheckboxList;

class EditProjectQuarter extends EditRecord
{
    protected static string $resource = ProjectResource::class;








public function form(Form $form): Form
{
    return $form
        ->schema(
            [

                Section::make('Manage Quarters')
                ->icon('heroicon-m-banknotes')
                // ->description('Manage and organize particulars  ')
                ->columnSpanFull()
                ->schema([

                    Repeater::make('project_years')

                        ->relationship()

                        ->label('Year & Quarters')

                        ->extraAttributes([
                            'class' => 'border-white',

                        ])
                        ->schema([
                              Select::make('year_id')
                                ->live()
                                ->options(Year::pluck('title','id'))
                                // ->relationship(name: 'year', titleAttribute: 'title')
                                // ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
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

                    ->extraAttributes([
                        'class' => 'border-white',

                    ])
                    ->schema([
                        Select::make('quarter_id')
                        ->live()
                        ->options(Quarter::pluck('title','id'))
                        // ->relationship(name: 'quarter', titleAttribute: 'title')
                        // ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                        ->searchable()
                        ->label('Quarter')
                        ->preload()
                        ->native(false)
                        ->columnSpanFull()
                        ->distinct()
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems(),


                                 Repeater::make('project_divisions')

                                ->relationship()

                                ->label('Divisions')

                                ->schema([

                                      Select::make('division_id')
                                ->live()
                                ->options(Division::pluck('title','id'))
                                // ->relationship(name: 'division', titleAttribute: 'title')
                                // ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title} - {$record->abbreviation}")
                                ->searchable()
                                ->label('Choose Division')
                                ->preload()
                                ->native(false)
                                ->columnSpanFull()
                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                            Repeater::make('project_division_categories')

                                ->relationship()

                                ->label('Division Categories')
                                ->columns([
                                    'sm' => 3,
                                    'xl' => 6,
                                    '2xl' => 9,
                                ])
                                ->schema([


                                    Select::make('from')
                                        ->options([

                                            'Direct Cost' => 'Direct Cost',
                                            'Indirect Cost' => 'Indirect Cost',
                                        ])
                                        ->distinct()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->columnSpanFull()
                                        ->native(false)
                                        ->searchable(),


                                    Repeater::make('project_division_sub_category_expenses')
                                        ->live()
                                        ->relationship()

                                        ->label('Division Sub Categories')
                                        ->columns([
                                            'sm' => 3,
                                            'xl' => 6,
                                            '2xl' => 9,
                                        ])
                                        ->schema([
                                            TextInput::make('parent_title')
                                                        ->label('Parent Title')

                                                        ->live()
                                                        ->maxLength(191)
                                                        ->columnSpanFull()

                                                        ->hidden(fn (Get $get) => $get('../../from') === 'Indirect Cost' ? false : true),

                                                    TextInput::make('title')
                                                        ->label('Title')
                                                        ->required()
                                                        ->live()
                                                        ->maxLength(191)
                                                        ->columnSpanFull(),
                                                  Repeater::make('fourth_layers')
                                        ->live()
                                        ->relationship()

                                        ->label('Division Sub Categories')
                                        ->columns([
                                            'sm' => 3,
                                            'xl' => 6,
                                            '2xl' => 9,
                                        ])
                                        ->schema([
                                            TextInput::make('title')
                                                                ->label('Fourth  Title')
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
                                        ])

                                        ])->columnSpanFull(),
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



                                    // Repeater::make('project_division_sub_category_expenses')
                                    //     ->live()
                                    //     ->relationship()

                                    //     ->label('Division Sub Categories')
                                    //     ->columns([
                                    //         'sm' => 3,
                                    //         'xl' => 6,
                                    //         '2xl' => 9,
                                    //     ])
                                    //     ->schema([

                                    //         TextInput::make('parent_title')
                                    //             ->label('Parent Title')

                                    //             ->live()
                                    //             ->maxLength(191)
                                    //             ->columnSpanFull()

                                    //             ->hidden(fn (Get $get) => $get('../../from') === 'Indirect Cost' ? false : true),

                                    //         TextInput::make('title')
                                    //             ->label('Title')
                                    //             ->required()
                                    //             ->live()
                                    //             ->maxLength(191)
                                    //             ->columnSpanFull(),


                                    //         Repeater::make('fourth_layers')
                                    //             ->live()
                                    //             ->relationship()

                                    //             ->label('Forth Layers')
                                    //             ->columns([
                                    //                 'sm' => 3,
                                    //                 'xl' => 6,
                                    //                 '2xl' => 9,
                                    //             ])
                                    //             ->schema([
                                    //                 TextInput::make('title')
                                    //                     ->label('Fourth  Title')
                                    //                     ->required()
                                    //                     ->maxLength(191)
                                    //                     ->columnSpanFull(),

                                    //                     TextInput::make('amount')

                                    //                     ->mask(RawJs::make('$money($input)'))
                                    //                     ->stripCharacters(',')
                                    //                     ->numeric()
                                    //                         // ->mask(RawJs::make('$money($input)'))
                                    //                         // ->stripCharacters(',')
                                    //                         ->prefix('₱')
                                    //                         ->numeric()
                                    //                         // ->maxValue(9999999999)
                                    //                         ->default(0)
                                    //                         ->columnSpanFull()
                                    //                         ->required(),
                                    //             ])->columnSpanFull(),
                                    //     ])

                                    //     ->columnSpanFull()
                                    //     ->visible(fn (Get $get) => !empty($get('from')) ? true : false)


                                ])
                                ->columns(2)
                                ->columnSpanFull()
                                ->visible(fn (Get $get) => !empty($get('division_id')) ? true : false),



                                ]),



                    ]),



                        ]),

                ])

                ->collapsed()
                ->collapsible()
                ->hidden(function (string $operation) {
                    if ($operation === 'create') {
                        return true;
                    } else {
                        return false;
                    }
                }),




            ]);}


// public function form(Form $form): Form
// {
//     return $form
//         ->schema([
//             Repeater::make('project_years')
//             ->relationship()
//             ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
//                 dd('test');
//                 return $data;
//             })
//             ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
//                 dd('test');
//                 return $data;
//             })
//             ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {

//                 return $data;
//             })
//             ->schema([
//                 Select::make('year_id')
//                 ->live()
//                 ->relationship(name: 'year', titleAttribute: 'title')
//                 ->native(false)
//                 ->searchable()
//                 ->preload()
//                 ->distinct()
//                 ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

//                 Repeater::make('project_quarters')
//                 ->relationship()
//                 ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {

//                     return $data;
//                 })
//                 ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {

//                     return $data;
//                 })
//                 ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {

//                     return $data;
//                 })
//                 ->schema([

//                     Select::make('quarter_id')
//                     ->live()
//                     ->relationship(name: 'quarter', titleAttribute: 'title')
//                     ->native(false)
//                     ->searchable()
//                     ->preload()
//                     ->distinct()
//                     ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

//                     Repeater::make('project_division_sub_category_expenses')
//                     ->relationship()
//                     ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {

//                         return $data;
//                     })
//                     ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {

//                         return $data;
//                     })
//                     ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {

//                         return $data;
//                     })
//                     ->schema([
//                         CheckboxList::make('project_division_sub_category_expense_id')
//                         ->relationship(titleAttribute: 'title')
//                         // TextInput::make('title')
//                         // ->label('Title')
//                         // ->required()
//                         // ->live()
//                         // ->maxLength(191)
//                         // ->columnSpanFull(),


//                     // Repeater::make('fourth_layers')
//                     //     ->live()
//                     //     ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {

//                     //         return $data;
//                     //     })
//                     //     ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {

//                     //         return $data;
//                     //     })
//                     //     ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {

//                     //         return $data;
//                     //     })
//                     //     ->relationship()

//                     //     ->label('Forth Layers')
//                     //     ->columns([
//                     //         'sm' => 3,
//                     //         'xl' => 6,
//                     //         '2xl' => 9,
//                     //     ])
//                     //     ->schema([


//                     //         // TextInput::make('title')
//                     //         //     ->label('Fourth  Title')
//                     //         //     ->required()
//                     //         //     ->maxLength(191)
//                     //         //     ->columnSpanFull(),
//                     //     ])->columnSpanFull(),
//                     ]),



//                 ])->visible(fn (Get $get) => !empty($get('year_id')) ? true : false),
//             ])->columnSpanFull()
//         ]);
// }
}
