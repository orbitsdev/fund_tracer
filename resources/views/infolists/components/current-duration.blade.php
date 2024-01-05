<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">

    <div>


 {{\Carbon\Carbon::parse($getRecord()->start_date)->format('F d, Y') . ' - ' .\Carbon\Carbon::parse($getRecord()->end_date)->format('F d, Y') }}
    </div>
</x-dynamic-component>
