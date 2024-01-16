<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>

        @php
            $GPS = 0;
            $GMOOE = 0;
            $GEO = 0;
        @endphp

        @foreach ($getRecord()->project_years as $project_year)
            @php
                $PS = 0;
                $MOOE = 0;
                $EO = 0;
            @endphp
            <p>
                {{ $project_year->year->title }}
            </p>
            @foreach ($project_year->project_quarters as $project_quarter)

                @foreach ($project_quarter->quarter_expense_budget_divisions as $project_budget_division)
                    @php
                    if($project_budget_division->project_division->division->abbreviation == 'PS'){
                        $PS +=  $project_budget_division->quarter_expenses()->sum('amount');
                    }
                    if($project_budget_division->project_division->division->abbreviation == 'MOOE'){
                        $MOOE +=  $project_budget_division->quarter_expenses()->sum('amount');
                    }
                    if($project_budget_division->project_division->division->abbreviation == 'EO'){
                        $EO +=  $project_budget_division->quarter_expenses()->sum('amount');
                    }
                    @endphp
                @endforeach

            @endforeach

            @php
                $GPS += $PS;
                $GMOOE += $MOOE;
                $GEO += $EO;
            @endphp
            <div class="font-bold text-xs mt-4">
                Total PS: {{ number_format($PS, 2) }}
            </div>
            <div class="font-bold text-xs mt-4">
                Total MOOE: {{ number_format($MOOE, 2) }}
            </div>
            <div class="font-bold text-xs mt-4">
                Total EO: {{ number_format($EO, 2) }}
            </div>
        @endforeach

        <div class="font-bold text-xs mt-6">
           GRAND Total PS: {{ number_format($GPS, 2) }}
        </div>
        <div class="font-bold text-xs mt-4">
            GRAND Total MOOE: {{ number_format($GMOOE, 2) }}
        </div>
        <div class="font-bold text-xs mt-4">
            GRAND Total EO: {{ number_format($GEO, 2) }}
        </div>
    </div>
</x-dynamic-component>
