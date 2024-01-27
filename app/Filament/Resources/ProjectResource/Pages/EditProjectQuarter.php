<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Models\Year;
use Filament\Actions;
use App\Models\Quarter;
use Filament\Forms\Get;
use App\Models\Division;
use Filament\Forms\Form;
use Filament\Support\RawJs;
use Filament\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProjectResource;
use Filament\Forms\Components\CheckboxList;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;

class EditProjectQuarter extends EditRecord
{
    protected static string $resource = ProjectResource::class;


    protected static ?string $title = 'Buget Divisions';

    // protected function mutateFormDataBeforeFill(array $data): array
    // {
    //     // dd($this->getRecord()->project_divisions);
    // }

    protected function getHeaderActions(): array
    {
        return [


            // Action::make('year_quarter_budget')->label('Manage Expenses')->icon('heroicon-m-banknotes')->url(fn (Model $record): string => ProjectResource::getUrl('manage-quarter-year', ['record'=> $record])),
        ];
    }

    protected function getRedirectUrl(): string
    {
        // return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
        return $this->getResource()::getUrl('index');
    }



    public function form(Form $form): Form
    {
        return $form
            ->schema(
                [

                    Repeater::make('project_divisions')
                    ->relationship()
                    ->addActionLabel('Budget Division')

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

                            ->label('Category')
                            ->addActionLabel('Budget Category')
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
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->columnSpanFull()
                                    ->native(false)
                                    ->searchable(),
                             


                                Repeater::make('project_division_sub_category_expenses')
                                    ->live()
                                    ->relationship()
                                    ->addActionLabel('Expenses Category')
                                    ->label('Budget Division Expenses')
                                    ->columns([
                                        'sm' => 3,
                                        'xl' => 6,
                                        '2xl' => 9,
                                    ])
                                    ->schema([

                                        Select::make('parent_title')
                                        ->options([

                                            'SKSU' => 'SKSU',
                                        'PCAARRD' => 'PCAARRD',
                                        ])
                                        ->label('Source')
                                        ->distinct()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->columnSpanFull()

                                            ->hidden(fn (Get $get) => $get('../../from') === 'Indirect Cost' ? false : true),

                                        TextInput::make('title')
                                            ->label('Title')
                                            ->required()
                                            ->live()
                                            ->maxLength(191)
                                            ->columnSpanFull(),


                                        TableRepeater::make('fourth_layers')
                                            ->live()
                                            ->relationship()
                                            ->addActionLabel('Expenses')
                                            ->label('Expenses')
                                            ->columns([
                                                'sm' => 3,
                                                'xl' => 6,
                                                '2xl' => 9,
                                            ])
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Expenses Desciption')
                                                    ->required()
                                                    ->maxLength(191)
                                                    ->columnSpanFull(),


                                            ])
                                            ->addActionLabel('Expenses')
                                            ->withoutheader()
                                            ->columnSpanFull(),
                                    ])

                                    ->columnSpanFull()
                                    ->visible(fn (Get $get) => !empty($get('from')) ? true : false)


                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => !empty($get('division_id')) ? true : false),


                    ])
                  
                    
                    ->columnSpanFull(),




                ]
            );
    }


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
