<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="table w-full">
        <table class="border border-black p-2 w-full">
            <tr>
                <td class="border border-black p-2 text-sm"> File Name</td>
                <td class="border border-black p-2 text-sm"> File Type</td>
                <td class="border border-black p-2 text-sm"> File Size</td>
                <td class="border border-black p-2"> </td>
            </tr>
            @foreach ($getRecord()->files as $file)
            <tr>
                <td class="border border-black p-2 text-xs">{{ $file->file_name }}</td>
                <td class="border border-black p-2 text-xs">{{ $file->file_type }}</td>
                <td class="border border-black p-2 text-xs">{{ $file->file_size }}</td>
                <td class="border border-black p-2 text-xs ">
                    <a href="{{Storage::disk('public')->url($file->file)}}" target="_blank" class="font-medium" style="color: #2d68e6 !important" class="">
                        <div  style=" display: flex; justify-content: center; align-items: center">

                            <div class="mr-2 " style="margin-right: 8px">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>

                            </div>
                            <p>

                                Dowload
                            </p>
                        </div>
                        </a>
                </td>
            </tr>
                @endforeach
        </table>
        {{-- {{ $getRecord() }} --}}
    </div>
</x-dynamic-component>
