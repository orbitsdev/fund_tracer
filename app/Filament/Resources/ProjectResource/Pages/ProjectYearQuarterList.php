<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Models\Project;
use Filament\Tables\Table;
use App\Models\ProjectYear;

use App\Models\ProjectQuarter;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Forms\Contracts\HasForms;

use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\ProjectResource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\ProjectYearResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Resources\ProjectQuarterResource;
use Filament\Tables\Actions\HeaderActionsPosition;
use Illuminate\Contracts\View\View;

class ProjectYearQuarterList extends Page implements HasForms, HasTable
{

    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.project-year-quarter-list';

    public function getHeader(): ?View
    {
        return view('filament.settings.custom-header', ['title' => 'Project Year Quarter List', 'first' => 'Projects', 'second' => 'Project Year Quarter List']);
    }
    public $record = null;

    public function mount($record): void
    {
        static::authorizeResourceAccess();
        $this->record = ProjectYear::find($record);
        // dd($this->record);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ProjectQuarter::query())
            ->columns([

                TextColumn::make('quarter.title')->sortable()->searchable()->color('info'),
                TextColumn::make('quarter_expense_budget_divisions')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->formatStateUsing(fn ($state) => $state->project_division->division->title)

                //  TextColumn::make('')->sortable()->searchable()->color('info'),
                // TextColumn::make('project_quarters_count')->counts('project_quarters')->label('Quarters Count'),



            ])
            ->filters([
                // ...
            ])->headerActions([

                Action::make('Back')->label('Back')->icon('heroicon-m-arrow-uturn-left')->outlined()->color('gray')->url(fn (): string => ProjectResource::getUrl('manage-quarter-year', ['record' => $this->record->project_id])),

            ], position: HeaderActionsPosition::Bottom)

            ->actions([


                Action::make('Manage Quarters')->button()->label('Manage')->icon('heroicon-m-pencil-square')->url(fn (Model $record): string => ProjectQuarterResource::getUrl('quarter-expenses-division-list', ['record' => $record]))
                ->hidden(function(Model $record){
                    if($record->quarter_expense_budget_divisions->count()>0){
                        return false;
                    }else{
                        return true;
                    }
                }) 
                ,
                Action::make('Set Expenses Division')->button()->outlined()->label('Set Expenses Division')->icon('heroicon-m-sparkles')->url(fn (Model $record): string => ProjectQuarterResource::getUrl('set-project-expenses-division', ['record' => $record])),

                DeleteAction::make()->button(),


            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('project_year_id', $this->record->id));;
    }
}
