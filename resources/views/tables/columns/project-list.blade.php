<div>
    @foreach ($getRecord()->projects  as $project)
    <div class="border border p-1 text-xs">
            <p> {{$project->title}}</p>          
    </div>
        @endforeach
</div>
