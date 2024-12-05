<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Industries') }}
        </h2>
    </x-slot>

    <div class="main-wrapper px-6 py-12">
        <div class="mx-auto mb-4">
        <form method="POST" action="{{ route('industries.updateStatus') }}" class="filter-form flex justify-between flex-wrap gap-4">
                @csrf
                <div class="flex justify-end gap-2 button-wrapper sm:gap-2">
                    <a href="{{ route('industries',['all']) }}" class="btn-bg-secondary py-2 px-4 text-white">ALL</a>
                    <a href="{{ route('industries',['tam']) }}" class="btn-bg-primary py-2 px-4 text-white">TAM</a>
                    <a href="{{ route('industries',['sam']) }}" class="btn-bg-primary py-2 px-4 text-white">SAM</a>
                    <a href="{{ route('industries',['som']) }}" class="btn-bg-primary py-2 px-4 text-white">SOM</a>
                </div>
          </div>    
            <div class="bg-white overflow-hidden shadow-sm  py-4 px-4">
              
                    <div class="data-table overflow-auto">
                    <table class="table-auto w-full dataTable ">
                        <thead  class="bg-light-blue">
                            <tr>
                                <th class="px-4 py-2"></th>
                                <!-- <th class="px-4 py-2">ID</th> -->
                                <th class="px-4 py-2">WZ Code</th>
                                <th class="px-4 py-2">Branch</th>
                                <th class="px-4 py-2">Associated Industries</th>
                                <th class="px-4 py-2">Score</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($industries as $key => $industry)
                            <tr>
                                <td  class=" px-4 py-2"><input type="checkbox" name="industries[]" value="{{$industry->id}}" {{ $industry->enabled ? 'checked' : '' }}></td>
                                <!-- <td data-title="ID" class=" px-4 py-2">{{ $industry->id }}</td> -->
                                <td data-title="WZ Code" class=" px-4 py-2">{{ $industry->wz_code }}</td>
                                <td data-title="Branch" class=" px-4 py-2">{{ $industry->branch }}</td>
                                <td data-title="Associated Industries" class=" px-4 py-2">{!! nl2br($industry->associated_industries) !!}</td>
                                <td data-title="Score"class=" px-4 py-2">{{ $industry->score }}</td>
                                <td class=" px-4 py-2 action  ">
                                    <a href="{{ route('industries.edit', ['id' => $industry->id]) }}" class="btn-bg-primary text-white flex  justify-center font-bold py-2 px-4 ">Edit</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div class="flex justify-end gap-4 mt-4">
                        <button type="submit" class="btn-bg-secondary text-white font-bold py-2 px-4 ">Save</button>
                    </div>
                </form>
            </div>
        </div>

    
</x-app-layout>
