<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Contact') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto py-4 px-6">
            <div class="bg-white overflow-hidden shadow-sm rounded p-6">
                <div class="flex-1">
                    <form method="POST" action="{{ route('updateContact',[$contact->company_id,$contact->id]) }}">
                        @method('PATCH')
                        @csrf
                        <table class="table-auto w-full table-2" id="myTable">
                            <thead>
                                <tr>
                                    <td class="py-2">First Name</td>
                                    <td class="py-2"><input type="text" name="first_name" value="{{ $contact->first_name }}" class="block mt-1 w-full"></td>
                                </tr>
                                <tr>
                                    <td class="py-2">Last Name</td>
                                    <td class="py-2"><input type="text" name="last_name" value="{{ $contact->last_name }}" class="block mt-1 w-full"></td>
                                </tr>
                                <tr>
                                    <td class="py-2">Email</td>
                                    <td class="py-2"><input type="text" name="email" value="{{ $contact->email }}" class="block mt-1 w-full"></td>
                                </tr>
                                <tr>
                                    <td class="py-2">Phone</td>
                                    <td class="py-2"><input type="text" name="phone" value="{{ $contact->phone }}" class="block mt-1 w-full"></td>
                                </tr>
                                <tr>
                                    <td class="py-2">Position</td>
                                    <td class="py-2"><input type="text" name="position" value="{{ $contact->position }}" class="block mt-1 w-full"></td>
                                </tr>
                                <tr>
                                    <td class="py-2">Location</td>
                                    <td class="py-2"><input type="text" name="location" value="{{ $contact->location }}" class="block mt-1 w-full"></td>
                                </tr>
                                <tr>
                                    <td class="py-2">Age</td>
                                    <td class="py-2"><input type="number" name="age" value="{{ $contact->age }}" class="block mt-1 w-full"></td>
                                </tr>
                                <tr>
                                    <td class="py-2">Gender</td>
                                    <td class="py-2">
                                        <select name="gender" class="block mt-1 w-full">
                                            <option value="">Select</option>
                                            <option value="male" @if($contact->gender == "male") selected @endif>Male</option>
                                            <option value="female" @if($contact->gender == "female") selected @endif>Female</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2">LinkedIn</td>
                                    <td class="py-2"><input type="text" name="linkedin" value="{{ $contact->linkedin }}" class="block mt-1 w-full"></td>
                                </tr>
                                <tr>
                                    <td class="py-2">URL</td>
                                    <td class="py-2"><input type="text" name="url" value="{{ $contact->url }}" class="block mt-1 w-full"></td>
                                </tr>
                                <tr>
                                    <td class="py-2">Target Category</td>
                                    <td class="py-2"><input type="text" name="target_category" value="{{ $contact->target_category }}" class="block mt-1 w-full"></td>
                                </tr>
                                <tr>
                                    <td class="py-2">Trigger</td>
                                    <td class="py-2"><input type="text" name="triggers" value="{{ $contact->triggers }}" class="block mt-1 w-full"></td>
                                </tr>
                                <tr>
                                    <td class="py-2">Skills</td>
                                    <td class="py-2"><textarea name="skills" class="block mt-1 w-full">{{ $contact->skills }}</textarea></td>
                                </tr>
                                <tr>
                                    <td class="py-2">Notes</td>
                                    <td class="py-2"><textarea name="notes" class="block mt-1 w-full">{{ $contact->notes }}</textarea></td>
                                </tr>
                                <tr>
                                    <td class="py-2">Extra</td>
                                    <td class="py-2"><textarea name="extra" class="block mt-1 w-full">{{ $contact->extra }}</textarea></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <a href="{{ route('viewCompany', $contact->company_id) }}" class="btn-bg-primary text-white  py-2 px-4 mr-2 mt-2">Cancel</a>
                                        <button class="btn-bg-secondary text-white  py-2 px-4 mt-2">Save</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        .btn-bg-secondary{line-height:normal;}
        @media(max-width:640px){
            .table-2 tr{display:flex;flex-direction:column;gap:0;margin:12px 0;}
            .table-2 tr td{padding:0;}
        }
    </style>
</x-app-layout>
