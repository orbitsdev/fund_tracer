<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectQuarterResource;
use App\Models\Year;
use App\Models\Project;
use Filament\Forms\Get;
use Filament\Tables\Table;
use App\Models\ProjectYear;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProjectResource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class ManageYearQuarter extends Page implements HasForms, HasTable
{

    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.manage-year-quarter';
    public $record = null;

    public function mount($record): void
    {
        static::authorizeResourceAccess();
        $this->record = Project::find($record);

    }


    public function table(Table $table): Table
    {
        return $table
            ->query(ProjectYear::query())
            ->columns([

                TextColumn::make('year.title'),
                TextColumn::make('project_quarters_count')->counts('project_quarters')->label('Quarters Count')



            ])
            ->filters([
                // ...
            ])->headerActions([
                CreateAction::make()->label('Quarter Year')->form([
                    Select::make('year_id')
                    ->live()
                    ->options(Year::pluck('title','id'))
                      ->unique(modifyRuleUsing: function (Unique $rule, Get $get) {
                     return $rule->where('year_id',$get('year_id'))->where('project_id', $this->record->id);
    })
                    // ->relationship(name: 'year', titleAttribute: 'title')
                    // ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                    ->searchable()
                    ->label('Year')
                    ->preload()
                    ->native(false)
                    ->columnSpanFull()


                ])
                ->using(function (array $data, string $model): Model {
                    $data['project_id'] = $this->record->id;

                    return $model::create($data);
                })
                ->disableCreateAnother()

            ])

            ->actions([
                Action::make('Manage Quarters')->icon('heroicon-m-pencil-square')->url(fn (Model $record): string => ProjectResource::getUrl('manage-quarter',['record'=> $record]))->button(),

                DeleteAction::make(),

            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('project_id', $this->record->id));

            ;
    }
}
