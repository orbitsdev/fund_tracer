<?php

namespace App\Filament\Resources\ProjectQuarterResource\Pages;

use App\Filament\Resources\ProjectQuarterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\View\View;
class EditProjectQuarter extends EditRecord
{
    protected static string $resource = ProjectQuarterResource::class;

    public function getHeader(): ?View
    {
        return view('filament.settings.custom-header',['title'=> 'Edit Project Quarter', 'first'=> 'Project Quarters' ,'second'=> 'Edit']);
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
