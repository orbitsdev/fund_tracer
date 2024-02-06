<?php

namespace App\Filament\Resources\ProjectResource\Pages;

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
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProjectResource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Resources\ProjectQuarterResource;

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

                TextColumn::make('year.title')->sortable()->color('info'),
                TextColumn::make('project_quarters_count')->counts('project_quarters')->label('Current Quarters'),



            ])
            ->filters([
                // ...
            ])->headerActions([
                Action::make('Back')->label('Back')->icon('heroicon-m-arrow-uturn-left')->outlined()->color('gray')->url(fn (): string => ProjectResource::getUrl('index')),
                Action::make('View')->label('View Project Details')->icon('heroicon-m-eye')->url(fn (): string => ProjectResource::getUrl('view', ['record' => $this->record->id])),

                CreateAction::make()->label('Create Year')->form([
                    Select::make('year_id')
                        ->live()
                        // ->options(Year::pluck('title', 'id'))
                        ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                            return $rule->where('year_id', $get('year_id'))->where('project_id', $this->record->id);
                        })
                        ->required()
                        ->relationship(
                            name: 'year', titleAttribute: 'title',
                            modifyQueryUsing: fn (Builder $query) => $query->whereDoesntHave('project_years', function($query){
                                $query->where('project_id', $this->record->id);
                            }),
                            )
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                        ->searchable()
                        ->label('Year')
                        ->preload()
                        ->native(false)
                        ->columnSpanFull()
                        ->createOptionForm([
                            TextInput::make('title')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ,

                        ])
                        ->hint('Click plus icon to add more options')
                        ,


                        ],
                        )
                ->modalHeading('Create Year For Quarter')
                    ->using(function (array $data, string $model): Model {
                        $data['project_id'] = $this->record->id;

                        return $model::create($data);
                    })
                    ->disableCreateAnother(),

                ],)

            ->actions([
                    Action::make('Manage Quarters')->button()->label('Manage Quarter')->icon('heroicon-m-pencil-square')->url(fn (Model $record): string => ProjectResource::getUrl('quarter-list', ['record' => $record]))->hidden(function(Model $record){
                        if($record->project_quarters->count()>0){
                            return false;
                        }else{
                            return true;
                        }
                    }),
                    Action::make('Create Quarter')->button()->outlined()->label('Create Quarters')->icon('heroicon-m-sparkles')
                    ->url(fn (Model $record): string => ProjectResource::getUrl('create-quarter', ['record' => $record])),

                    ActionGroup::make([

                     DeleteAction::make(),

                ]),


            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('project_id', $this->record->id));;
    }
}
