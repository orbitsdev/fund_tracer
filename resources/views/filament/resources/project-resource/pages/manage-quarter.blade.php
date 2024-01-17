<x-filament-panels::page>



 <p class="text-lg"> {{ $record->project->title }}</p>
 <div>

     {{$this->back}}
 </div>
<form wire:submit="create">
    {{ $this->form }}


    {{-- <div class="mb-2">
        {{ $createQuarter }}
    </div>
    --}}
    <button type="submit" class="mt-6 bg-primary-600 rounded p-2 text-white " >
        Submit
    </button>
</form>

<x-filament-actions::modals />
</x-filament-panels::page>
