<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Tables\Table;
use App\Models\ProjectDevision;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use App\Models\ProjectDivisionCategory;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProjectResource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Actions\HeaderActionsPosition;
use App\Filament\Resources\ProjectDivisionCategoryResource;

class ProjectTableDivisionCategory extends Page implements HasForms, HasTable
{
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.project-table-division-category';

    public function getHeader(): ?View
    {
        return view('filament.settings.custom-header',['title'=> 'Division Category List', 'first'=> 'Projects' ,'second'=> 'Division Category List']);
    }

    use InteractsWithTable;
    use InteractsWithForms;

    public $record = null;
    public function mount($record): void
    {
        static::authorizeResourceAccess();
        $this->record = ProjectDevision::find($record);
        //  dd($this->record);
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(ProjectDivisionCategory::query())
            ->columns([

                   TextColumn::make('from')->sortable()->searchable()->color('info'),
                // TextColumn::make('project_quarters_count')->counts('project_quarters')->label('Quarters Count'),



            ])
            ->filters([
                // ...
            ])->headerActions([

                Action::make('Back')->label('Back')->icon('heroicon-m-arrow-uturn-left')->outlined()->color('gray')
                ->url(fn (): string => ProjectResource::getUrl('project-table-division', ['record' => $this->record->division_id]))

            ], position: HeaderActionsPosition::Bottom)

            ->actions([


                Action::make('Manage Expenses')->button()->label('Declare Expenses')->icon('heroicon-m-pencil-square')
                ->url(fn (Model $record): string => ProjectDivisionCategoryResource::getUrl('edit-category-expenses', ['record' => $record])),

                DeleteAction::make()->button(),


            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                ]),
            ])
        //     ->modifyQueryUsing(fn (Builder $query) => $query->where('project_devision_id', $this->record->id
        //     )
        // )
        ;
    }

}
