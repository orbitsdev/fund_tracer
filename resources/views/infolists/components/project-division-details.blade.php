<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">





    <div class="container mx-auto mt-8">
        @foreach ($getRecord()->project_years as $project_year)
            <div class="year-container border text-xs border-gray-500 mt-2 rounded-lg">
                <p class="year-title text-lg font-bold p-2 text-center">
                    {{ $project_year->year->title }}
                </p>

                <table class="w-full border text-xs border-gray-500">
                    <thead>
                        <tr class="border text-xs border-gray-500 px-4 py-2 ">
                            <!-- Common Headers -->
                            <th class="py-2 " >Quarters</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($project_year->project_quarters as $project_quarter)
                            <tr>
                                <!-- Quarter -->
                                <td
                                    class="border text-xs border-gray-500 px-4 py-2 text-md font-medium ">
                                    {{ $project_quarter->quarter->title }}
                                </td>
                                @foreach ($project_quarter->project_divisions as $project_division)
                                    <tr>
                                        <!-- Division -->
                                        <td
                                            class="border text-xs border-gray-500 px-4 py-2 font-bold">
                                            {{ $project_division->division->title }}
                                        </td>
                                        @foreach ($project_division->project_division_categories as $project_division_category)
                                        <tr>
                                            <!-- Category -->
                                            <td class="border text-xs border-gray-500 px-4 py-2">
                                                {{ $project_division_category->from }}
                                            </td>
                                            <!-- Subcategory and Expense Table -->
                                            <td class="border text-xs border-gray-500 px-4 py-2">
                                                <table class="border text-xs border-gray-500 w-full">
                                                    @foreach ($project_division_category->project_division_sub_category_expenses as $thirdlayer)
                                                        <tr>
                                                            <!-- Subcategory and Expense Details -->
                                                            <td class="border text-xs border-gray-500 px-4 py-2">
                                                                @if ($project_division_category->from === 'Indirect Cost')
                                                                    <p class="font-bold">
                                                                        {{ $thirdlayer->parent_title }}
                                                                    </p>
                                                                @endif
                                                                {{ $thirdlayer->title }}
                                                            </td>
                                                            <td class="border text-xs border-gray-500 px-4 py-2">
                                                                {{-- Display a download link for each expense --}}
                                                                @if ($thirdlayer->file_path)
                                                                    <a href="{{ asset($thirdlayer->file_path) }}" download>Download Receipt</a>
                                                                @else
                                                                    No Receipt Available
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @foreach ($thirdlayer->fourth_layers as $fourthlayer)
                                                            <!-- Fourth Layer Details -->
                                                            <tr>
                                                                <td class="border text-xs border-gray-500 px-8 py-2"></td>
                                                                <td class="border text-xs border-gray-500 px-8 py-2"></td>
                                                                <td class="border text-xs border-gray-500 px-8 px-4 py-2">
                                                                    {{ $fourthlayer->title }}
                                                                </td>
                                                                <td class="border text-xs border-gray-500 px-8 px-4 py-2">
                                                                    {{ number_format($fourthlayer->amount) }}
                                                                </td>
                                                                <!-- Download link for each fourth layer -->
                                                                {{-- <td class="border  text-xs border-gray-500 text-blue underline  px-8 px-4 py-2">
                                                                    Download

                                                                        @if ($fourthlayer->file_path)
                                                                        <a href="{{ asset($fourthlayer->file_path) }}" download>Download Receipt</a>
                                                                    @else
                                                                    @endif

                                                                </td> --}}
                                                            </tr>
                                                        @endforeach

                                                        <!-- Total for Fourth Layer -->
                                                        <tr>
                                                            <td class="border text-xs border-gray-500 px-8 py-2"></td>
                                                            <td class="border text-xs border-gray-500 px-8 px-4 py-2 font-bold">Total</td>
                                                            <td class="border text-xs border-gray-500 px-8 px-4 py-2 font-bold">
                                                                {{ number_format($thirdlayer->fourth_layers->sum('amount')) }}
                                                            </td>
                                                            <td class="border text-xs border-gray-500 px-8 px-4 py-2"></td> <!-- Empty column for consistency -->
                                                        </tr>
                                                    @endforeach
                                                </table>

                                            </td>
                                        </tr>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>





{{--
    {{--
         <div class="container mx-auto mt-8">
        @foreach ($getRecord()->project_years as $project_year)
            <div class="year-container border text-xs border-gray-500 mt-2 rounded-lg">
                <p class="year-title text-lg font-bold p-2 text-center">
                    {{ $project_year->year->title }}
                </p>

                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="border text-xs border-gray-500 px-4 py-2">Quarter</th>
                            <th class="border text-xs border-gray-500 px-4 py-2">Division</th>
                            <th class="border text-xs border-gray-500 px-4 py-2">Category</th>
                            <th class="border text-xs border-gray-500 px-4 py-2">Subcategory</th>
                            <th class="border text-xs border-gray-500 px-4 py-2">Expense</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($project_year->project_quarters as $project_quarter)
                            @foreach ($project_quarter->project_divisions as $first)
                                <tr>
                                    <td rowspan="{{ $first->project_division_categories->count() }}"
                                        class="border text-xs border-gray-500 px-4 py-2">
                                        @if ($loop->first)
                                            {{ $project_quarter->quarter->title }}
                                        @endif
                                    </td>
                                    <td rowspan="{{ $first->project_division_categories->count() }}"
                                        class="border text-xs border-gray-500 px-4 py-2">
                                        {{ $first->division->title }}
                                    </td>
                                    @foreach ($first->project_division_categories as $secondlayer)
                                        @foreach ($secondlayer->project_division_sub_category_expenses as $thirdlayer)
                                            <tr>
                                                <td class="border text-xs border-gray-500 px-4 py-2">
                                                    {{ $secondlayer->from }}
                                                </td>
                                                <td class="border text-xs border-gray-500 px-4 py-2">
                                                    @if ($secondlayer->from === 'Indirect Cost')
                                                        {{ $thirdlayer->parent_title }}
                                                    @endif
                                                    {{ $thirdlayer->title }}
                                                </td>
                                                <td class="border text-xs border-gray-500 px-4 py-2">
                                                    {{ number_format($thirdlayer->fourth_layers->sum('amount')) }}
                                                </td>
                                            </tr>
                                            @foreach ($thirdlayer->fourth_layers as $fourthlayer)
                                                <tr>
                                                    <td class="border text-xs border-gray-500 px-8 py-2"></td>
                                                    <td class="border text-xs border-gray-500 px-8 py-2">
                                                        {{ $fourthlayer->title }}
                                                    </td>
                                                    <td class="border text-xs border-gray-500 px-8 py-2">
                                                        {{ number_format($fourthlayer->amount) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
    --}}
</x-dynamic-component>
