<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        @foreach ($getRecord()->projects as $project)
            @foreach ($project->project_years as $project_year)
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
                            {{-- <tr style="background: #F2F2F2 !important; color:#2f2f31;">
                                <td class="border border-black p-2 text-xs">{{ $project_quarter->quarter->title }}</td>
                            </tr> --}}
                            @foreach ($project_quarter->quarter_expense_budget_divisions as $project_budget_division)
                                <tr>
                                    <td class="border border-black p-2 text-xs">
                                        {{ $project_budget_division->project_division->division->title }}</td>
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
                                            @php
                                                $subTotal = 0; // Initialize subTotal variable for the current category
                                            @endphp
                                            @foreach ($expensesDivisionSub as $expense_title => $expenses)
                                                <tr>
                                                    <td class="text-xs" style="padding-left:10px">{{ $expense_title }}
                                                    </td>
                                                </tr>
                                                @php
                                                    $categoryTotal = 0; // Initialize categoryTotal variable for the current sub-category
                                                @endphp
                                                @foreach ($expenses as $expense)
                                                    <tr>
                                                        <td class="text-xs" style="padding-left:20px">
                                                            {{ $expense->fourth_layer->title }}</td>
                                                        <td class="text-xs border border-black text-right px-1"
                                                            style="padding-left:60px">{{ $expense->amount }}</td>
                                                    </tr>
                                                    @php
                                                        $categoryTotal += $expense->amount; // Accumulate expenses for the current sub-category
                                                        $subTotal += $expense->amount; // Accumulate expenses for the current category
                                                    @endphp
                                                @endforeach
                                                <!-- Display the total for the current sub-category -->
                                                <tr>
                                                    <td class="text-xs" style="padding-left:10px">Sub-Total</td>
                                                    <td class="text-xs border border-black text-right px-1"
                                                        style="padding-left:60px">{{ $categoryTotal }}</td>
                                                </tr>
                                            @endforeach
                                            <!-- Display the total for the current category -->
                                            <tr>
                                                <td class="text-xs">Total</td>
                                                <td class="text-xs border border-black text-right px-1">
                                                    {{ $subTotal }}</td>
                                            </tr>
                            @endforeach

                            @php
                                $sectionGrandTotal += $expensesByDivision->sum('amount');
                                $divisionTotal += $expensesByDivision->sum('amount');
                            @endphp
                            @if ($division_category === 'Indirect Cost')
                                <tr class="" style="background: #F2F2F2 !important; color:#2f2f31;">
                                    <td class="border border-black   text-gray-600" style="padding-left:10px">
                                        {{ $project_budget_division->project_division->division->abbreviation }} IC
                                        Total</td>
                                    <td class="border border-black font-bold text-right px-2  text-gray-600 text-xs ">
                                        {{ number_format(
                                            $project_budget_division->quarter_expenses()->whereHas('fourth_layer.project_division_sub_category_expense.project_division_category', function ($query) {
                                                    $query->where('from', 'Indirect Cost');
                                                })->sum('amount'),
                                            2,
                                        ) }}
                                    </td>
                                </tr>
                            @else
                                <tr class="" style="background: #F2F2F2 !important; color:#2f2f31;">
                                    <td class="border border-black   " style="padding-left:10px">
                                        {{ $project_budget_division->project_division->division->abbreviation }} DC
                                        Total</td>
                                    <td class="border border-black font-bold text-right px-2   text-xs ">
                                        {{ number_format(
                                            $project_budget_division->quarter_expenses()->whereHas('fourth_layer.project_division_sub_category_expense.project_division_category', function ($query) {
                                                    $query->where('from', 'Direct Cost');
                                                })->sum('amount'),
                                            2,
                                        ) }}
                                    </td>
                                </tr>
                            @endif
                            </td>
                            </tr>
                        @endforeach
                        <tr style="background: #352180 !important; color:white;">
                            <td class="border border-black p-2   " style="padding-left:10px">Total For
                                {{ $project_budget_division->project_division->division->abbreviation }} </td>
                            <td class="border border-black text-right  p-2  text-xs ">
                                {{ number_format($divisionTotal, 2) }}</td>
                        </tr>
                        @php
                            $yearGrandTotal += $divisionTotal;

                            if ($project_budget_division->project_division->division->abbreviation === 'PS') {
                                $PS += $divisionTotal;
                            }
                            if ($project_budget_division->project_division->division->abbreviation === 'MOOE') {
                                $MOOE += $divisionTotal;
                            }
                            if ($project_budget_division->project_division->division->abbreviation === 'EO') {
                                $EO += $divisionTotal;
                            }

                        @endphp
                    </table>
                    </td>
                    </tr>
            @endforeach
        @endforeach
        </table>


    </div>
    @endforeach
    @endforeach

    </div>

</x-dynamic-component>
