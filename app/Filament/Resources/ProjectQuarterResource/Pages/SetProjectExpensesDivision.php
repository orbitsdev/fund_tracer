<?php

namespace App\Filament\Resources\ProjectQuarterResource\Pages;

use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Illuminate\View\View;

use Illuminate\Database\Query\Builder;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\ProjectQuarterResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
class SetProjectExpensesDivision extends EditRecord
{
    protected static string $resource = ProjectQuarterResource::class;

    protected function getRedirectUrl(): string
    {
        // dd($this->getRecord());
        // return ProjectResource::getUrl('quarter-list', ['record' => $this->getRecord()]);
        return ProjectResource::getUrl('view', ['record' => $this->getRecord()->project_year->project_id]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
{
    //   dd($this->getRecord()->project_year->project_id);

    return $data;
}

    public function getHeader(): ?View
    {
        return view('filament.settings.custom-header', ['title' => 'Set Expenses Division', 'first' => 'Expenses Division', 'second' => 'Set Expenses Division']);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(
                [


                    Repeater::make('quart_divide')

                    ->relationship('quarter_expense_budget_divisions')
                    ->label('Budget Divisions')
                    ->addActionLabel('Budget Division')
                    ->schema([
                        Select::make('project_devision_id')
    ->relationship(name: 'project_division', titleAttribute: 'project_devision_id')
                        // Select::make('project_devision_id')

                        // ->required()
                        // ->live()
                        // ->relationship(
                        //     name: 'project_division',
                        //     titleAttribute: 'title',
                        //     modifyQueryUsing: fn (Builder $query, Get $get, Set $set) => $query->where('project_id', $this->getRecord()->project_year->project_id)
                        // )
                        // ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->division->title}")
                        // ->searchable()
                        // ->label('Division')
                        // ->preload()
                        // ->native(false)
                        // ->columnSpanFull()
                        // ->distinct()
                        // ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                     ]),
                ]
            );
    }
}
