<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Account') }}</h2>
    </x-slot>
    <div class="py-12 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="tabs-wrapper flex text-gray-500 border-b border-gray-200 text-base">
                <div class="tab-item p-4 border-b-2 border-transparent active" data-id="companyProfile">Company</div>
                @if($company->quiz->count() > 0)
                    <div class="tab-item p-4 border-b-2 border-transparent" data-id="companyQA">QA Response</div>
                @endif
            </div>
            <div id="companyProfile" class="tab-content bg-white overflow-hidden shadow-sm rounded p-6 active">
                @if($company->deleted_at)
                    <div class="">
                        <form action="{{ route('deleteCompany', $company->id) }}" method="POST" class="inline">
                            @method('DELETE')
                            @csrf
                            <button type="submit" class="btn-bg-primary text-white  py-2 px-4">Restore</button>
                        </form>
                    </div>
                @else
                    <div class="">
                        <form action="{{ route('deleteCompany', $company->id) }}" method="POST" class="inline">
                            @method('DELETE')
                            @csrf
                            <button type="submit" class="btn-bg-primary text-white  py-2 px-4">Delete</button>
                        </form>
                    </div>
                @endif
                <div class="flex">
                    <div class="table-responsive flex-1 overflow-auto data-table top-table">
                        <table class="table-auto dataTable">
                            <tbody>
                                <tr>
                                    <td class="px-4 py-2">Name:</td>
                                    <td class="px-4 py-2">{{ $company->name }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Domain:</td>
                                    <td class="px-4 py-2">{{ $company->domain }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Country:</td>
                                    <td class="px-4 py-2">{{ $company->country }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Revenue in Mio. Euro:</td>
                                    <td class="px-4 py-2">{{ $company->revenue }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">WZ Code:</td>
                                    <td class="px-4 py-2">{{ $company->wz_code }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Headcount:</td>
                                    <td class="px-4 py-2">{{ $company->headcount }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">HS ID:</td>
                                    <td class="px-4 py-2">
                                        @if($company->hubspot_id)
                                            <a href="https://app.hubspot.com/contacts/26548368/company/{{ $company->hubspot_id }}" target="_blank">{{ $company->hubspot_id }}</a>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="flex justify-between mb-4 items-center mt-6">
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Contacts</h2>
                            <a href="{{ route('createContact', $company->id) }}" class="btn-bg-primary text-white  py-2 px-4 ">Add Contact</a>
                        </div>
                        <table class="table-auto w-full" id="myTable">
                            <thead class="bg-light-blue">
                                <tr>
                                    <th class="px-4 py-2">First Name</th>
                                    <th class="px-4 py-2">Last Name</th>
                                    <th class="px-4 py-2">Email</th>
                                    <th class="px-4 py-2">Phone</th>
                                    <th class="px-4 py-2">Position</th>
                                    <th class="px-4 py-2">Location</th>
                                    <th class="px-4 py-2">Target Category</th>
                                    <th class="px-4 py-2">Age</th>
                                    <th class="px-4 py-2">Gender</th>
                                    <th class="px-4 py-2">LinkedIn</th>
                                    <th class="px-4 py-2">URL</th>
                                    <th class="px-4 py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($company->contacts as $contact)
                                    <tr>
                                        <td data-title="First Name" class="px-4 py-2">{{ $contact->first_name }}</td>
                                        <td data-title="Last Name" class="px-4 py-2">{{ $contact->last_name }}</td>
                                        <td data-title="Email" class="px-4 py-2">{{ $contact->email }}</td>
                                        <td data-title="Phone" class="px-4 py-2">{{ $contact->phone }}</td>
                                        <td data-title="Position" class="px-4 py-2">{{ $contact->position }}</td>
                                        <td data-title="Location" class="px-4 py-2">{{ $contact->location }}</td>
                                        <td data-title="Target Category" class="px-4 py-2">{{ $contact->target_category }}</td>
                                        <td data-title="Age" class="px-4 py-2">{{ $contact->age }}</td>
                                        <td data-title="Gender" class="px-4 py-2">{{ $contact->gender }}</td>
                                        <td data-title="LinkedIn" class="px-4 py-2">{{ $contact->linkedin }}</td>
                                        <td data-title="URL" class="px-4 py-2">{{ $contact->url }}</td>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('editContact', ['id' => $company->id, 'contact_id' => $contact->id]) }}" class="btn-bg-primary text-white  py-2 px-4">Edit</a>
                                            <form action="{{ route('deleteContact', ['id' => $company->id, 'contact_id' => $contact->id]) }}" method="POST" class="inline">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn-bg-primary text-white  py-2 px-4">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                        </table>
                    </div>
                </div>
            </div>
            @if($company->quiz->count() > 0)
                <div id="companyQA" class="tab-content bg-white overflow-hidden shadow-sm rounded p-6">
                    <div class="flex flex-col items-center">
                        <div class="flex flex-col gap-4 mt-4">
                            @foreach($company->quiz as $quiz)
                                <div class="flex flex-col">
                                    <span class="font-semibold">Q) {{ $quiz->question_name }}</span>
                                    <span>Ans: {{ ($quiz->answer == 'yes' ? "Yes" : ($quiz->answer == 'no' ? "No" : $quiz->answer)) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $('.tab-item').click(function(){
                $('.tab-item').removeClass('active');
                $(this).addClass('active');
                $('.tab-content').removeClass('active');
                $('#'+$(this).data('id')).addClass('active');
            });
        });
    </script>
    <style>
        .tabs-wrapper{display:flex;gap:10px;}
        .tabs-wrapper .tab-item{cursor:pointer;}
        .tabs-wrapper .tab-item.active{border-color:#2563EB;color:#2563EB;}
        .tab-content{display:none;}
        .tab-content.active{display:block;}
        .text-left{text-align:left;}
        @media(max-width:1024px){
            .data-table.top-table table tbody tr td:first-child{position:static;font-weight:600;}
            .data-table.top-table table tbody tr{gap: 5px;}
            .data-table.top-table table tbody tr td{padding:0 !important;}
        }
    </style>
</x-app-layout>
