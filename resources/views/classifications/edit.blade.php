<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Classification') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="mx-auto px-6 px-8">
            <div class="bg-white overflow-hidden shadow-sm  px-6 py-6">
                <form method="post" action="{{ route('classifications.update',$classification->id) }}">
                    @csrf
                    @method('patch')
                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Name')"/>
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name',$classification->name)" required autofocus autocomplete="name"/>
                        <x-input-error class="mt-2" :messages="$errors->get('name')"/>
                    </div>
                    <div class="mb-4">
                        <x-input-label for="description" :value="__('Description')"/>
                        <x-text-input id="description" name="description" type="text" class="mt-1 block w-full" :value="old('description',$classification->description)" required/>
                        <x-input-error class="mt-2" :messages="$errors->get('description')"/>
                    </div>
                    <div class="mb-4 flex items-center gap-4">
                        <div class="flex-1">
                            <x-input-label for="revenue_threshold" :value="__('Min. Revenue')"/>
                            <x-text-input id="revenue_threshold" name="revenue_threshold" type="text" class="mt-1 block w-full" :value="old('revenue_threshold',$classification->revenue_threshold)" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('revenue_threshold')"/>
                        </div>
                        <div class="flex-1">
                            <x-input-label for="revenue_max" :value="__('Max. Revenue')"/>
                            <x-text-input id="revenue_max" name="revenue_max" type="text" class="mt-1 block w-full" :value="old('revenue_max',$classification->revenue_max)" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('revenue_max')"/>
                        </div>
                    </div>
                    <div class="mb-4 flex items-center gap-4">
                        <div class="flex-1">
                            <x-input-label for="employee_threshold" :value="__('Min. Employee')"/>
                            <x-text-input id="employee_threshold" name="employee_threshold" type="text" class="mt-1 block w-full" :value="old('employee_threshold',$classification->employee_threshold)" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('employee_threshold')"/>
                        </div>
                        <div class="flex-1">
                            <x-input-label for="employee_max" :value="__('Max. Employee')"/>
                            <x-text-input id="employee_max" name="employee_max" type="text" class="mt-1 block w-full" :value="old('employee_max',$classification->employee_max)" required/>
                            <x-input-error class="mt-2" :messages="$errors->get('employee_max')"/>
                        </div>
                    </div>
                    <div class="mb-6">
                        <x-input-label :value="__('WZ Codes')"/>
                        <div class="mb-6">
                            <div class="data-table overflow-auto">
                            <table class="mt-2 dataTable data-table" id="table1">
                                <thead class="bg-light-blue">
                                    <tr>
                                        <th class="p-2">Select</th>
                                        <th class="p-2 flex justify-between">Code</th>
                                        <th class="p-2">Branch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($industries as $industry)
                                        @if(in_array($industry['wz_code'],$classification->wz_codes))
                                            <tr class="border-b">
                                                <td data-title="Select" class="p-2">
                                                    <x-text-input name="wz_codes[]" type="checkbox" :value="$industry['wz_code']" :id="'code-'.$industry['wz_code']" :checked="in_array($industry['wz_code'],$classification->wz_codes)"/>
                                                </td>
                                                <td  data-title="Code" class="p-2 flex fle-wrap">
                                                    <x-input-label :for="'code-'.$industry['wz_code']" :value="$industry['wz_code']"/>
                                                </td>
                                                <td data-title="Branch"  class="p-2">
                                                    <x-input-label :for="'code-'.$industry['wz_code']" :value="$industry['branch']"/>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    @foreach($industries as $industry)
                                        @if(!in_array($industry['wz_code'],$classification->wz_codes))
                                            <tr class="border-b">
                                                <td data-title="Select" class="p-2">
                                                    <x-text-input name="wz_codes[]" type="checkbox" :value="$industry['wz_code']" :id="'code-'.$industry['wz_code']" :checked="in_array($industry['wz_code'],$classification->wz_codes)"/>
                                                </td>
                                                <td  data-title="Code" class="p-2 flex fle-wrap">
                                                    <x-input-label :for="'code-'.$industry['wz_code']" :value="$industry['wz_code']"/>
                                                </td>
                                                <td data-title="Branch"  class="p-2">
                                                    <x-input-label :for="'code-'.$industry['wz_code']" :value="$industry['branch']"/>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                            <x-input-error class="mt-2" :messages="$errors->get('wz_codes')"/>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('wz_codes')"/>
                    </div>
                    <div class="mb-6">
                        <x-input-label :value="__('Exclude WZ Codes')"/>
                        <div class="mb-6">
                            <div class="data-table overflow-auto">
                            <table class="mt-2 dataTable data-table" id="table3">
                                <thead class="bg-light-blue">
                                    <tr>
                                        <th class="p-2">Select</th>
                                        <th class="p-2 flex justify-between">Code</th>
                                        <th class="p-2">Branch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($industries as $industry)
                                        @if(in_array($industry['wz_code'],$classification->negative_wz_codes))
                                            <tr class="border-b">
                                                <td data-title="Select" class="p-2">
                                                    <x-text-input name="negative_wz_codes[]" type="checkbox" :value="$industry['wz_code']" :id="'code-'.$industry['wz_code']" :checked="in_array($industry['wz_code'],$classification->negative_wz_codes)"/>
                                                </td>
                                                <td  data-title="Code" class="p-2 flex fle-wrap">
                                                    <x-input-label :for="'code-'.$industry['wz_code']" :value="$industry['wz_code']"/>
                                                </td>
                                                <td data-title="Branch"  class="p-2">
                                                    <x-input-label :for="'code-'.$industry['wz_code']" :value="$industry['branch']"/>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    @foreach($industries as $industry)
                                        @if(!in_array($industry['wz_code'],$classification->negative_wz_codes))
                                            <tr class="border-b">
                                                <td data-title="Select" class="p-2">
                                                    <x-text-input name="negative_wz_codes[]" type="checkbox" :value="$industry['wz_code']" :id="'code-'.$industry['wz_code']" :checked="in_array($industry['wz_code'],$classification->negative_wz_codes)"/>
                                                </td>
                                                <td  data-title="Code" class="p-2 flex fle-wrap">
                                                    <x-input-label :for="'code-'.$industry['wz_code']" :value="$industry['wz_code']"/>
                                                </td>
                                                <td data-title="Branch"  class="p-2">
                                                    <x-input-label :for="'code-'.$industry['wz_code']" :value="$industry['branch']"/>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                            <x-input-error class="mt-2" :messages="$errors->get('negative_wz_codes')"/>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('negative_wz_codes')"/>
                    </div>
                    <div class="mb-6">
                        <x-input-label :value="__('NAICS Codes')"/>
                        <div class="mb-6">
                            <div class="data-table overflow-auto">
                            <table class="mt-2 dataTable data-table" id="table2">
                                <thead class="bg-light-blue">
                                    <tr>
                                        <th class="p-2">Select</th>
                                        <th class="p-2 flex justify-between">Code</th>
                                        <th class="p-2">Branch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($naics_industries as $industry)
                                        @if(in_array($industry['code'],$classification->naics_codes))
                                            <tr class="border-b">
                                                <td data-title="Select" class="p-2">
                                                    <x-text-input name="naics_codes[]" type="checkbox" :value="$industry['code']" :id="'code-'.$industry['code']" :checked="in_array($industry['code'],$classification->naics_codes)"/>
                                                </td>
                                                <td  data-title="Code" class="p-2 flex fle-wrap">
                                                    <x-input-label :for="'code-'.$industry['code']" :value="$industry['code']"/>
                                                </td>
                                                <td data-title="Title"  class="p-2">
                                                    <x-input-label :for="'code-'.$industry['code']" :value="$industry['title']"/>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    @foreach($naics_industries as $industry)
                                        @if(!in_array($industry['code'],$classification->naics_codes))
                                            <tr class="border-b">
                                                <td data-title="Select" class="p-2">
                                                    <x-text-input name="naics_codes[]" type="checkbox" :value="$industry['code']" :id="'code-'.$industry['code']" :checked="in_array($industry['code'],$classification->naics_codes)"/>
                                                </td>
                                                <td  data-title="Code" class="p-2 flex fle-wrap">
                                                    <x-input-label :for="'code-'.$industry['code']" :value="$industry['code']"/>
                                                </td>
                                                <td data-title="Title"  class="p-2">
                                                    <x-input-label :for="'code-'.$industry['code']" :value="$industry['title']"/>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                            <x-input-error class="mt-2" :messages="$errors->get('naics_codes')"/>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('naics_codes')"/>
                    </div>
                    <div class="flex items-center gap-4">
                        <button type="button" onclick="save()" class="btn-bg-primary text-white py-2 px-4 ">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        var table1;
        var table2;
        $(document).ready(function(){
            table1 = $('#table1').DataTable();
            table2 = $('#table2').DataTable();
            table3 = $('#table3').DataTable();
        });
        function save(){
            formdata = $('form').serialize();
            table1.$('input[type="checkbox"]').each(function(){
                if($(this).is(':checked')){
                    formdata += '&'+$(this).attr('name')+'='+$(this).val();
                }
            });
            table2.$('input[type="checkbox"]').each(function(){
                if($(this).is(':checked')){
                    formdata += '&'+$(this).attr('name')+'='+$(this).val();
                }
            });
            table3.$('input[type="checkbox"]').each(function(){
                if($(this).is(':checked')){
                    formdata += '&'+$(this).attr('name')+'='+$(this).val();
                }
            });
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('classifications.update',$classification->id) }}",
                type: 'PATCH',
                data: formdata,
                success: function(response){
                    alert('Data updated successfully');
                }
            });
        }
    </script>

<style>

form input, form select {border-radius: 0px !important;}
@media(min-width:640px){
.dt-length {flex-direction: row !important; gap: 10px;}
}
@media(max-width:1024px){
.data-table table tbody tr td:first-child {position: static !important;}
#table tbody tr td {display: flex !important;flex-wrap: wrap;padding: 4px 5px;border:0px !important}
}

@media(max-width:767px){
.dt-layout-row {display: flex !important; gap:15px}
}
.dt-length {
	display: flex; flex-direction: column-reverse;

}
.dt-layout-cell.dt-layout-end {
	flex: 1;
}
</style> 
</x-app-layout>