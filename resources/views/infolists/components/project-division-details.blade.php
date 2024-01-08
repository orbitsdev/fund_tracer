<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">



    <div class="container mx-auto mt-8">
        @foreach ($getRecord()->project_years as $project_year)
            <div class="year-container text-sm border border-gray-500 mt-2 rounded-lg">
                <p class="year-title text-lg font-bold p-2 text-center">
                    {{ $project_year->year->title }}
                </p>

                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="text-sm border border-gray-500 px-4 py-2">Quarter</th>
                            <th class="text-sm border border-gray-500 px-4 py-2">Division</th>
                            <th class="text-sm border border-gray-500 px-4 py-2">Category</th>
                            <th class="text-sm border border-gray-500 px-4 py-2">Subcategory</th>
                            <th class="text-sm border border-gray-500 px-4 py-2">Expense</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($project_year->project_quarters as $project_quarter)
                        @foreach ($project_quarter->project_divisions as $first)
                            @foreach ($first->project_division_categories as $secondlayer)
                                @foreach ($secondlayer->project_division_sub_category_expenses as $thirdlayer)
                                    <tr>
                                        <td class="text-sm border border-gray-500 px-4 py-2">{{ $project_quarter->quarter->title }}</td>
                                        <td class="text-sm border border-gray-500 px-4 py-2">{{ $first->division->title }}</td>
                                        <td class="text-sm border border-gray-500 px-4 py-2">{{ $secondlayer->from }}</td>
                                        <td class="text-sm border border-gray-500 px-4 py-2">
                                            @if ($secondlayer->from === 'Indirect Cost')
                                                {{ $thirdlayer->parent_title }}
                                            @endif
                                            {{ $thirdlayer->title }}
                                        </td>
                                        <td class="text-sm border border-gray-500 px-4 py-2">
                                            {{ number_format($thirdlayer->fourth_layers->sum('amount')) }}
                                        </td>
                                    </tr>
                                    @foreach ($thirdlayer->fourth_layers as $fourthlayer)
                                        <tr>
                                            <td class="text-sm border border-gray-500 px-8 py-2"></td>
                                            <td class="text-sm border border-gray-500 px-8 py-2"></td>
                                            <td class="text-sm border border-gray-500 px-8 py-2"></td>
                                            <td class="text-sm border border-gray-500 px-8 py-2">{{ $fourthlayer->title }}</td>
                                            <td class="text-sm border border-gray-500 px-8 py-2">
                                                {{ number_format($fourthlayer->amount) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach

                    </tbody>
                </table>
            </div>
        @endforeach
    </div>




</x-dynamic-component>
