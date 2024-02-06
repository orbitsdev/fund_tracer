<x-filament-panels::page>
 <div>

        <p class="text-2xl mb-4 font-bold text-primary-600">
            {{ $this->record->division->title }}
        </p>
        <p class="mb-2">The expenses listed under this division</p>
        {{$this->table}}
    </div>
</x-filament-panels::page>
