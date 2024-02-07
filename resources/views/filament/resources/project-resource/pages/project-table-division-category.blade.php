<x-filament-panels::page>
 <div>

        <p class="text-2xl mb-4 font-bold text-primary-600">
            {{ $this->record->division->title }}
        </p>

        {{$this->table}}
    </div>
</x-filament-panels::page>
