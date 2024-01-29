<?php

namespace App\Filament\Resources\ProjectDivisionCategoryResource\Pages;

use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Support\RawJs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action as RAction;
use App\Filament\Resources\ProjectDivisionCategoryResource;
use App\Filament\Resources\ProjectResource;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;

class EditDivisionCategoryExpenses extends EditRecord
{
    protected static string $resource = ProjectDivisionCategoryResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {

        //  dd($this->getRecord());
        $data['category'] = $this->getRecord()->from;

        return $data;
    }


    protected function getRedirectUrl(): string
    {
        // dd(ProjectResource::getUrl('project-table-division', ['record' => $this->getRecord()->project_devision_id]));
        return ProjectResource::getUrl('project-table-division-category', ['record' => $this->getRecord()->project_devision_id]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['category']);

        return $data;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(
                [

                    TextInput::make('category')
                        ->label('Selected Category')
                        ->disabled()
                        ->columnSpanFull(),
                    TableRepeater::make('sub_category_expenses')
                        ->emptyLabel('No Category ')
                        ->live()
                        ->relationship('project_division_sub_category_expenses')
                        ->addActionLabel('Add Group')
                        ->label('Division Expenses')
                        // ->columns([
                        //     'sm' => 3,
                        //     'xl' => 6,
                        //     '2xl' => 9,
                        // ])
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
                                ->hidden(function (Get $get) {
                                    if ($this->getRecord()->from  === 'Indirect Cost') {
                                        return false;
                                    } else {
                                        return true;
                                    }
                                }),

                            TextInput::make('title')
                                ->label('Title')
                                ->required()
                                ->live()
                                ->maxLength(191)
                                ->columnSpanFull(),


                            TableRepeater::make('fourth_layers')
                                ->withoutHeader()
                                ->emptyLabel('No Expenses Declared ')
                                ->live()
                                ->relationship()
                                ->addActionLabel('Add expenses')
                                ->label('Expenses')

                                ->schema([
                                    TextInput::make('title')
                                        ->label('Expenses Desciption')
                                        ->required()
                                        ->maxLength(191)
                                        ->columnSpanFull(),


                                ])
                                ->addActionLabel('Expenses')
                            // ->withoutheader()
                            // ->columnSpanFull(),
                        ])

                        ->columnSpanFull()
                        ->visible(fn (Get $get) => !empty($this->getRecord()->from) ? true : false)

                ]
            );
    }
}
