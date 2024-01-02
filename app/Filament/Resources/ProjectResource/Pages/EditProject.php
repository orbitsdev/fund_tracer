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

        $project = Project::find($data['id']);
        $total_expenses = $project->expenses()->sum('amount');

        //fill program oberview
        $data['program_name_overview'] = $project->program->title;
        $data['program_budget_overview'] = number_format($project->program->total_budget);
        $data['program_use_budget_overview'] =   number_format($project->program->total_usage);
        $data['program_remaining_budget_overview'] = number_format($project->program->total_budget - $project->program->total_usage);
        
        //fill project overview

        $data['project_fund'] =number_format($project->allocated_fund);
        $data['total_expenses'] = number_format($total_expenses);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['total_expenses']);
        unset($data['program_name_overview']);
        unset($data['program_budget_overview']);
        unset($data['program_use_budget_overview']);
        unset($data['program_remaining_budget_overview']);
        unset($data['project_fund']);
        
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
