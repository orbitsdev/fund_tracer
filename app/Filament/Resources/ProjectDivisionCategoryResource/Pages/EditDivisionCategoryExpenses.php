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
use Awcodes\FilamentTableRepeater\Components\TableRepeater;

class EditDivisionCategoryExpenses extends EditRecord
{
    protected static string $resource = ProjectDivisionCategoryResource::class;

     protected function mutateFormDataBeforeFill(array $data): array
    {

        // dd($this->getRecord());
        $data['category'] = $this->getRecord()->from;

        return $data;
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
                    ->disabled()
                    ->columnSpanFull(),
                    TableRepeater::make('sub_category_expenses')
                    ->emptyLabel('No Expenses Was Declare')

                                    ->live()
                                    ->relationship('project_division_sub_category_expenses')
                                    ->addActionLabel('Expenses Category')
                                    ->label('Budget Division Expenses')
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
                                        ->hidden(function(Get $get){
                                            if($this->getRecord()->from  === 'Indirect Cost'){
                                                return false;
                                            }else{
                                                return true;
                                            }
                                        }),

                                        TextInput::make('title')
                                            ->label('Title')
                                            ->required()
                                            ->live()
                                            ->maxLength(191)
                                            ->columnSpanFull(),


                                        Repeater::make('fourth_layers')
                                            ->live()
                                            ->relationship()
                                            ->addActionLabel('Expenses')
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

                ]);
    }

}
