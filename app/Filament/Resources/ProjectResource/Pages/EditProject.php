<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use App\Models\Program;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProjectResource;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
{

    // dd($data);
    $record = Project::find($data['id']);
    // dd($record, $data);

    $total_expenses =$record->expenses()->sum('amount');
    // dd($total_expenses);
    

     $data['total_expenses'] = $total_expenses;
 
    return $data;
}
    protected function mutateFormDataBeforeSave(array $data): array
{
       unset($data['total_expenses']);
      //  unset($data['project_fund']);
      // dd($data);
        return $data;
    }
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }
    protected function getHeaderActions(): array
    {
        return [
          //  Actions\DeleteAction::make(),
        ];
    }
}
