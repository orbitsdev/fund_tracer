<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProjectResource;
use Filament\Forms\Components\Repeater;

class EditProjectQuarter extends EditRecord
{
    protected static string $resource = ProjectResource::class;

public function form(Form $form): Form
{
    return $form
        ->schema([
            Repeater::make('project_divisions')
            ->relationship()
            ->schema([
                TextInput::make('title')
                    ->label('Title')
                    ->required(),

            ])->columnSpanFull()
        ]);
}
}
