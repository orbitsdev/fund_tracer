<?php

namespace App\Filament\Resources\ProgramResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use App\Models\Program;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProgramResource;

class EditProgram extends EditRecord
{
    protected static string $resource = ProgramResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {

        $program = Program::find($data['id']);
        $start_date = $program->start_date;
        $end_date = $program->end_date;
    
        if ($start_date && $end_date) {
            $startDate = Carbon::parse($start_date);
            $endDate = Carbon::parse($end_date);
    
            // Calculate the difference in months
            $totalMonths = $endDate->diffInMonths($startDate);
    
            // Set the duration in months
             $data['duration'] = $totalMonths . ' months';
        }
    
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['duration']);
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
