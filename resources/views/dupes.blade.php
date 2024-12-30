<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Duplicates') }}
        </h2>
    </x-slot>
    <div class="main-wrapper px-6 py-12">
        <div class="mx-auto bg-white mb-4">
            <div class="bg-light overflow-hidden shadow-sm py-6 px-6 sm:py-4 sm:px-4">
                <div class="overflow-auto data-table ">
                <table class="table-auto w-full mt-4" id="myTable">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Domain</th>
                        <th>Revenue</th>
                        <th>Headcount</th>
                        <th>Action</th>
                    </tr>
                    @foreach($dupes as $company)
                        <tr>
                            <td>{{ $company->id }}</td>
                            <td>{{ $company->name }}</td>
                            <td>{{ $company->domain }}</td>
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