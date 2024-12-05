<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('WZ Codes') }}
        </h2>
    </x-slot>

    <div class="main-wrapper px-6 py-12 max-w-7xl mx-auto">
    <div class="mx-auto mb-4">
    <form action="{{route('wz_code_status')}}" method="get" class="filter-form flex justify-between flex-wrap gap-4">
                    <div class="flex justify-end gap-2">
                        <select name="filter" id="filter" class="bg-white  py-2 px-4  border border-transparent">
                            <option @if(request()->input('filter') == 'all') selected @endif value="all">All Types</option>
                            <option  @if(request()->input('filter') == 'incomplete') selected @endif value="incomplete">Incomplete</option>
                            <option  @if(request()->input('filter') == 'tam') selected @endif value="tam">TAM</option>
                            <option  @if(request()->input('filter') == 'sam') selected @endif value="sam">SAM</option>
                            <option  @if(request()->input('filter') == 'som') selected @endif value="som">SOM</option>
                            <option  @if(request()->input('filter') == 'tam_samson4') selected @endif value="tam_samson4">TAM Samson 4</option>
                            <option  @if(request()->input('filter') == 'sam_samson4') selected @endif value="sam_samson4">SAM Samson 4</option>
                            <option  @if(request()->input('filter') == 'som_samson4') selected @endif value="som_samson4">SOM Samson 4</option>
                        </select>
                        <select name="flag" id="flag" class="bg-white  py-2 px-4  border border-transparent">
                            <option  @if(request()->input('flag') == 'all') selected @endif value="all">No Flags</option>
                            @foreach ($flags as $flag)
                                <option @if(request()->input('flag') == $flag) selected @endif value="{{$flag}}">{{$flag}}</option>
                            @endforeach
                        </select>
                        <select name="country" id="country" class="bg-white  py-2 px-4  border border-transparent">
                            <option  @if(request()->input('country') == 'all') selected @endif value="all">All Countries</option>
                            @foreach ($countries as $country)
                                <option @if(request()->input('country') == $country) selected @endif value="{{$country}}">{{$country}}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-bg-primary text-white py-2 px-4  border border-transparent">Filter</button>
                    </div>
                </form>
          </div> 
    
        <div class="mx-auto ">
            <div class="bg-white overflow-hidden shadow-sm  py-6 px-6 sm:py-4 sm:px-4">
               
                <table class="table-auto w-full">
                    <thead class="bg-light-blue">
                        <tr>
                            <th class="px-4 py-2">WZ Code</th>
                            <th class="px-4 py-2">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($counts as $wz_code => $count)
                            <tr>
                                <td data-title="WZ Code" class="border px-4 py-2">{{ $wz_code }}</td>
                                <td data-title="Count" class="border px-4 py-2"><a href="{{route('dashboard', ['wz_code' => $wz_code])}}" class="text-blue-500 hover:text-blue-700">{{ $count }}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


<style>
.filter-form select {min-width: 160px;}

@media(max-width:767px){
.filter-form a, .filter-form select,.filter-form button {font-size:14px;flex:1;}
.filter-form select{padding: 5px 12px !important;}
.filter-form button, .filter-form button a{ padding: 8px 12px !important; }
.filter-form > div { width: 100%;flex-wrap: wrap;justify-content: center;}
}       
</style> 
</x-app-layout>
