<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">

    <div style="display: block !important;">

        {{-- <h2>Vertical Headings:</h2> --}}
        @foreach ($getRecord()->project_years as $project_year)
            <div class="borde border-black">
                <p class="text-lg font-bold   p-2 border border-black" style="text-align: center">
                    {{ $project_year->year->title }}

                </p>

                <div class="border border-primary p-2" 
                {{-- style="display: flex;  flex-wrap: wrap;" --}}
                >

                    @foreach ($project_year->project_quarters as $project_quarter)
                    <div class="p-2 border border-black rounded-lg " style="margin: 4px 8px;">

                
                        <p class="font-bold mt-2">
                            {{ $project_quarter->quarter->title }}
                        </p>

                        @foreach ($project_quarter->project_divisions as $first)
                            <p class=" font-bold">
                                {{ $first->division->title }}
                            </p>
                            @foreach ($first->project_division_categories as $secondlayer)
                                <p class="text-sm font-bold mt-4 " style=" margin-left:20px !important">
                                    {{ $secondlayer->from }}
                                </p>

                                @foreach ($secondlayer->project_division_sub_category_expenses as $thirdlayer)
                                    @if ($secondlayer->from === 'Indirect Cost')
                                        <p class=" text-sm font-bold" style="margin-left:40px !important">
                                            {{ $thirdlayer->parent_title }}
                                        </p>
                                    @endif


                                    <p class="text-sm " style="margin-left:40px !important">
                                        {{ $thirdlayer->title }}
                                    </p>

                                    <table class="" " style="margin-left:60px !important"> 
                                        @foreach ($thirdlayer->fourth_layers as $fourtlayer)
                                        <tr class="border-b  ">
                                            <td>
                                                {{ $fourtlayer->title }}

                                            </td>
                                            <td>
                                                {{ number_format($fourtlayer->amount) }}

                                            </td>
 
                                        </tr>
                                        @endforeach
                                    </table>
                                @endforeach
                            @endforeach
                        @endforeach
                    </div>
                    @endforeach

                </div>
            </div>
        @endforeach
    </div>

        {{-- {{$getRecord()}} --}}
    {{-- <div class="container mx-auto ">
        <div class="border border-black p-6 text-warning rounded-md">

            @foreach ($getRecord()->project_divisions as $first)
                <p class=" font-bold">
                    {{ $first->division->title }}
                </p>
                @foreach ($first->project_division_categories as $secondlayer)
                    <p class="text-sm font-bold mt-4 " style=" margin-left:20px !important" >
                        {{ $secondlayer->from }}
                    </p>

                    @foreach ($secondlayer->project_division_sub_category_expenses as $thirdlayer)
                        @if ($secondlayer->from === 'Indirect Cost')
                         <p class=" text-sm font-bold"  style="margin-left:40px !important" >
                                {{ $thirdlayer->parent_title }}
                         </p>
                         @endif


                            <p class="text-sm " style="margin-left:40px !important" >
                                {{ $thirdlayer->title }}
                            </p>

                            @foreach ($thirdlayer->fourth_layers as $fourtlayer)
                                <p class="text-sm " style="margin-left:60px !important" >
                                    {{ $fourtlayer->title }}
                                </p>
                            @endforeach
                    @endforeach
                @endforeach


            @endforeach
        </div>

        <div>
            <div>
               --}}





</x-dynamic-component>
