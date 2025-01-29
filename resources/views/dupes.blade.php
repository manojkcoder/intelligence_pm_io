<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Duplicates') }}</h2>
    </x-slot>
    <div class="main-wrapper px-6 py-12">
        <div class="mx-auto mb-4">
            <form action="{{route('list_duplicates')}}" method="get" class="filter-form flex justify-between flex-wrap gap-4">
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
                        <option  @if(request()->input('filter') == 'sam_4_diff') selected @endif value="sam_4_diff">SAM 4 - Diff</option>
                        <option  @if(request()->input('filter') == 'som_4_diff') selected @endif value="som_4_diff">SOM 4 - Diff</option>
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
                    <select name="revenue" id="revenue" class="bg-white py-2 px-4 border border-transparent flex-1">
                        <option value="">Revenue</option>
                        <option @if(request()->input('revenue') == '0-49.99') selected @endif value="0-49.99">0 - 50 million</option>
                        <option @if(request()->input('revenue') == '50-99.99') selected @endif value="50-99.99">50 - 99.99 million</option>
                        <option @if(request()->input('revenue') == '100-299.99') selected @endif value="100-299.99">100 - 299.99 million</option>
                        <option @if(request()->input('revenue') == '300-499.99') selected @endif value="300-499.99">300 - 499.99 million</option>
                        <option @if(request()->input('revenue') == '500-999.99') selected @endif value="500-999.99">500 - 999.99 million</option>
                        <option @if(request()->input('revenue') == '1000-4999.99') selected @endif value="1000-4999.99">1 - 5 billion</option>
                        <option @if(request()->input('revenue') == '5000-9999.99') selected @endif value="5000-9999.99">5 - 10 billion</option>
                        <option @if(request()->input('revenue') == '10000-19999.99') selected @endif value="10000-19999.99">10 - 20 billion</option>
                        <option @if(request()->input('revenue') == '20000-49999.99') selected @endif value="20000-49999.99">20 - 50 billion</option>
                        <option @if(request()->input('revenue') == '50000-99999.99') selected @endif value="50000-99999.99">50 - 100 billion</option>
                        <option @if(request()->input('revenue') == '100000') selected @endif value="100000">more than 100 billion</option>
                    </select>
                    <button type="submit" class="btn-bg-primary text-white py-2 px-4 flex-1">Filter</button>
                </div>
                <a href="{{$pageUrl . (strpos($pageUrl, '?') !== false ? '&' : '?')}}export" class="btn-bg-secondary text-white py-2 px-4">Export</a>
            </form>            
        </div>
        <div class="mx-auto bg-white mb-4">
            <div class="bg-light overflow-hidden shadow-sm py-6 px-6 sm:py-4 sm:px-4">
                <div class="overflow-auto data-table ">
                    <table class="table-auto w-full mt-4" id="myTable">
                        <thead class="bg-light-blue">
                            <tr>
                                <th class="px-2 py-2">ID</th>
                                <th class="px-2 py-2">Name</th>
                                <th class="px-2 py-2">Legal Name</th>
                                <th class="px-2 py-2">Domain</th>
                                <th class="px-2 py-2">Country</th>
                                <th class="px-2 py-2">Revenue in Mio.</th>
                                <th class="px-2 py-2">Headcount</th>
                                <th class="px-2 py-2">Actions</th>
                            </tr>
                        </thead>
                    </table>
                    <script>
                        $(document).ready(function(){
                            $('#myTable').DataTable({
                                ordering: false,
                                ajax: '<?=$pageUrl?>',
                                processing: true,
                                serverSide: true,
                                columns:[
                                    {data: "id"},
                                    {data: "name"},
                                    {data: "legal_name"},
                                    {data: "domain"},
                                    {data: "country"},
                                    {data: "revenue"},
                                    {data: "headcount"},
                                    {data: "actions"}
                                ],
                                lengthMenu: [[10,25,50,100],[10,25,50,100]],
                                pageLength: 50
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>