<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>
    <div class="main-wrapper px-6 py-12">
        <div class="mx-auto mb-4">
            <form action="{{route('dashboard')}}" method="get" class="filter-form flex justify-between flex-wrap gap-4">
                <div class="flex gap-2 flex-wrap">
                    <select name="filter" id="filter" class="bg-white   py-2 px-4  border border-transparent flex-1">
                        <option @if(request()->input('filter') == 'all') selected @endif value="all">All Types</option>
                        <option @if(request()->input('filter') == 'deleted') selected @endif value="deleted">Deleted</option>
                        <option  @if(request()->input('filter') == 'incomplete') selected @endif value="incomplete">Incomplete</option>
                        <option  @if(request()->input('filter') == 'no_wz_code') selected @endif value="no_wz_code">No WZ Code</option>
                        <option  @if(request()->input('filter') == 'tam') selected @endif value="tam">TAM 2</option>
                        <option  @if(request()->input('filter') == 'sam') selected @endif value="sam">SAM 2</option>
                        <option  @if(request()->input('filter') == 'som') selected @endif value="som">SOM 2</option>
                        <option  @if(request()->input('filter') == 'tam_samson4') selected @endif value="tam_samson4">TAM 4</option>
                        <option  @if(request()->input('filter') == 'sam_samson4') selected @endif value="sam_samson4">SAM 4</option>
                        <option  @if(request()->input('filter') == 'som_samson4') selected @endif value="som_samson4">SOM 4</option>
                        <option  @if(request()->input('filter') == 'sam_samson4_oversized') selected @endif value="sam_samson4_oversized">SAM 4 Oversized</option>
                        <option  @if(request()->input('filter') == 'som_samson4_oversized') selected @endif value="som_samson4_oversized">SOM 4 Oversized</option>                       
                        <option  @if(request()->input('filter') == 'tam_4_diff') selected @endif value="tam_4_diff">TAM 4 - Diff</option>
                        <option  @if(request()->input('filter') == 'sam_4_diff') selected @endif value="sam_4_diff">SAM 4 - Diff</option>
                        <option  @if(request()->input('filter') == 'som_4_diff') selected @endif value="som_4_diff">SOM 4 - Diff</option>
                        <option  @if(request()->input('filter') == 'existing_client') selected @endif value="existing_client">Existing clients</option>
                    </select>
                    <select name="dream" id="dream" class="bg-white   py-2 px-4 border border-transparent flex-1 ">
                        <option value="all">All</option>
                        <option @if(request()->input('dream') == '1') selected @endif value="1">Dream</option>
                    </select>
                    <select name="flag" id="flag" class="bg-white   py-2 px-4 border border-transparent flex-1 ">
                        <option  @if(request()->input('flag') == 'all') selected @endif value="all">No Flags</option>
                        @foreach ($flags as $flag)
                            <option @if(request()->input('flag') == $flag) selected @endif value="{{$flag}}">{{$flag}}</option>
                        @endforeach
                    </select>
                    <select name="country" id="country" class="bg-white   py-2 px-4 border border-transparent flex-1">
                        <option  @if(request()->input('country') == 'all') selected @endif value="all">All Countries</option>
                        @foreach ($countries as $country)
                            <option @if(request()->input('country') == $country) selected @endif value="{{$country}}">{{$country}}</option>
                        @endforeach
                    </select>
                    <select name="revenue[]" id="revenue" class="bg-white py-2 px-4 border border-transparent flex-1" multiple>
                        <option @if(is_array(request()->input('revenue')) && in_array('0-49.99',request()->input('revenue'))) selected @endif value="0-49.99">0 - 50 million</option>
                        <option @if(is_array(request()->input('revenue')) && in_array('50-99.99',request()->input('revenue'))) selected @endif value="50-99.99">50 - 99.99 million</option>
                        <option @if(is_array(request()->input('revenue')) && in_array('100-299.99',request()->input('revenue'))) selected @endif value="100-299.99">100 - 299.99 million</option>
                        <option @if(is_array(request()->input('revenue')) && in_array('300-499.99',request()->input('revenue'))) selected @endif value="300-499.99">300 - 499.99 million</option>
                        <option @if(is_array(request()->input('revenue')) && in_array('500-999.99',request()->input('revenue'))) selected @endif value="500-999.99">500 - 999.99 million</option>
                        <option @if(is_array(request()->input('revenue')) && in_array('1000-4999.99',request()->input('revenue'))) selected @endif value="1000-4999.99">1 - 5 billion</option>
                        <option @if(is_array(request()->input('revenue')) && in_array('5000-9999.99',request()->input('revenue'))) selected @endif value="5000-9999.99">5 - 10 billion</option>
                        <option @if(is_array(request()->input('revenue')) && in_array('10000-19999.99',request()->input('revenue'))) selected @endif value="10000-19999.99">10 - 20 billion</option>
                        <option @if(is_array(request()->input('revenue')) && in_array('20000-49999.99',request()->input('revenue'))) selected @endif value="20000-49999.99">20 - 50 billion</option>
                        <option @if(is_array(request()->input('revenue')) && in_array('50000-99999.99',request()->input('revenue'))) selected @endif value="50000-99999.99">50 - 100 billion</option>
                        <option @if(is_array(request()->input('revenue')) && in_array('100000',request()->input('revenue'))) selected @endif value="100000">more than 100 billion</option>
                    </select>
                    <select name="division" id="division" class="bg-white py-2 px-4 border border-transparent flex-1">
                        <option value="">All</option>
                        <option @if(request()->input('division') == 'independent') selected @endif value="independent">Independent</option>
                        <option @if(request()->input('division') == 'dependent') selected @endif value="dependent">Dependent</option>
                    </select>
                    <button type="submit" class="btn-bg-primary text-white py-2 px-4 flex-1">Filter</button>
                </div>
            </form>            
        </div>    
        <div class="mx-auto bg-white mb-4">
            <div class="bg-light overflow-hidden shadow-sm py-6 px-6 sm:py-4 sm:px-4">
                <div class="overflow-auto data-table ">
                <table class="table-auto w-full mt-4" id="myTable">
                    <thead class="bg-light-blue">
                        <tr>
                            <th class="px-2 py-2">Name</th>
                            <th class="px-2 py-2" style="width:100px;min-width:100px;">Country</th>
                            <th class="px-2 py-2" style="width:100px;min-width:100px;">Revenue(Mio.)</th>
                            <th class="px-2 py-2" style="width:100px;min-width:100px;">WZ Code</th>
                            <th class="px-2 py-2" style="width:100px;min-width:100px;">Headcount</th>
                            <th class="px-2 py-2">Industry</th>
                            <th class="px-2 py-2" style="width:100px;min-width:100px;">General Matching Score</th>
                            <th class="px-2 py-2" style="width:100px;min-width:100px;">Industry Similarity Score</th>
                            <th class="px-2 py-2" style="width:100px;min-width:100px;">Revenue Similarity Score</th>
                            <th class="px-2 py-2" style="width:100px;min-width:100px;">Activity Level Score</th>
                            <th class="px-2 py-2" style="width:100px;min-width:100px;">Network Overlap Score</th>
                            <th class="px-2 py-2" style="width:100px;min-width:100px;">Country Matched</th>
                        </tr>
                    </thead>
                </table>
                </div>
            </div>
        </div>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#myTable').DataTable({
                ordering: false,
                ajax: '<?=$pageUrl?>',
                processing: true,
                serverSide: true,
                columns:[
                    {data: "name"},
                    {data: "country"},
                    {data: "revenue"},
                    {data: "wz_code"},
                    {data: "headcount"},
                    {data: "industry"},
                    {data: "general_matching_score"},
                    {data: "industry_similarity_score"},
                    {data: "revenue_similarity_score"},
                    {data: "activity_level_score"},
                    {data: "network_overlap_score"},
                    {data: "location_matched"}
                ],
                lengthMenu: [[10,25,50,100],[10,25,50,100]],
                pageLength: 50
            });
            $('#revenue').select2({width:'380px',placeholder: "Select revenue range",multiple: true});
        });
    </script>
    <style>
        .filter-form select{min-width:120px;}
        .txt-mother{background:#E3AFBD;width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;border-radius:50%;color:#72384C;line-height:1;margin-right:6px;}
        .txt-daughter{background:#B3B8DD;width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;border-radius:50%;color:#3E466C;line-height:1;margin-right:6px;}
        .select2-container .select2-selection--multiple{min-height:40px;border:none;background:#FFF;}
        .select2-container--default .select2-selection--multiple .select2-selection__choice{font-size:12px;line-height:1.5;}
    </style>
</x-app-layout>