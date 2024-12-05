<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Stats') }}</h2>
    </x-slot>
    <div class="main-wrapper px-6 py-12">
        <div class="mx-auto bg-white mt-6">
            <div class="bg-light overflow-hidden shadow-sm py-6 px-6 sm:py-4 sm:px-4">
                <div class="overflow-auto data-table ">
                    <table class="table-auto w-full mt-4" id="statsTable">
                        <thead class="bg-light-blue">
                            <tr>
                                <th class="px-2 py-2">Country</th>
                                <th class="px-2 py-2">#TAM</th>
                                <th class="px-2 py-2">#SAM</th>
                                <th class="px-2 py-2">#SOM</th>
                                <th class="px-2 py-2">Incomplete Likes</th>
                                <th class="px-2 py-2">Incomplete Comments</th>
                                <th class="px-2 py-2">TAM Likes</th>
                                <th class="px-2 py-2">TAM Comments</th>
                                <th class="px-2 py-2">SAM Likes</th>
                                <th class="px-2 py-2">SAM Comments</th>
                                <th class="px-2 py-2">SOM Likes</th>
                                <th class="px-2 py-2">SOM Comments</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="overflow-auto data-table d-none ">
                    <table class="table-auto w-full mt-4" id="detailTable">
                        <thead class="bg-light-blue">
                            <tr>
                                <th class="px-2 py-2">Name</th>
                                <th class="px-2 py-2">Company</th>
                                <th class="px-2 py-2">LinkedIn</th>
                                <th class="px-2 py-2">Approach</th>
                                <th class="px-2 py-2">Target Category</th>
                                <th class="px-2 py-2">LinkedIn Hub URL</th>
                                <th class="px-2 py-2">Country</th>
                                <th class="px-2 py-2">Profile Link</th>
                                <th class="px-2 py-2">Comment/Like</th>
                                <th class="px-2 py-2">Post URL</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function(){
            $('#statsTable').DataTable({
                searching: false,
                ordering: false,
                bLengthChange: false,
                paging: false,
                ajax: '<?=$statsUrl?>',
                processing: true,
                serverSide: true,
                columns:[
                    {data: "country"},
                    {data: "tamCompanies"},
                    {data: "samCompanies"},
                    {data: "somCompanies"},
                    {data: "incompleteLikes"},
                    {data: "incompleteComments"},
                    {data: "tamLikes"},
                    {data: "tamComments"},
                    {data: "samLikes"},
                    {data: "samComments"},
                    {data: "somLikes"},
                    {data: "somComments"}
                ],
                "columnDefs": [
                    {
                        "targets": [6],
                        "render": function ( data, type, row ) {
                            return '<a href="javascript:void(0)" data-metric="tamLikes" class="detail-link text-blue-500">'+data+'</a>';
                        }
                    },
                    {
                        "targets": [7],
                        "render": function ( data, type, row ) {
                            return '<a href="javascript:void(0)" data-metric="tamComments" class="detail-link text-blue-500">'+data+'</a>';
                        }
                    },
                    {
                        "targets": [8],
                        "render": function ( data, type, row ) {
                            return '<a href="javascript:void(0)" data-metric="samLikes" class="detail-link text-blue-500">'+data+'</a>';
                        }
                    },
                    {
                        "targets": [9],
                        "render": function ( data, type, row ) {
                            return '<a href="javascript:void(0)" data-metric="samComments" class="detail-link text-blue-500">'+data+'</a>';
                        }
                    },
                    {
                        "targets": [10],
                        "render": function ( data, type, row ) {
                            return '<a href="javascript:void(0)" data-metric="somLikes" class="detail-link text-blue-500">'+data+'</a>';
                        }
                    },
                    {
                        "targets": [11],
                        "render": function ( data, type, row ) {
                            return '<a href="javascript:void(0)" data-metric="somComments" class="detail-link text-blue-500">'+data+'</a>';
                        }
                    }
                ]
            });
        });
        $(document).on('click', '.detail-link', function(){
            var metric = $(this).data('metric');
            var country = $(this).closest('tr').find('td:first').text();
            $.ajax({
                url: '<?=$statsUrl?>?detail=1',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {metric: metric, country: country},
                success: function(response){
                    var data = response;
                    var table = $('#detailTable');
                    table.find('tbody').empty();
                    $.each(data, function(index, item){
                        var tr = $('<tr>');
                        tr.append('<td>'+item.contact.first_name+' '+item.contact.last_name+'</td>');
                        if(item.contact.company){
                            tr.append('<td>'+item.contact.company.name+'</td>');
                        }else{
                            tr.append('<td></td>');
                        }
                        if(item.contact.linkedin){
                            tr.append('<td><a href="'+item.contact.linkedin+'" target="_blank">LinkedIn</a></td>');
                        }else{
                            tr.append('<td></td>');
                        }
                        tr.append('<td>'+item.contact.approach+'</td>');
                        tr.append('<td>'+item.contact.target_category+'</td>');
                        if(item.contact.linkedin_hub_url){
                            tr.append('<td><a href="'+item.contact.linkedin_hub_url+'" target="_blank">LinkedIn Hub URL</a></td>');
                        }else{
                            tr.append('<td></td>');
                        }
                        tr.append('<td>'+item.contact.country+'</td>');
                        if(item.contact.profile_link){
                            tr.append('<td><a href="'+item.contact.profile_link+'" target="_blank">Profile Link</a></td>');
                        }else{
                            tr.append('<td></td>');
                        }
                        tr.append('<td>'+item.comment+'</td>');
                        if(item.post_url){
                            tr.append('<td><a href="'+item.post_url+'" target="_blank">Post URL</a></td>');
                        }else{
                            tr.append('<td></td>');
                        }
                        table.find('tbody').append(tr);
                    });
                }
            });
        });
    </script>
</x-app-layout>