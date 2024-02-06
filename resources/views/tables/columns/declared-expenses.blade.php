<div>
    {{-- {{ $getRecord() }} --}}

    @foreach ($getRecord()->project_division_sub_category_expenses as $sub )
    @if (!empty($sub->parent_title))
    <p class="font-bold" >

        {{$sub->parent_title}}
            
    </p>
    @endif
    <p style="margin-left: 10px; " class="font-medium text-sm">
        {{$sub->title}}
    </p>

    @foreach ($sub->fourth_layers as $expenses)
    <p style="margin-left: 20px;" class="text-xs">
        {{$expenses->title}}

    </p>
    @endforeach
        
    @endforeach
</div>
