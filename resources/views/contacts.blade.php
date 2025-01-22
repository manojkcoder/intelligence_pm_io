<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contacts') }}
        </h2>
    </x-slot>
    <div class="contacts main-wrapper px-6 py-12">
        <div class="mx-auto">
            <div class="mx-auto mb-4">
                <form action="{{route('contacts.all')}}" method="get" class="filter-form flex justify-between flex-wrap">
                    <div class="flex  gap-2 flex-wrap flex-1">
                        <select name="type" id="type" class=" py-2 px-4 border border-transparent flex-1 ">
                            <option  @if(request()->input('type') == 'all') selected @endif value="all">All</option>
                            <option  @if(request()->input('type') == 'likes') selected @endif value="likes">Likes</option>
                            <option  @if(request()->input('type') == 'comments') selected @endif value="comments">Comments</option>
                            <option  @if(request()->input('type') == 'comments') selected @endif value="comments">Both</option>
                        </select>
                        <select name="filter" id="filter" class="py-2 px-4 border border-transparent flex-1">
                            <option @if(request()->input('filter') == 'all') selected @endif value="all">All Types</option>
                            <option  @if(request()->input('filter') == 'tam') selected @endif value="tam">TAM</option>
                            <option  @if(request()->input('filter') == 'sam') selected @endif value="sam">SAM</option>
                            <option  @if(request()->input('filter') == 'som') selected @endif value="som">SOM</option>
                        </select>
                        <select name="dream" id="dream" class="py-2 px-4 border border-transparent flex-1">
                            <option value="all">All</option>
                            <option @if(request()->input('dream') == '1') selected @endif value="1">Dream</option>
                        </select>
                        <select name="flag" id="flag" class="py-2 px-4 border border-transparent flex-1">
                            <option  @if(request()->input('flag') == 'all') selected @endif value="all">No Flags</option>
                            @foreach ($flags as $flag)
                                <option @if(request()->input('flag') == $flag) selected @endif value="{{$flag}}">{{$flag}}</option>
                            @endforeach
                        </select>
                        <select name="country" id="country" class="py-2 px-4 border border-transparent flex-1">
                            <option  @if(request()->input('country') == 'all') selected @endif value="all">All Countries</option>
                            @foreach ($countries as $country)
                                <option @if(request()->input('country') == $country) selected @endif value="{{$country}}">{{$country}}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-bg-primary text-white py-2 px-4 flex-1">Filter</button>
                    </div>
                </form>
            </div>
            <div class="bg-white overflow-hidden shadow-sm  py-4 px-4">
                <div class="overflow-auto data-table">
                    <table class="table-auto w-full" id="myTable">
                        <thead class="bg-light-blue">
                            <tr>
                                <th>Approach</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Linkedin</th>
                                <th>Company</th>
                                <th>Position</th>
                                <th>Location</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Activity Rate</th>
                                <th>Email Domain</th>
                                <th>Target Category</th>
                                <th>Linkedin Hub URL</th>
                                <th>Likes</th>
                                <th>Comments</th>
                                <th>Country</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <style>
        .contacts select{min-width:120px;}
        @media(max-width:1024px){
            .data-table table tbody tr td:first-child{position:static;}
            .data-table table tbody tr td{padding-left:0;}
        }
    </style>    
     <script>
        $(document).ready(function(){
            $('#myTable').DataTable({
                ordering: false,
                ajax: '<?=$pageUrl?>',
                processing: true,
                serverSide: true,
                columns:[
                    {data: 'approach', name: 'approach'},
                    {data: 'first_name', name: 'first_name'},
                    {data: 'last_name', name: 'last_name'},
                    {data: 'linkedin', name: 'linkedin'},
                    {data: 'company', name: 'company'},
                    {data: 'position', name: 'Position'},
                    {data: 'location', name: 'Location'},
                    {data: 'age', name: 'Age'},
                    {data: 'gender', name: 'Gender'},
                    {data: 'activity_rate', name: 'Activity Rate'},
                    {data: 'email_domain', name: 'email_domain'},
                    {data: 'target_category', name: 'target_category'},
                    {data: 'linkedin_hub_url', name: 'linkedin_hub_url'},
                    {data: 'likes_count', name: 'likes_count'},
                    {data: 'comments_count', name: 'comments_count'},
                    {data: 'country', name: 'country'},
                    {data: 'actions', name: 'Action'}
                ],
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                pageLength: 50,
                columnDefs: [
                    {targets: [4],render: function(data,type,row){
                        return data ? data.name : '-';
                    }},
                    {targets: [8],render: function(data,type,row){
                        return data ? capitalizeFirstLetter(data) : '';
                    }}
                ]
            });
            function capitalizeFirstLetter(string){
                return string.charAt(0).toUpperCase() + string.slice(1);
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</x-app-layout>