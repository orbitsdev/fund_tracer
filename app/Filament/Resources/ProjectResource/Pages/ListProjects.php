<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

//     public function getTabs(): array
// {
//     return [
//         'all' => Tab::make('All customers'),
//         'active' => Tab::make('Active customers')
//             ->modifyQueryUsing(fn (Builder $query) => $query->where('active', true)),
//         'inactive' => Tab::make('Inactive customers')
//             ->modifyQueryUsing(fn (Builder $query) => $query->where('active', false)),
//     ];
// }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            // ->beforeFormFilled(function(){

            // }),
        ];
    }
}
