<?php

namespace App\Filament\Resources\ProjectQuarterResource\Pages;

use Illuminate\View\View;
use Filament\Tables\Table;
use App\Models\ProjectQuarter;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProjectResource;
use App\Models\QuarterExpenseBudgetDivision;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Resources\ProjectQuarterResource;
use Filament\Tables\Actions\HeaderActionsPosition;
use App\Filament\Resources\QuarterExpenseBudgetDivisionResource;

class QuarterDivisionExpenses extends Page implements HasForms, HasTable
{

    use InteractsWithTable;
    use InteractsWithForms;
    protected static string $resource = ProjectQuarterResource::class;

    protected static string $view = 'filament.resources.project-quarter-resource.pages.quarter-division-expenses';


    public function getHeader(): ?View
    {
        return view('filament.settings.custom-header',['title'=> 'Quarter Expenses Division', 'first'=> 'Quarter Divsaion' ,'second'=> 'Quarter Expenses Division List']);
    }
    public $record = null;

    public function mount($record): void
    {
        static::authorizeResourceAccess();
        $this->record = ProjectQuarter::find($record);
        // dd(ProjectResource::geturl('quarter-list',['record'=> $this->record->project_year->project_id]));
    //    dd($this->record->project_year->project_id);
    }


    public function table(Table $table): Table
    {
        return $table
            ->query(QuarterExpenseBudgetDivision::query())
            ->columns([

                  TextColumn::make('project_division.division.title')->sortable()->searchable()->color('info')->searchable(),
                // TextColumn::make('project_quarters_count')->counts('project_quarters')->label('Quarters Count'),



            ])
            ->filters([
                // ...
            ])->headerActions([

                Action::make('Back')->label('Back')->icon('heroicon-m-arrow-uturn-left')->outlined()->color('gray')->url(fn (): string => ProjectResource::geturl('quarter-list',['record'=> $this->record->project_year->project_id])),
                // CreateAction::make()->label('Create Budget Division')

                //     ->form([

                //         Select::make('division_id')
                //             ->live()
                //             // ->options(Year::pluck('title', 'id'))
                //             ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                //                 return $rule->where('division_id', $get('division_id'))->where('project_id', $this->record->id);
                //             })
                //             ->required()
                //             ->relationship(
                //                 name: 'division',
                //                 titleAttribute: 'title',
                //                 modifyQueryUsing: fn (Builder $query) => $query->whereDoesntHave('project_divisions', function ($query) {
                //                     $query->where('project_id', $this->record->id);
                //                 }),
                //             )
                //             ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                //             ->searchable()
                //             ->label('Division')
                //             ->preload()
                //             ->native(false)
                //             ->columnSpanFull()
                //             ->distinct()
                //             ->createOptionForm([
                //                 TextInput::make('title')
                //                     ->required()
                //                     ->unique(ignoreRecord: true),

                //             ])
                //             ->hint('Click plus icon to add more options'),


                //     ],)

                //     ->mutateFormDataUsing(function (array $data): array {
                //         $data['project_id'] = $this->record->id;
                //         // dd($data);

                //         return $data;
                //     })
                //     ->modalHeading('Create Year For Quarter')
                //     ->using(function (array $data, string $model): Model {
                //         $data['project_id'] = $this->record->id;

                //         return $model::create($data);
                //     })
                //     ->disableCreateAnother(),

            ], position: HeaderActionsPosition::Bottom)

            ->actions([


              Action::make('Manage Quarters')->button()->label('Manage Expenses')->icon('heroicon-m-pencil-square')->url(fn (Model $record): string => QuarterExpenseBudgetDivisionResource::getUrl('edit-division-expenses', ['record' => $record])),

                DeleteAction::make()->button(),


            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                ]),
            ])
            // ->modifyQueryUsing(fn (Builder $query) => $query->where('project_quarter_id', $this->record->id))

            ;
    }
}
