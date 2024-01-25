<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use App\Models\Program;
use App\Models\Project;
use Filament\Actions\Action;
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

        // dd('test');
        $project = Project::find($data['id']);
        $total_expenses = $project->expenses()->sum('amount');

        //fill program oberview
        $data['program_name_overview'] = $project->program ? $project->program->title : '';
        $data['program_budget_overview'] = number_format($project->program ? $project->program->total_budget : 0);
        $data['program_use_budget_overview'] = number_format($project->program ? $project->program->total_usage : 0);
        $data['current_allocated_budget'] = number_format($project->allocated_fund);
       
        //get sum of allocated projects = 
        
        
        //fill project overview

        $allocatedFund = (float) str_replace(',', '',  $project->allocated_fund);
        if (!empty($project->program)) {
            $total_allocated_projects = $project->program->projects->sum('allocated_fund');
            $remaining_budget =  floatval(str_replace(',', '', $project->program->total_budget)) - $total_allocated_projects;
            // $left_budget = $remaining_budget - $allocatedFund;
            $left_budget = $remaining_budget;
            $data['program_remaining_budget_overview'] = number_format($remaining_budget);
            $data['left_budget'] = number_format($left_budget);
        
           
        } else {
            
            $data['program_remaining_budget_overview'] = null;
            $data['left_budget'] = null;
        }
        
 
        $data['project_fund'] =0;
        // $data['project_fund'] =number_format($project->allocated_fund);
        $data['total_expenses'] = number_format($total_expenses);
        $start_date = $project->start_date;
        $end_date = $project->end_date;



        if ($start_date && $end_date) {
            $startDate = Carbon::parse($start_date);
            $endDate = Carbon::parse($end_date);

            // Calculate the difference in months
            $totalMonths = $endDate->diffInMonths($startDate);

            // Set the duration in months
             $data['duration_overview'] = $totalMonths . ' months';
            }

            $data['current_duration_overview'] = Carbon::parse($project->start_date)->format('F d, Y') . ' - ' . Carbon::parse($project->end_date)->format('F d, Y');

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
        unset($data['duration_overview']);
        unset($data['current_duration_overview']);


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
            Action::make('Back')->label('Back')->icon('heroicon-m-arrow-uturn-left')->outlined()->color('gray')->url(fn (): string => ProjectResource::getUrl('index')),

            Actions\Action::make('View')->outlined()->icon('heroicon-m-eye')->url(fn (Model $record): string => ProjectResource::getUrl('view', ['record'=> $record]))->label('View Details'),
            // Actions\Action::make('Create Quarters')->outlined()->icon('heroicon-m-sparkles')->url(fn (Model $record): string => ProjectResource::getUrl('manage-quarter-year', ['record'=> $record]))->label('Create Quarters'),
            Actions\Action::make('Manage Quarter')->outlined()->icon('heroicon-m-pencil-square')->url(fn (Model $record): string => ProjectResource::getUrl('manage-quarter-year', ['record'=> $record]))->label('Manage Quarters'),

            //  Actions\DeleteAction::make(),
        ];
    }
}
