<?php

namespace App\Filament\Resources\ProgramResource\Pages;

use Filament\Actions;
use App\Models\Program;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProgramResource;

class CreateProgram extends CreateRecord
{
    protected static string $resource = ProgramResource::class;
    protected static bool $canCreateAnother = false;


    protected function mutateFormDataBeforeCreate(array $data): array
      {
        //  dd($data);
        unset($data['program_leader_overview']);
        unset($data['duration_overview']);
        unset($data['current_duration_overview']);
        // unset($data['program_name_overview']);
        // unset($data['program_budget_overview']);
        // unset($data['program_use_budget_overview']);
        // unset($data['program_remaining_budget_overview']);
        // unset($data['project_fund']);
        // dd($data);
          return $data;
      }

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
  
}
