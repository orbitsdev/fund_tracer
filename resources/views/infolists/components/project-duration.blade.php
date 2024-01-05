<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">

    @if ($getRecord()->start_date && $getRecord()->end_date)

    <p class="text-sm">

        @php
            $startDate = \Carbon\Carbon::parse($getRecord()->start_date);
            $endDate = \Carbon\Carbon::parse($getRecord()->end_date);

            // Calculate the difference in months
            $totalMonths = $endDate->diffInMonths($startDate);
        @endphp
        {{-- {{ $thirdlayer->parent_title }} --}}
        {{ $totalMonths . ''}} months
    </p>
@endif


</x-dynamic-component>
