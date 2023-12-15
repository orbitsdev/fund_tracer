<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use App\Models\Program;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProjectResource;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;
    protected static bool $canCreateAnother = false;

      // public $budget;
      // public function mount(): void
      // {
      //     $this->budget = Program::first();
      //     // dd((int)$this->budget->total_budget);
      // }

      protected function mutateFormDataBeforeCreate(array $data): array
      {
  
         unset($data['total_expenses']);
        //  unset($data['project_fund']);
        // dd($data);
          return $data;
      }
  
      protected function handleRecordCreation(array $data): Model
  {
      $program = Program::first();
      // dd($program->total_usage, $data['allocated_fund']);
     $sum= $program->total_usage =$program->total_usage + $data['allocated_fund'];
      // dd($sum);
      $program->save();
     
      return static::getModel()::create($data);
  }
     
    
    protected function getRedirectUrl(): string
    {
    return $this->getResource()::getUrl('index');
    }

}
