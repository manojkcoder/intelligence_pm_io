<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Classifications') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm  py-4 px-4">
                <div class="data-table overflow-auto dataTable">
                <table class="table-auto w-full dataTable">
                    <thead class="bg-light-blue">
                        <tr>
                            <th class="px-4 py-2">Sr. No.</th>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Description</th>
                            <th class="px-4 py-2">Revenue</th>
                            <th class="px-4 py-2">Employee</th>
                            <th class="px-4 py-2">WZ Codes</th>
                            <th class="px-4 py-2">NAICS Codes</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classifications as $key => $classification)
                            <tr>
                                <td data-title="Sr. No." class="px-4 py-2">{{ $key + 1 }}</td>
                                <td data-title="Name" class="px-4 py-2">{{ $classification->name }}</td>
                                <td data-title="Description" class="px-4 py-2">{{ $classification->description }}</td>
                                <td data-title="Revenue" class="px-4 py-2">{{ $classification->revenue_threshold }} - {{ $classification->revenue_max }}</td>
                                <td data-title="Employee" class="px-4 py-2">{{ $classification->employee_threshold }} - {{ $classification->employee_max }}</td>
                                <td data-title="WZ Codes" class="px-4 py-2">{{ implode(" | ",$classification->wz_codes) }}</td>
                                <td data-title="NAICS Codes" class="px-4 py-2">{{ implode(" | ",$classification->naics_codes) }}</td>
                                <td class="px-4 py-2  gap-2 action">
                                    <a href="{{ route('classifications.edit',$classification->id) }}" class="btn-bg-primary text-white  py-2 px-4 flex justify-center">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
               </div>
            </div>
        </div>
    </div>

    <style>
table.dataTable thead th:first-child {
min-width: 70px;
}
table.dataTable tbody td {
	vertical-align: top;
}

@media(max-width:1024px){
.data-table table tbody tr td[data-title] br{
    margin:6px 0px;
    display:block;
}
.data-table table tbody tr td:first-child {
	position: static;
}
.data-table table tbody tr td {
	padding-left: unset;
}
.data-table table tbody tr {
	padding: 15px;
}
}    
  
</x-app-layout>