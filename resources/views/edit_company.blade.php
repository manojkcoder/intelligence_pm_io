<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Account') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm p-8">
                <form method="POST" action="{{ route('updateCompany', $company->id) }}">
                    @method('PATCH')
                    @csrf
                    <div class="flex flex-wrap gap-y-4">
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="parent_id">Mother Company</label>
                            <select name="parent_id" id="parent_id" class="block mt-1 w-full">
                                <option value="">Select</option>
                                @if(count($allCompanies) > 0)
                                    @foreach($allCompanies as $allCompany)
                                        <option @if($company->parent_id == $allCompany->id) selected @endif value="{{ $allCompany->id }}">{{ $allCompany->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="child_companies">Daughter Companies</label>
                            <select name="child_companies[]" id="child_companies" class="block mt-1 w-full" multiple>
                                <option value="">Select</option>
                                @if(count($childCompanies) > 0)
                                    @foreach($childCompanies as $childCompany)
                                        <option selected value="{{ $childCompany->id }}">{{ $childCompany->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="name">Name</label>
                            <input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ $company->name }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="name">Legal Name</label>
                            <input id="name" class="block mt-1 w-full" type="text" name="legal_name" value="{{ $company->legal_name }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="domain">Domain</label>
                            <input id="domain" class="block mt-1 w-full" type="text" name="domain" value="{{ $company->domain }}" required />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="country">Country</label>
                            <select name="country" id="country" class="block mt-1 w-full" required>
                                <option value="">Select</option>
                                @foreach($countries as $country)
                                    <option @if($company->country == $country) selected @endif value="{{$country}}">{{$country}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="revenue">Revenue in Mio. Euro</label>
                            <input id="revenue" class="block mt-1 w-full" type="text" name="revenue" value="{{ $company->revenue }}" />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="wz_code">WZ Code</label>
                            <input id="wz_code" class="block mt-1 w-full" type="text" name="wz_code" value="{{ $company->wz_code }}" />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="headcount">Headcount</label>
                            <input id="headcount" class="block mt-1 w-full" type="text" name="headcount" value="{{ $company->headcount }}" />
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <button class="ml-4  btn-bg-primary text-white  py-2 px-4">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <style>
        @media(max-width:767px){
            form .flex div{width:100%;padding:0;}
            .ml-4.btn-bg-primary{width:100%;margin:0;}
        }
        .select2-container .select2-selection--single{height:38px;margin-top:.25rem;}
        .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:38px;}
        .select2-container--default .select2-selection--single .select2-selection__arrow{height:38px;top:4px;}
        .select2-container .select2-selection--multiple{margin-top:.25rem;}
        .select2-container--default .select2-selection--multiple .select2-selection__choice{font-size:12px;}
    </style>
    <script>
        $(document).ready(function(){
            $('#parent_id').select2({
                ajax: {
                    url: '{{ route("companies.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params){
                        return {q: params.term,company_id: '{{ $company->id }}'};
                    },
                    processResults: function(data){
                        return {
                            results: data.map(function(company){
                                return {id: company.id,text: company.name}
                            })
                        }
                    },
                    cache: true
                },
                minimumInputLength: 2,
                width: '100%',
                placeholder: "Select Mother Company",
                allowClear: true
            });
            var existingParentId = '{{ $company->parent_id }}';
            if(existingParentId){
                $('#parent_id').val(existingParentId).trigger('change');
            }
            $('#child_companies').select2({
                ajax: {
                    url: '{{ route("companies.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params){
                        return {q: params.term,company_id: '{{ $company->id }}'};
                    },
                    processResults: function(data){
                        return {
                            results: data.map(function(company){
                                return {id: company.id,text: company.name}
                            })
                        }
                    },
                    cache: true
                },
                minimumInputLength: 2,
                width: '100%',
                placeholder: "Select Daughter Companies",
                multiple: true
            });
        });
    </script>
</x-app-layout>