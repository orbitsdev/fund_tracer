<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    
    <div style="display: block !important;">   

        {{-- <h2>Vertical Headings:</h2> --}}

        <table style="width:100%" class="border border-primary-600" border="2">
          <tr>
            <th>Name:</th>
            <td>Bill Gates</td>
          </tr>
          <tr>
            <th>Telephone:</th>
            <td>555 77 854</td>
          </tr>
          <tr>
            <th>Telephone:</th>
            <td>555 77 855</td>
          </tr>
        </table>
    {{$getRecord()}}
    </div>
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
