<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use Filament\Forms\Get;
use App\Models\Division;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProjectResource;

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
                       
                        Repeater::make('quarter_expense')

                        ->relationship()
    
                        ->label('Quarter Expenses')
    
                        ->schema([]),

                    ])
                     ->columnSpanFull(),
                ]);
            }

}
