<x-filament-panels::page>


{{-- {{dd($record)}} --}}

    <div>
        @foreach ($record->projects as $project)
        <div class="border border-black p-2">

            <p class="border border-black p-2">
                {{ $project->title }}
            </p>
            @foreach ($project->project_years as $project_year)
                <p>
                    {{ $project_year->year->title }}
                </p>
                @foreach ($project_year->project_quarters as $project_quarter)

                    <h3>{{ $project_quarter->title }}</h3>
                    @foreach ($project_quarter->project_divisions as $project_division)
                        <p>
                            {{ $project_division->division->title }}
                            @foreach ($project_division->project_division_categories as $project_division_category)
                                @foreach ($project_division_category->project_division_sub_category_expenses as $thirdLayer)
                                    {{ number_format($thirdLayer->fourth_layers->sum('amount')) }}
                                @endforeach
                            @endforeach
                        </p>
                    @endforeach
                @endforeach
            @endforeach
        </div>

        @endforeach

    </div>





</x-filament-panels::page>
