<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
      {{$getRecord()}}
    </div>
</x-dynamic-component>
