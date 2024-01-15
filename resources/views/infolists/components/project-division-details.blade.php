
<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">



    <div class=" mt-4">

        @foreach ($getRecord()->project_years as $projectYear)
            <div class="border rounded-lg text-xs border-gray-500 px-4 py-2 mt-4">
                <p class="text-md font-medium">{{ $projectYear->year->title }}</p>

                <div class="">

                @foreach ($projectYear->project_quarters as $projectQuarter)
                    <div class=" border text-xs border-gray-500 px-4 py-2 ml-4 mt-2">
                        <p class="text-md font-bold ">{{ $projectQuarter->quarter->title }}</p>

                        @foreach ($projectQuarter->quarter_expense_budget_divisions as $budgetDivision)
                            <div class="border text-xs border-gray-500 px-4 py-2 ml-4 mt-2">
                                <p class="text-md  font-bold">{{ $budgetDivision->project_division->division->title }}</p>
                                @foreach ($budgetDivision->quarter_expenses->groupBy('fourth_layer.project_division_sub_category_expense.parent_title') as $parentTitle => $expensesGroup)
                                <p class="text-md font-bold">{{ $parentTitle }}</p>
                                <p class="text-xs  " style="margin-left: 8px;" >{{ $expensesGroup->first()->fourth_layer->project_division_sub_category_expense->project_division_category->from }}</p>
                                <p class="text-xs "  style="margin-left: 16px;">{{ $expensesGroup->first()->fourth_layer->project_division_sub_category_expense->title }}</p>
                                @foreach ($expensesGroup as $expenses)
                                <div class=" text-xs border-gray-500 px-4 py-2 ml-4 "  style="margin-left: 24px;">
                                    <p class="text-md ">{{ $expenses->fourth_layer->title }}</p>
                                    <p class="text-xs ">{{ $expenses->title }}</p>
                                    <p class="text-xs  px-4 py-2 ">
                                        {{ number_format($expenses->amount) }}
                                    </p>
                                </div>
                            @endforeach
                                {{-- @dump($expensesGroup) --}}
                                {{-- <div class="border text-xs border-gray-500 px-4 py-2 ml-4 mt-2">
                                        <p class="text-md font-bold">{{ $parentTitle }}</p>
                                        <p class="text-xs font-bold">{{ $expensesGroup->first()->fourth_layer->project_division_sub_category_expense->title }}</p>
                                        <p class="text-xs font-bold">{{ $expensesGroup->first()->fourth_layer->project_division_sub_category_expense->project_division_category->from }}</p>
                                        @foreach ($expensesGroup as $expenses)
                                            <div class="border text-xs border-gray-500 px-4 py-2 ml-4 mt-2">
                                                <p class="text-md font-bold">{{ $expenses->fourth_layer->title }}</p>
                                                <p class="text-xs font-bold">{{ $expenses->title }}</p>
                                                <p class="border text-xs border-gray-500 px-4 py-2 font-bold">
                                                    {{ number_format($expenses->amount) }}
                                                </p>
                                            </div>
                                        @endforeach


                                        <div class="border text-xs border-gray-500 px-4 py-2 ml-4 mt-2">
                                            <p class="text-md font-bold">Total</p>
                                            <p class="border text-xs border-gray-500 px-4 py-2 font-bold">
                                                {{ number_format($expensesGroup->sum('amount')) }}
                                            </p>
                                        </div>
                                    </div> --}}
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    @endforeach
                </div>
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
