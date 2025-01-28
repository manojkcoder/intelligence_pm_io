<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Duplicates') }}</h2>
    </x-slot>
    <div class="main-wrapper px-6 py-12">
        <div class="mx-auto bg-white mb-4">
            <div class="bg-light overflow-hidden shadow-sm py-6 px-6 sm:py-4 sm:px-4">
                <div class="overflow-auto data-table ">
                <table class="table-auto w-full mt-4" id="myTable">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Legal Name</th>
                        <th style="width:160px;">Domain</th>
                        <th style="width:140px;">Country</th>
                        <th style="width:120px;">Revenue</th>
                        <th style="width:120px;">Headcount</th>
                        <th style="width:120px;">Action</th>
                    </tr>
                    @foreach($dupes as $key => $company)
                        @if($key > 0 && $company->domain != $dupes[$key-1]->domain)
                            <tr>
                                <td colspan="8" style="height:30px;"></td>
                            </tr>
                        @endif
                        <tr>
                            <td>{{ $company->id }}</td>
                            <td>{{ $company->name }}</td>
                            <td>{{ $company->legal_name }}</td>
                            <td>{{ $company->domain }}</td>
                            <td>{{ $company->country }}</td>
                            <td>{{ $company->revenue }}</td>
                            <td>{{ $company->headcount }}</td>
                            <td>
                                <a href="{{ route('viewCompany',$company->id) }}">View</a> | <a target="_blank" href="{{ route('deleteCompany',$company->id) }}">Delete</a>
                            </td>
                        </tr>
                    @endforeach
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>