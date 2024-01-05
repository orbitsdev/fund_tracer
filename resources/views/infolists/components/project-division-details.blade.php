<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">

    <div class="container mx-auto ">
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


                {{-- <div class="mb-4 ">




            </div>

            <div >
                @foreach ($first->project_division_categories as $secondlayer)

               <div >
                   {{$secondlayer->from}}
                        @foreach ($secondlayer->project_division_sub_category_expenses as $thirdlayer)
                        <br>
                        @if ($secondlayer->from === 'Indirect Cost')
                                 {{$thirdlayer->title}}

                                 @foreach ($thirdlayer->fourth_layers as $fourtlayer)
                                 <br>
                                 {{$fourtlayer->title}}
                                 @endforeach

                        @endif


                        @endforeach
               </div>
                @endforeach
            </div> --}}
            @endforeach
        </div>

        <div>
            <div>
                {{-- <div >




    @foreach ($getRecord()->project_divisions as $first)
    <div class="mb-4 ">
        {{$first->division->title}}




    </div>

    <div >
        @foreach ($first->project_division_categories as $secondlayer)

       <div >
           {{$secondlayer->from}}
                @foreach ($secondlayer->project_division_sub_category_expenses as $thirdlayer)
                <br>
                @if ($secondlayer->from === 'Indirect Cost')
                         {{$thirdlayer->title}}

                         @foreach ($thirdlayer->fourth_layers as $fourtlayer)
                         <br>
                         {{$fourtlayer->title}}
                         @endforeach

                @endif


                @endforeach
       </div>
        @endforeach
    </div>
    @endforeach
    </div> --}}


</x-dynamic-component>
