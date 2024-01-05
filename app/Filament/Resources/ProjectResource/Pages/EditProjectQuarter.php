<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProjectResource;

class EditProjectQuarter extends EditRecord
{
    protected static string $resource = ProjectResource::class;



public function form(Form $form): Form
{
    return $form
        ->schema([
            Repeater::make('project_years')
            ->relationship()
            ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {

                return $data;
            })
            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {

                return $data;
            })
            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {

                return $data;
            })
            ->schema([
                Select::make('year_id')
                ->live()
                ->relationship(name: 'year', titleAttribute: 'title')
                ->native(false)
                ->searchable()
                ->preload()
                ->distinct()
                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                Repeater::make('project_quarters')
                ->relationship()
                ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {

                    return $data;
                })
                ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {

                    return $data;
                })
                ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {

                    return $data;
                })
                ->schema([

                    Select::make('quarter_id')
                    ->live()
                    ->relationship(name: 'quarter', titleAttribute: 'title')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                    Repeater::make('project_division_sub_category_expenses')
                    ->relationship()
                    ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {

                        return $data;
                    })
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {

                        return $data;
                    })
                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {

                        return $data;
                    })
                    ->schema([

                        TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->live()
                        ->maxLength(191)
                        ->columnSpanFull(),


                    Repeater::make('fourth_layers')
                        ->live()
                        ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {

                            return $data;
                        })
                        ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {

                            return $data;
                        })
                        ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {

                            return $data;
                        })
                        ->relationship()

                        ->label('Forth Layers')
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
                        ])->columnSpanFull(),
                    ]),



                ])->visible(fn (Get $get) => !empty($get('year_id')) ? true : false),
            ])->columnSpanFull()
        ]);
}
}
