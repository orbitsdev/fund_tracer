<x-filament-panels::page>
 <div>

        <p class="text-lg mb-2 font-medium text-primary-600">
            {{ $this->record->division->title }}
        </p>
        {{$this->table}}
    </div>
</x-filament-panels::page>
