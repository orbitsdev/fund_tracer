<x-filament-panels::page>


{{-- {{dd($record)}} --}}

    <div>
        @foreach ($record->projects as $project)
        <div class="mt-6">

            <table class="w-full text-xs border">
                <thead>
                    <tr>
                        <td colspan="4" class="p-2 font-medium">
                           Porject:  {{$project->title}}
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-black font-medium p-1 text-xs text-center">Year</th>
                        <td class="border border-black font-medium p-1 text-xs text-center">PS</td>
                        <td class="border border-black font-medium p-1 text-xs text-center">MOOE</td>
                        <td class="border border-black font-medium p-1 text-xs text-center">EO</td>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $GPS = 0;
                        $GMOOE = 0;
                        $GEO = 0;
                    @endphp

                    @foreach ($project->project_years as $project_year)
                        @php
                            $PS = 0;
                            $MOOE = 0;
                            $EO = 0;
                        @endphp
                        <tr >
                            <td   class="border border-black p-1 text-xs text-c">{{ $project_year->year->title }}</td>
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
                            <td  class="text-xs text-gray-600 text-right border">{{ number_format($PS, 2) }}</td>
                            <td  class="text-xs text-gray-600 text-right border">{{ number_format($MOOE, 2) }}</td>
                            <td  class="text-xs text-gray-600 text-right border">{{ number_format($EO, 2) }}</td>
                        </tr>

                        @php
                            $GPS += $PS;
                            $GMOOE += $MOOE;
                            $GEO += $EO;
                        @endphp
                    @endforeach
                </tbody>

                <tfoot>
                    <tr style="background: rgb(53, 33, 128) !important; color:white;" class="border p-2">
                        <td  class="text-xs text-left p-4 ">Grand Total</th>
                        <td class="text-xs text-right p-4 border border-white ">{{ number_format($GPS, 2) }}</td>
                        <td class="text-xs text-right p-4 border border-white ">{{ number_format($GMOOE, 2) }}</td>
                        <td class="text-xs text-right p-4 border border-white ">{{ number_format($GEO, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endforeach

    </div>





</x-filament-panels::page>
