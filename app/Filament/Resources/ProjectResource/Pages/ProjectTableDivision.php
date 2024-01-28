<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Closure;
use App\Models\Project;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Table;
use App\Models\ProjectDevision;
use App\Models\DivisionCategory;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use App\Models\ProjectDivisionCategory;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
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
use App\Filament\Resources\ProjectResource\Pages\ProjectTableDivisionCategory;

class ProjectTableDivision extends Page implements HasForms, HasTable
{

    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = ProjectResource::class;
    protected static string $view = 'filament.resources.project-resource.pages.project-table-division';

    protected static ?string $title = 'Budget Division';

    public $record = null;


    public function mount($record): void
    {
        static::authorizeResourceAccess();
        $this->record = Project::find($record);
    }


    public function table(Table $table): Table
    {
        return $table
            ->query(ProjectDevision::query())
            ->columns([

                TextColumn::make('division.title')->sortable()->color('Division'),

                // TextColumn::make('project_division_categories_count')->counts('project_division_categories')->label('Current'),



            ])
            ->filters([
                // ...
            ])->headerActions([
                Action::make('Back')->label('Back')->icon('heroicon-m-arrow-uturn-left')->outlined()->color('gray')->url(fn (): string => ProjectResource::getUrl('index')),

                CreateAction::make()->label('Create Budget Division')

                    ->form([

                        Select::make('division_id')
                            ->live()
                            // ->options(Year::pluck('title', 'id'))
                            ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                                return $rule->where('division_id', $get('division_id'))->where('project_id', $this->record->id);
                            })
                            ->required()
                            ->relationship(
                                name: 'division',
                                titleAttribute: 'title',
                                modifyQueryUsing: fn (Builder $query) => $query->whereDoesntHave('project_divisions', function ($query) {
                                    $query->where('project_id', $this->record->id);
                                }),
                            )
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                            ->searchable()
                            ->label('Division')
                            ->preload()
                            ->native(false)
                            ->columnSpanFull()
                            ->distinct()
                            ->createOptionForm([
                                TextInput::make('title')
                                    ->required()
                                    ->unique(ignoreRecord: true),

                            ])
                            ->hint('Click plus icon to add more options'),


                    ],)

                    ->mutateFormDataUsing(function (array $data): array {
                        $data['project_id'] = $this->record->id;
                        // dd($data);

                        return $data;
                    })
                    ->modalHeading('Create Year For Quarter')
                    ->using(function (array $data, string $model): Model {
                        $data['project_id'] = $this->record->id;

                        return $model::create($data);
                    })
                    ->disableCreateAnother(),

            ],)

            ->actions([
                Action::make('Create Category')->button()->outlined()->label('Create Category')->icon('heroicon-m-sparkles')
                ->url(fn (Model $record): string => ProjectResource::getUrl('create-project-table-division-category', ['record' => $record])),
                // Action::make('sendEmail')
                //     ->form([
                //         Select::make('from')
                //             ->label('Costing Type')
                //             ->options([

                //                 'Direct Cost' => 'Direct Cost',
                //                 'Indirect Cost' => 'Indirect Cost',
                //             ])
                //             ->rules([
                //                 function (Model $record) {
                //                     return function (string $attribute, $value, Closure $fail,  $record, Get $get) {
                //                         $exist = $record->whereHas('project_division_categories', function($query) use($get, $record){
                //                             $query->where('from', $get('from'))->where('project_devision_id', $record->id);
                //                         });

                                    
                //                         if ($value === 'foo') {
                //                             $fail('The :attribute is invalid.');
                //                         }
                //                     };
                //                 },
                //             ])
                //             ->afterStateUpdated(function (Get $get, Set $set, Model $record) {
                //             })
                //     ])
                //     ->action(function (array $data, Model $record) {
                //         $data['project_devision_id'] = $record->id;
                //         dd($data);
                //     }),

                // Action::make('Manage Quarters')->button()->label('Manage Quarter')->icon('heroicon-m-pencil-square')->url(fn (Model $record): string => ProjectResource::getUrl('quarter-list', ['record' => $record]))->hidden(function(Model $record){
                //     if($record->project_quarters->count()>0){
                //         return false;
                //     }else{
                //         return true;
                //     }
                // }),

                // Action::make('create_division_category')->button()->outlined()->label('Create Division Category')->icon('heroicon-m-sparkles')
                // ->url(fn (Model $record): string => ProjectResource::getUrl('create-division-category', ['record' => $record])),
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
