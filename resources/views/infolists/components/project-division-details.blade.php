<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">


    <div class="table w-full">
        @foreach ($getRecord()->project_years as $project_year)
            <div class="mt-2 rounded-lg p-2">
                @php
                    $yearGrandTotal = 0;
                    $PS = 0;
                    $MOOE = 0;
                    $EO = 0;
                @endphp
                <table class="border border-black p-2 w-full">
                    <tr>
                        <td class="border border-black p-2">{{ $project_year->year->title }}</td>
                    </tr>
                    @foreach ($project_year->project_quarters as $project_quarter)
                        <tr>
                            <td class="border border-black p-2 text-xs">{{ $project_quarter->quarter->title }}</td>
                        </tr>
                        @foreach ($project_quarter->quarter_expense_budget_divisions as $project_budget_division)
                            <tr>
                                <td class="border border-black p-2 text-xs">{{ $project_budget_division->project_division->division->title }}</td>
                                <td>
                                    <table class="w-full p-2 text-xs">
                                        @php
                                            $sectionGrandTotal = 0;
                                            $divisionTotal = 0;
                                        @endphp
                                        @foreach ($project_budget_division->quarter_expenses->groupBy('fourth_layer.project_division_sub_category_expense.project_division_category.from') as $division_category => $expensesByDivision)
                                            <tr class="border border-black p-2 m-2">
                                                <td class="font-bold px-2 ">{{ $division_category }}</td>
                                                <td class="border border-black">
                                                    @foreach ($expensesByDivision->groupBy(['fourth_layer.project_division_sub_category_expense.parent_title', 'fourth_layer.project_division_sub_category_expense.title']) as $title => $expensesDivisionSub)
                                                        @if (!empty($title) && $division_category === 'Indirect Cost')
                                                            <tr>
                                                                <td class="text-xs">{{ $title }}</td>
                                                            </tr>
                                                        @endif
                                                        @foreach ($expensesDivisionSub as $expense_title => $expenses)
                                                            <tr>
                                                                <td class="text-xs" style="padding-left:10px">{{ $expense_title }} </td>
                                                            </tr>
                                                            @foreach ($expenses as $expense)
                                                                <tr>
                                                                    <td class="text-xs" style="padding-left:20px">{{ $expense->fourth_layer->title }}</td>
                                                                    <td class="text-xs border border-black text-right px-1" style="padding-left:60px ">{{ $expense->amount }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach
                                                    @endforeach
                                                    @php
                                                        $sectionGrandTotal += $expensesByDivision->sum('amount');
                                                        $divisionTotal += $expensesByDivision->sum('amount');
                                                    @endphp
                                                    @if($division_category === 'Indirect Cost')
                                                    <tr class="" style="background: rgb(214, 216, 218) !important; color:rgb(44, 42, 42);">
                                                        <td class="border border-black   text-gray-600" style="padding-left:10px">{{$project_budget_division->project_division->division->abbreviation }} IC Total</td>
                                                        <td class="border border-black font-bold text-right px-2  text-gray-600 text-xs "> {{ number_format($project_budget_division->quarter_expenses()->whereHas('fourth_layer.project_division_sub_category_expense.project_division_category', function($query) {
                                                            $query->where('from', 'Indirect Cost');
                                                        })->sum('amount'), 2) }}</td>
                                                    </tr>
                                                    @else
                                                    <tr class="" style="background: rgb(214, 216, 218) !important; color:rgb(44, 42, 42);">
                                                        <td class="border border-black   " style="padding-left:10px">{{$project_budget_division->project_division->division->abbreviation }} DC Total</td>
                                                        <td  class="border border-black font-bold text-right px-2   text-xs "> {{ number_format($project_budget_division->quarter_expenses()->whereHas('fourth_layer.project_division_sub_category_expense.project_division_category', function($query) {
                                                            $query->where('from', 'Direct Cost');
                                                        })->sum('amount'), 2) }}</td>
                                                    </tr>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr style="background: rgb(53, 33, 128) !important; color:white;">
                                            <td class="border border-black p-2   " style="padding-left:10px">Total For {{ $project_budget_division->project_division->division->abbreviation }} </td>
                                            <td class="border border-black text-right  p-2  text-xs ">{{ number_format($divisionTotal, 2) }}</td>
                                        </tr>
                                        @php
                                            $yearGrandTotal += $divisionTotal;

                                            if($project_budget_division->project_division->division->abbreviation === 'PS'){
                                                $PS += $divisionTotal;
                                            }
                                            if($project_budget_division->project_division->division->abbreviation === 'MOOE'){
                                                $MOOE += $divisionTotal;
                                            }
                                            if($project_budget_division->project_division->division->abbreviation === 'EO'){
                                                $EO += $divisionTotal;
                                            }

                                        @endphp
                                    </table>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </table>
                <div class="flex flex-col mt-4 border-t border-gray-300 p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-600">Year Grand Total:</span>
                        <span class="text-xs text-gray-600">{{ number_format($yearGrandTotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-4 border-t border-gray-300 pt-4">
                        <span class="text-xs text-gray-600">Total PS:</span>
                        <span class="text-xs text-gray-600">{{ number_format($PS, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-4 border-t border-gray-300 pt-4">
                        <span class="text-xs text-gray-600">Total MOOE:</span>
                        <span class="text-xs text-gray-600">{{ number_format($MOOE, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-4 border-t border-b border-gray-300 pt-4 pb-4">
                        <span class="text-xs text-gray-600">Total OE:</span>
                        <span class="text-xs text-gray-600">{{ number_format($EO, 2) }}</span>
                    </div>
                </div>

            </div>
        @endforeach
    </div>





{{--
    <div>
        @foreach ($getRecord()->project_years as $project_year)
        <p class="border border-black p-2">
            {{ $project_year->year->title }}
        </p>
        @foreach ($project_year->project_quarters as $project_quarter)
            <p class="border border-black p-2">
                {{ $project_quarter->quarter->title }}
            </p>
            @foreach ($project_quarter->quarter_expense_budget_divisions as $project_budget_division)
                <p class="border border-black p-2">
                    {{ $project_budget_division->project_division->division->title }}
                </p>
                @foreach ($project_budget_division->quarter_expenses->groupBy('fourth_layer.project_division_sub_category_expense.project_division_category.from') as $division_category => $expensesByDivision)
                    <p class="border border-black p-2 font-bold">
                        {{ $division_category }}
                    </p>
                    @foreach ($expensesByDivision->groupBy(['fourth_layer.project_division_sub_category_expense.parent_title', 'fourth_layer.project_division_sub_category_expense.title']) as $title => $titleGroup)
                        @if (!empty($title) && $division_category === 'Indirect Cost')
                            <p class="border border-black p-2 font-bold">
                                {{ $title }}
                            </p>
                        @endif

                        @foreach ($titleGroup as $kd => $pr)
                            <p class="border border-black p-2 font-bold">
                                {{ $kd }}
                            </p>
                            @foreach ($pr as $a)
                                <p class="border border-black p-2">
                                    {{ $a->fourth_layer->title }}
                                    {{ $a->amount }}
                                </p>
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
                <p class="font-bold">
                    Grand Total {{ number_format($project_budget_division->quarter_expenses->sum('amount'), 2) }}
                </p>
                <p class="font-bold">
                    Direct Cost Total
                    {{ number_format($project_budget_division->quarter_expenses()->whereHas('fourth_layer.project_division_sub_category_expense.project_division_category', function($query) {
                        $query->where('from', 'Direct Cost');
                    })->sum('amount'), 2) }}
                </p>
                <p class="font-bold">
                    Indirect Cost
                    {{ number_format($project_budget_division->quarter_expenses()->whereHas('fourth_layer.project_division_sub_category_expense.project_division_category', function($query) {
                        $query->where('from', 'Indirect Cost');
                    })->sum('amount'), 2) }}
                </p>
            @endforeach
        @endforeach
    @endforeach
    </div> --}}
</x-dynamic-component>
