<div>
    
    {{-- {{ $getRecord() }} --}}

    {{-- @foreach ($getRecord()->project_division_sub_category_expenses as $sub )
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
        
    @endforeach --}}

    <style>
        summary {
           cursor: pointer;
       }
   
        details summary::-webkit-details-marker {
           display: none;
       }
   
        details summary {
           list-style: none;
       }
   
   
   
       ul {
               list-style: none;
               line-height: 2em;
         }
   
         ul li{
           position: relative;
           /* outline:  1px solid green; */
         }
   
         ul li::before{
           position:  absolute;
           top: 0;
           left: -10px;
           border-left:  2px solid gray;
           border-bottom: 2px solid  gray;
           height: 1em;
           width: 8px;
           content: "";
           /* background-color: yellow; */
         }
         ul li::after{
           position:  absolute;
           bottom: 0;
           left: -10px;
           border-left:  2px solid gray;
           height: 100%;
           width: 8px;
           /* background-color: orange; */
           content: "";
         }
   
         ul li:last-child::after{
           display: none;
         }
         ul.tree > li:after, ul.tree > li:before {
           display: none;
         }
       </style>

    <ul class="tree">

        @foreach ($getRecord()->project_division_sub_category_expenses as $sub )
        <li>
            <details open>
                <summary>
                    {{$sub->title}}
                </summary>
                @if (!empty($sub->parent_title))    
                
                <ul>
                    <li>

                        <details open>
                            <summary>
                                {{$sub->parent_title}}
                            </summary>

                            <ul>
                                @foreach ($sub->fourth_layers as $expenses)
                                <li>
                                    <details open>
                                        <summary>
                                            {{$expenses->title}}
            
                                        </summary>
                                       
                                    </details>
                                </li>
                                @endforeach
                            </ul>
                        </details>
                        
                    </li>
                </ul>
             
              
                   
                
                @else
                <ul>
                    @foreach ($sub->fourth_layers as $expenses)
                    <li>
                        <details open>
                            <summary>
                                {{$expenses->title}}

                            </summary>
                           
                        </details>
                    </li>
                    @endforeach
                </ul>
                @endif
                
            </details>
        </li>
        @endforeach

       

       

    </ul>
</div>
