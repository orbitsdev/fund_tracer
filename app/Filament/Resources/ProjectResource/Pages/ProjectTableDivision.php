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
use Filament\Forms\Components\Toggle;
use Filament\Forms\Contracts\HasForms;
use App\Models\ProjectDivisionCategory;
use Filament\Forms\Components\Checkbox;
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

    protected static ?string $title = 'Particulars';

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

                TextColumn::make('project_division_categories.from')
                ->label('Has')
                    ->listWithLineBreaks()
                    ->bulleted()

                // TextColumn::make('project_division_categories_count')->counts('project_division_categories')->label('Current'),



            ])
            ->filters([
                // ...
            ])->headerActions([
                Action::make('Back')->label('Back')->icon('heroicon-m-arrow-uturn-left')->outlined()->color('gray')->url(fn (): string => ProjectResource::getUrl('index')),
                Action::make('view project')->label('View Project Details')->icon('heroicon-m-eye')->url(fn (): string => ProjectResource::getUrl('view', ['record'=> $this->record])),

                CreateAction::make()->label('Create Particular')

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

                        Checkbox::make('is_included')->label('Include DC and IC on particular creation?')->default(true)



                    ],)

                    ->mutateFormDataUsing(function (array $data): array {
                        $data['project_id'] = $this->record->id;


                        //  dd($data);

                        return $data;
                    })

                    ->modalHeading('Create Year For Quarter')
                    ->using(function (array $data, string $model): Model {



                        // $data['project_id'] = $this->record->id;


                        // $original_data  = $data;

                        // unset($data['is_included']);
                        // $new_record = $model::create($data);

                        // // Assuming $division_category_id is the ID of the existing division category you want to associate

                        // if($original_data['is_included']){
                        //     $pivot_data = [
                        //         [
                        //             'division_category_id' => $new_record->id,
                        //             'from' => 'Direct Cost',
                        //         ],
                        //         [
                        //             'division_category_id' => $new_record->id,
                        //             'from' => 'Indirect Cost',
                        //         ],
                        //     ];

                        //     $new_record->project_division_categories()->createMany($pivot_data);
                        // }



                        // return $new_record;

                        $data['project_id'] = $this->record->id;

                        $original_data = $data;

                        unset($data['is_included']);
                        $new_record = $model::create($data);

                        if ($original_data['is_included']) {
                            $pivot_data = [
                                ['from' => 'Indirect Cost'],
                                ['from' => 'Direct Cost'],
                            ];

                            $new_record->project_division_categories()->createMany($pivot_data);
                        }

                        return $new_record;
                    })
                    ->disableCreateAnother(),

            ],)

            ->actions([

                Action::make('Manage Category')->button()->label('Manage')->icon('heroicon-m-pencil-square')->url(fn (Model $record): string => ProjectResource::getUrl('project-table-division-category', ['record' => $record]))->hidden(function (Model $record) {
                    if ($record->project_division_categories->count() > 0) {
                        return false;
                    } else {
                        return true;
                    }
                }),

                Action::make('Create Category')->button()->outlined()->label('Create Category')->icon('heroicon-m-sparkles')
                ->url(function (Model $record): string {
                    return ProjectResource::getUrl('create-project-table-division-category', ['record' => $record]);
                })
                ->hidden(function (Model $record) {
                    return $record->project_division_categories()
                    ->where('from', 'Direct Cost')
                    ->exists() && $record->project_division_categories()
                    ->where('from', 'Indirect Cost')
                    ->exists();
                })
            
                ,

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
