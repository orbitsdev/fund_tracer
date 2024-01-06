<?php

namespace App\Livewire;

use Livewire\Component;

class CreateQuarter extends Component
{

    public function mount($record){
        // dd($record);
    }
    public function render()
    {
        return view('livewire.create-quarter');
    }
}
