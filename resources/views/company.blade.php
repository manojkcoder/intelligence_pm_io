<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Account') }}</h2>
    </x-slot>
    <div class="py-12 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="tabs-wrapper flex text-gray-500 border-b border-gray-200 text-base">
                <div class="tab-item p-4 border-b-2 border-transparent active" data-id="companyProfile">Company</div>
                @if($company->parent_id)
                    <div class="tab-item p-4 border-b-2 border-transparent" data-id="motherCompany">Mother Company</div>
                @endif
                @if($company->quiz->count() > 0)
                    <div class="tab-item p-4 border-b-2 border-transparent" data-id="companyQA">QA Response</div>
                @endif
                @if($client && (count($uniqueToClients) || count($uniqueToCompanies) || count($commonConnections)))
                    <div class="tab-item p-4 border-b-2 border-transparent" data-id="veenDiagrams">Venn Diagram</div>
                @endif
            </div>
            <div id="companyProfile" class="tab-content bg-white overflow-hidden shadow-sm rounded p-6 active">
                <div class="flex items-center gap-2 mb-2">
                    <a href="{{ route('editCompany',$company->id) }}" class="btn-bg-primary text-white py-2 px-4">Edit</a>
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
                </div>
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
                                <tr>
                                    <td class="px-4 py-2">Existing Client</td>
                                    <td class="px-4 py-2">
                                        <input type="checkbox" value="{{ $company->id }}" class="existing-client" {{(($company->existing_client == 1) ? 'checked' : '')}}/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">General Matching Score</td>
                                    <td class="px-4 py-2">{{ $company->general_matching_score }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Industry Similarity Score</td>
                                    <td class="px-4 py-2">{{ $company->industry_similarity_score }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Revenue Similarity Score</td>
                                    <td class="px-4 py-2">{{ $company->revenue_similarity_score }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Activity Level Score</td>
                                    <td class="px-4 py-2">{{ $company->activity_level_score }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Location Matched</td>
                                    <td class="px-4 py-2">{{ $company->location_matched }}</td>
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
            @if($company->parent_id)
                <div id="motherCompany" class="tab-content bg-white overflow-hidden shadow-sm rounded p-6">
                    <div class="flex items-center gap-2 mb-2">
                        <a href="{{ route('viewCompany',$company->parent_id) }}" class="btn-bg-primary text-white py-2 px-4">View</a>
                        <a href="{{ route('editCompany',$company->parent_id) }}" class="btn-bg-primary text-white py-2 px-4">Edit</a>
                    </div>
                    <div class="flex">
                        <div class="table-responsive flex-1 overflow-auto data-table top-table">
                            <table class="table-auto dataTable">
                                <tbody>
                                    <tr>
                                        <td class="px-4 py-2">Name:</td>
                                        <td class="px-4 py-2">{{ $company->parent->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2">Domain:</td>
                                        <td class="px-4 py-2">{{ $company->parent->domain }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2">Country:</td>
                                        <td class="px-4 py-2">{{ $company->parent->country }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2">Revenue in Mio. Euro:</td>
                                        <td class="px-4 py-2">{{ $company->parent->revenue }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2">WZ Code:</td>
                                        <td class="px-4 py-2">{{ $company->parent->wz_code }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2">Headcount:</td>
                                        <td class="px-4 py-2">{{ $company->parent->headcount }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2">HS ID:</td>
                                        <td class="px-4 py-2">
                                            @if($company->parent->hubspot_id)
                                                <a href="https://app.hubspot.com/contacts/26548368/company/{{ $company->parent->hubspot_id }}" target="_blank">{{ $company->parent->hubspot_id }}</a>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            @if($company->quiz->count() > 0)
                <div id="companyQA" class="tab-content bg-white overflow-hidden shadow-sm rounded p-6">
                    <div class="flex flex-col">
                        <div class="flex justify-end">
                            <button id="editButton" class="btn-bg-primary text-white py-2 px-4">Edit</button>
                        </div>
                        <div class="flex flex-col gap-4 mt-4" id="quizList">
                            @foreach($company->quiz as $quiz)
                                <div class="flex flex-col">
                                    <span class="font-semibold">Q) {{ $quiz->question_name }}</span>
                                    <span class="space-pre-wrap"><b>Ans:</b> {{ ($quiz->answer == 'yes' ? "Yes" : ($quiz->answer == 'no' ? "No" : $quiz->answer)) }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div id="editForm" class="hidden mt-4">
                            <form action="{{ route('quiz.update') }}" method="POST">
                                <input type="hidden" name="company_id" value="{{ $company->id }}"/>
                                @csrf
                                @foreach($company->quiz as $quiz)
                                    <div class="flex flex-col mb-4">
                                        <label for="answers-{{ $quiz->question_id }}" class="font-semibold mb-2">Q) {{ $quiz->question_name }}</label>
                                        @if($quiz->question_id == 1 || $quiz->question_id == 2 || $quiz->question_id == 3 || $quiz->question_id == 4 || $quiz->question_id == 15)
                                            <select name="answers[{{ $quiz->question_id }}]" id="answers-{{ $quiz->question_id }}" class="bg-white py-2 px-4 border flex-1">
                                                <option value="yes" {{ $quiz->answer == 'yes' ? 'selected' : '' }}>Yes</option>
                                                <option value="no" {{ $quiz->answer == 'no' ? 'selected' : '' }}>No</option>
                                            </select>
                                        @else
                                            <textarea name="answers[{{ $quiz->question_id }}]" id="answers-{{ $quiz->question_id }}" class="bg-white py-2 px-4 border flex-1">{{ $quiz->answer }}</textarea>
                                        @endif
                                    </div>
                                @endforeach
                                <div class="flex justify-end gap-4 mt-4">
                                    <button type="button" id="cancelButton" class="btn-bg-primary text-white py-2 px-4">Cancel</button>
                                    <button type="submit" class="btn-bg-primary text-white py-2 px-4">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            @if($client && (count($uniqueToClients) || count($uniqueToCompanies) || count($commonConnections)))
                <div id="veenDiagrams" class="tab-content bg-white overflow-hidden shadow-sm rounded p-6">
                    <div class="flex flex-col">
                        <div id="chart-container" class="mb-4 align-center">
                            <canvas id="vennChart"></canvas>
                        </div>
                        <div class="table-responsive flex-1 overflow-auto data-table top-table">
                            
                        </div>
                    </div>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"></script>
                <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-venn@3.1.0"></script>
                <script type="text/javascript">
                    document.addEventListener("DOMContentLoaded",function(){
                        const clientName = "<?=$client->name;?>";
                        const companyName = "<?=$company->name;?>";
                        const uniqueToClients = Number(<?=json_encode(count($uniqueToClients) ?? 0);?>);
                        const uniqueToCompanies = Number(<?=json_encode(count($uniqueToCompanies) ?? 0);?>);
                        const commonConnections = Number(<?=json_encode(count($commonConnections) ?? 0);?>);
                        var ctx = document.getElementById('vennChart').getContext('2d');
                        var vennChart = new Chart(ctx, {
                            type: 'venn',
                            data: {
                                labels: [clientName,companyName,'Common'],
                                datasets: [{
                                    label: 'Connections',
                                    data: [{label: clientName,value: uniqueToClients},{label: companyName,value: uniqueToCompanies},{label: "Common",value: commonConnections}],
                                    backgroundColor: ['rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(75, 192, 192, 0.5)']
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(tooltipItem){
                                                return `Connections: ${tooltipItem.raw.value}`;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    });
                </script>
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
            $(document).on('change','.existing-client',function(){
                var id = $(this).val();
                var checked = $(this).prop('checked');
                $.ajax({
                    url: '/existing-client/' + id,
                    type: 'POST',
                    data: {_token: '{{csrf_token()}}',checked: checked},
                    success: function(data){
                        console.log(data);
                    }
                });
            });
            document.getElementById('editButton').addEventListener('click',function(){
                document.getElementById('editForm').classList.remove('hidden');
                document.getElementById('quizList').classList.add('hidden');
                document.getElementById('editButton').classList.add('hidden');
            });
            document.getElementById('cancelButton').addEventListener('click',function(){
                document.getElementById('editButton').classList.remove('hidden');
                document.getElementById('quizList').classList.remove('hidden');
                document.getElementById('editForm').classList.add('hidden');
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
        .tab-content form input,
        .tab-content form select,
        .tab-content form textarea{border:1px solid #e2e8f0;}
        .space-pre-wrap{white-space:pre-wrap;}
        #chart-container{width:100%;max-width:500px;max-height:400px;margin:auto;}
        canvas{width:100% !important;max-height:400px !important;}
        @media(max-width:1024px){
            .data-table.top-table table tbody tr td:first-child{position:static;font-weight:600;}
            .data-table.top-table table tbody tr{gap: 5px;}
            .data-table.top-table table tbody tr td{padding:0 !important;}
        }
    </style>
</x-app-layout>
