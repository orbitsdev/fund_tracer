<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Actions;
use App\Models\Program;
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
    
    protected function getRedirectUrl(): string
    {
    return $this->getResource()::getUrl('index');
    }

}
