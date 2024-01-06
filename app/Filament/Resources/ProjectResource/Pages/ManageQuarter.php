<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Models\Quarter;
use Filament\Forms\Get;
use App\Models\Division;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ProjectYear;
use Filament\Support\RawJs;
use App\Models\ProjectQuarter;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action as FAction;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Contracts\HasActions;
use App\Filament\Resources\ProjectResource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Concerns\InteractsWithActions;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;

class ManageQuarter extends Page implements HasForms,  HasActions

{
    use InteractsWithForms;
    use InteractsWithActions;
    use InteractsWithForms;
    //  use InteractsWithTable;
    // use InteractsWithActions;
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.manage-quarter';


    public ?array $data = [];

    public ?ProjectQuarter $project_quarter = null;

    public $record = null;



    public function mount($record): void
    {

        $this->record = ProjectYear::find($record);




        // $this->record = auth()->user();
        $this->form->fill([
            'project_year_id' => $this->record->id
        ]);

        // dd($this->form);

    }



    public function createQuarter(): FAction
    {
        return FAction::make('save');
    }

    public function create()
    {

        $project_quarter = ProjectQuarter::create($this->form->getState());
        // dd($this->record);


        // Save the relationships from the form to the post after it is created.
        $this->form->model($project_quarter)->saveRelationships();
        Notification::make()
            ->title('Saved successfully')
            ->icon('heroicon-o-document-text')
            ->iconColor('success')
            ->send();

            $this->form->fill();
        // dd(ProjectResource::getUrl('manage-quarter-year', ['record'=>$this->record->project->id]));
        return redirect(ProjectResource::getUrl('manage-quarter-year', ['record' => $this->record->project->id]));
        // dd($this->form->fill($this->data));
        // dd($this->form->getState());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([


                Hidden::make('project_year_id'),

                Select::make('quarter_id')
                    ->required()
                    ->live()
                    // ->options(Quarter::pluck('title','id'))
                    ->relationship(name: 'quarter', titleAttribute: 'title')
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                    ->searchable()
                    ->label('Quarter')
                    ->preload()
                    ->native(false)
                    ->columnSpanFull()
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),


                 Repeater::make('project_divisions')

                    ->relationship()

                    ->addActionLabel('Add Category')
                    ->label('Expenses By Categories')
                    ->schema([

                        Select::make('division_id')
                            ->live()
                            // ->options(Division::pluck('title','id'))
                            ->relationship(name: 'division', titleAttribute: 'title')
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title} - {$record->abbreviation}")
                            ->searchable()
                            ->label('Choose Category')
                            ->preload()
                            ->native(false)
                            ->columnSpanFull()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                        Repeater::make('project_division_categories')

                            ->relationship()

                            ->columns([
                                'sm' => 3,
                                'xl' => 6,
                                '2xl' => 9,
                            ])
                            ->schema([


                                Select::make('from')
                                ->label('Cost Type')
                                    ->options([

                                        'Direct Cost' => 'Direct Cost',
                                        'Indirect Cost' => 'Indirect Cost',
                                    ])
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->columnSpanFull()
                                    ->native(false)
                                    ->searchable(),
                                // Select::make('division_category_id')
                                //     ->relationship(name: 'division_category', titleAttribute: 'title')
                                //     ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->title}")
                                //     ->searchable()
                                //     ->label('Choose Category')
                                //     ->preload()
                                //     ->native(false)
                                //     ->columnSpanFull()

                                //     ->distinct()
                                //     ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                //     ->live()
                                //     ->createOptionForm([
                                //         TextInput::make('title')
                                //             ->required(),
                                //     ])
                                //     ,



                                Repeater::make('project_division_sub_category_expenses')
                                    ->live()
                                    ->relationship()

                                    ->label('Expense Categories')
                                    ->columns([
                                        'sm' => 3,
                                        'xl' => 6,
                                        '2xl' => 9,
                                    ])
                                    ->schema([

                                        TextInput::make('parent_title')
                                            ->label('Source')

                                            ->live()
                                            ->maxLength(191)
                                            ->columnSpanFull()

                                            ->hidden(fn (Get $get) => $get('../../from') === 'Indirect Cost' ? false : true),

                                        TextInput::make('title')
                                            ->label('Expense Title')
                                            ->required()
                                            ->live()
                                            ->maxLength(191)
                                            ->columnSpanFull(),


                                        TableRepeater::make('fourth_layers')
                                            ->live()
                                            ->relationship()
                                            ->label('Expenses')
                                            ->columns([
                                                'sm' => 3,
                                                'xl' => 6,
                                                '2xl' => 9,
                                            ])
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Expenses Description')
                                                    ->required()
                                                    ->maxLength(191)
                                                    ->columnSpanFull(),

                                                TextInput::make('amount')

                                                    ->mask(RawJs::make('$money($input)'))
                                                    ->stripCharacters(',')
                                                    ->numeric()
                                                    // ->mask(RawJs::make('$money($input)'))
                                                    // ->stripCharacters(',')
                                                    ->prefix('₱')
                                                    ->numeric()
                                                    // ->maxValue(9999999999)
                                                    ->default(0)
                                                    ->columnSpanFull()
                                                    ->required(),
                                            ])->columnSpanFull()
                                            ->withoutHeader()
                                            ,
                                    ])
                                    // ->withoutHeader()
                                    ->columnSpanFull()
                                    // ->visible(fn (Get $get) => !empty($get('from')) ? true : false)


                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            // ->visible(fn (Get $get) => !empty($get('division_id')) ? true : false),



                    ])
                    // ->visible(fn (Get $get) => !empty($get('quarter_id')) ? true : false),

                // ...
            ])
            ->statePath('data')
            ->model(ProjectQuarter::class);
    }



}
