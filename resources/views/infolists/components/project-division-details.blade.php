
<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">

    <div class="border text-xs border-gray-500 px-4 py-2 mt-4">
        <h1 class="text-lg font-bold">{{ $getRecord()->title }}</h1>

        @foreach ($getRecord()->project_years as $projectYear)
            <div class="border text-xs border-gray-500 px-4 py-2 mt-4">
                <p class="text-md font-bold">{{ $projectYear->year->title }}</p>

                @foreach ($projectYear->project_quarters as $projectQuarter)
                    <div class="border text-xs border-gray-500 px-4 py-2 ml-4 mt-2">
                        <p class="text-md font-bold">{{ $projectQuarter->quarter->title }}</p>

                        <table class="border-collapse border text-xs border-gray-500 w-full">
                            <thead>
                                <tr>
                                    <th class="border text-xs border-gray-500 px-4 py-2">Division</th>
                                    <th class="border text-xs border-gray-500 px-4 py-2">Parent Title</th>
                                    <th class="border text-xs border-gray-500 px-4 py-2">Category Type</th>
                                    <th class="border text-xs border-gray-500 px-4 py-2">Expense Title</th>
                                    <th class="border text-xs border-gray-500 px-4 py-2">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($projectQuarter->quarter_expense_budget_divisions as $budgetDivision)
                                <tr>
                                    <td class="border text-xs border-gray-500 px-4 py-2">{{ $budgetDivision->project_division->division->title }}</td>
                                    <td class="border text-xs border-gray-500 px-4 py-2" rowspan="{{ count($budgetDivision->quarter_expenses) }}">{{ $budgetDivision->project_division->division->title }}</td>
                                    @foreach ($budgetDivision->quarter_expenses as $expenses)
                                        <td class="border text-xs border-gray-500 px-4 py-2">{{ $expenses->fourth_layer->project_division_sub_category_expense->parent_title }}</td>
                                        <td class="border text-xs border-gray-500 px-4 py-2">{{ $expenses->fourth_layer->project_division_sub_category_expense->project_division_category->from }}</td>
                                        <td class="border text-xs border-gray-500 px-4 py-2">{{ $expenses->fourth_layer->title }}</td>
                                        <td class="border text-xs border-gray-500 px-4 py-2">{{ number_format($expenses->amount) }}</td>
                                    </tr>
                                    <tr>
                                        <!-- Nested row for 'from' column -->
                                        <td class="border text-xs border-gray-500 px-4 py-2"></td>
                                        <td class="border text-xs border-gray-500 px-4 py-2" colspan="4">{{ $expenses->fourth_layer->project_division_sub_category_expense->from }}</td>
                                    </tr>
                                @endforeach
                            @endforeach

                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>




    {{-- <div class="border text-xs border-gray-500 px-4 py-2 mt-4">
        <h1 class="text-lg font-bold">{{ $getRecord()->title }}</h1>

        @foreach ($getRecord()->project_years as $projectYear)
            <div class="border text-xs border-gray-500 px-4 py-2 mt-4">
                <p class="text-md font-bold">{{ $projectYear->year->title }}</p>

                @foreach ($projectYear->project_quarters as $projectQuarter)
                    <div class="border text-xs border-gray-500 px-4 py-2 ml-4 mt-2">
                        <p class="text-md font-bold">{{ $projectQuarter->quarter->title }}</p>

                        @foreach ($projectQuarter->quarter_expense_budget_divisions as $budgetDivision)
                            <div class="border text-xs border-gray-500 px-4 py-2 ml-4 mt-2">
                                <p class="text-md font-bold">{{ $budgetDivision->project_division->division->title }}</p>

                                @foreach ($budgetDivision->quarter_expenses as $expenses)
                                    <div class="border text-xs border-gray-500 px-4 py-2 ml-4 mt-2">
                                        <p class="text-md font-bold">{{ $expenses->fourth_layer->title }}</p>
                                        <p class="border text-xs border-gray-500 px-4 py-2 font-bold">
                                            {{ number_format($expenses->amount) }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach
    </div> --}}


</x-dynamic-component>
