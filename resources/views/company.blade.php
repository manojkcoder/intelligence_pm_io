<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Account') }}
        </h2>
    </x-slot>

    <div class="py-12 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm rounded p-6">
                @if($company->deleted_at)
                <div class="">
                    <!-- Delete Company -->
                    <form action="{{ route('deleteCompany', $company->id) }}" method="POST" class="inline">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="btn-bg-primary text-white  py-2 px-4">Restore</button>
                    </form>
                </div>
                @else
                <div class="">
                    <!-- Delete Company -->
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
                            </tbody>
                        </table>
                        <div class="flex justify-between mb-4 items-center mt-6">
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Contacts</h2>
                            <a href="{{ route('createContact', $company->id) }}" class="btn-bg-primary text-white  py-2 px-4 ">Add Contact</a>
                        </div>
                        <!-- Contacts Table -->
                        <table class="table-auto w-full" id="myTable">
                            <thead class="bg-light-blue">
                                <tr>
                                    <th class="px-4 py-2">First Name</th>
                                    <th class="px-4 py-2">Last Name</th>
                                    <th class="px-4 py-2">Email</th>
                                    <th class="px-4 py-2">Phone</th>
                                    <th class="px-4 py-2">Position</th>
                                    <th class="px-4 py-2">LinkedIn</th>
                                    <th class="px-4 py-2">URL</th>
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
        </div>
    </div>

<style>

    @media(max-width:1024px){
        .data-table.top-table table tbody tr td:first-child{
          position:static;
          font-weight:600;
        }
        .data-table.top-table table tbody tr {
            gap: 5px;
        }
        .data-table.top-table table tbody tr td {
        padding: 0px !important;
        }
        
        }
</style>
</x-app-layout>
