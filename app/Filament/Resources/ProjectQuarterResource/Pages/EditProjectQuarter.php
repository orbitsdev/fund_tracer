<?php

namespace App\Filament\Resources\ProjectQuarterResource\Pages;

use App\Filament\Resources\ProjectQuarterResource;
use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\View\View;
class EditProjectQuarter extends EditRecord
{
    protected static string $resource = ProjectQuarterResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
{
    // dd($this->getRecord()->project_year);
    return $data;
}

    protected function getRedirectUrl(): string
    {


        // dd($this->getOwnerRecord());
        // return redirect()->route('filament.admin.resources.projects.index');
      return ProjectResource::getUrl('index');
    }
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
