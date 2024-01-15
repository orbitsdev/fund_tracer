<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">



    <div>
        @foreach ($getRecord()->project_years as $project_year)
            @foreach ( $project_year->project_quarters as $project_quarter)
                @foreach ($project_quarter->quarter_expense_budget_divisions as $project_budget_division )
                    {{$project_budget_division->project_division->division->title}}
                    @php
                    $groupedExpenses = $project_budget_division->quarter_expenses->groupBy('fourth_layer.project_division_sub_category_expense.project_division_category.from');
                @endphp

                @foreach ($groupedExpenses as $fromKey => $expenses)
                    <p>
                        From: {{$fromKey}}
                    </p>
                    @foreach ($expenses->groupBy('fourth_layer.project_division_sub_category_expense.title') as $titleKey => $subCategoryExpenses)
                        <p>
                            Title: {{$titleKey}}
                        </p>
                        @foreach ($subCategoryExpenses as $data)
                            <p>
                                {{$data->fourth_layer->title}}
                                {{$data->fourth_layer->amount}}
                            </p>
                        @endforeach
                    @endforeach
                @endforeach

                @endforeach
            @endforeach
        @endforeach
    </div>
    {{--
    <div class=" mt-4">

        @foreach ($getRecord()->project_years as $project_years)
            <div class="border rounded-lg text-xs border-gray-500 px-4 py-2 mt-4">
                <p class="text-md font-medium">{{ $projectYear->year->title }}</p>

                <div class="">

                @foreach ($projectYear->project_quarters as $projectQuarter)
                    <div class=" border text-xs border-gray-500 px-4 py-2 ml-4 mt-2">
                        <p class="text-md font-bold ">{{ $projectQuarter->quarter->title }}</p>

                      @foreach ($projectQuarter->quarter_expense_budget_divisions as $budgetDivision)
                        <div class="border text-xs border-gray-500 px-4 py-2 ml-4 mt-2">
                            <p class="text-md font-bold">{{ $budgetDivision->project_division->division->title }}</p>

                            @foreach ($budgetDivision->quarter_expenses->groupBy(['fourth_layer.project_division_sub_category_expense.parent_title', 'fourth_layer.title']) as $parentTitle => $titleGroup)

                                <p class="text-md font-bold">{{ $parentTitle[0] }}</p>
                                <p class="text-xs" style="margin-left: 8px;">{{ $titleGroup->first()->fourth_layer->project_division_sub_category_expense->project_division_category->from }}</p>
                                <p class="text-xs" style="margin-left: 16px;">{{ $titleGroup->first()->fourth_layer->project_division_sub_category_expense->title }}</p>

                                @foreach ($titleGroup as $expenses)
                                    <div class="text-xs border-gray-500 px-4 py-2 ml-4" style="margin-left: 24px;">
                                        <p class="text-md">{{ $expenses->fourth_layer->title }} {{ number_format($expenses->amount) }}</p>
                                        <p class="text-xs px-4 py-2">
                                            Total DC <span class="font-normal">{{ number_format($expenses->amount) }}</span>
                                        </p>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    @endforeach

                    </div>

                    @endforeach
                </div>
            </div>
        @endforeach
    </div> --}}



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
