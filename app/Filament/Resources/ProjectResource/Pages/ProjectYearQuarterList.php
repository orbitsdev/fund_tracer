<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectQuarterResource;
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
use App\Filament\Resources\ProjectResource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\ProjectYearResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class ProjectYearQuarterList extends Page implements HasForms, HasTable
{

    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.project-year-quarter-list';


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

                 TextColumn::make('quarter.title')->sortable()->searchable(),
                // TextColumn::make('project_quarters_count')->counts('project_quarters')->label('Quarters Count'),



            ])
            ->filters([
                // ...
            ])->headerActions([


            ])

            ->actions([


              Action::make('Manage Quarters')->button()->label('Manage')->icon('heroicon-m-pencil-square')->url(fn (Model $record): string => ProjectQuarterResource::getUrl('edit', ['record' => $record])),

                DeleteAction::make(),


            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('project_year_id', $this->record->id));;
    }
}
