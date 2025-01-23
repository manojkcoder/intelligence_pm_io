<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Setting') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto py-4 px-6">
            <div class="tabs-wrapper flex text-gray-500 border-b border-gray-200 text-base">
                <div class="tab-item p-4 border-b-2 border-transparent active" data-id="ageScores">Age</div>
                <div class="tab-item p-4 border-b-2 border-transparent" data-id="genderScores">Gender</div>
                <div class="tab-item p-4 border-b-2 border-transparent" data-id="cityScores">City</div>
                <div class="tab-item p-4 border-b-2 border-transparent" data-id="educationScores">Education</div>
                <div class="tab-item p-4 border-b-2 border-transparent" data-id="sharedContactScores">Shared Contact</div>
                <div class="tab-item p-4 border-b-2 border-transparent" data-id="sharedOrganizationScores">Shared Organization</div>
                <div class="tab-item p-4 border-b-2 border-transparent" data-id="activityScores">Activity</div>
            </div>
            <div id="ageScores" class="tab-content bg-white overflow-hidden shadow-sm p-8 active">
                <form method="POST" action="{{ route('updateSetting') }}">
                    @csrf
                    @foreach($ageScores as $k => $ageScore)
                        <div class="flex flex-wrap gap-4 mb-3">
                            <div class="flex-1">
                                <label>Min</label>
                                <input class="block mt-1 w-full" type="number" name="age[{{ $k }}][min]" value="{{ $ageScore['min'] }}" required autofocus/>
                            </div>
                            <div class="flex-1">
                                <label>Max</label>
                                <input class="block mt-1 w-full" type="number" name="age[{{ $k }}][max]" value="{{ $ageScore['max'] }}" required autofocus/>
                            </div>
                            <div class="flex-1">
                                <label>Score</label>
                                <input class="block mt-1 w-full" type="number" name="age[{{ $k }}][score]" value="{{ $ageScore['score'] }}" required autofocus/>
                            </div>
                        </div>
                    @endforeach
                    <div class="flex justify-end mt-4">
                        <button class="btn-bg-primary text-white py-2 px-4">Save</button>
                    </div>
                </form>
            </div>
            <div id="genderScores" class="tab-content bg-white overflow-hidden shadow-sm p-8">
                <form method="POST" action="{{ route('updateSetting') }}">
                    @csrf
                    @foreach($genderScores as $k => $genderScore)
                        <div class="flex flex-wrap gap-4 mb-3">
                            <div class="flex-1">
                                <label>Gender</label>
                                <select class="block mt-1 w-full" name="gender[{{ $k }}][gender]" value="{{ $genderScore['gender'] }}" required>
                                    <option value="">Select</option>
                                    <option value="male" {{ $genderScore['gender'] == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $genderScore['gender'] == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <label>Score</label>
                                <input class="block mt-1 w-full" type="number" name="gender[{{ $k }}][score]" value="{{ $genderScore['score'] }}" required autofocus/>
                            </div>
                        </div>
                    @endforeach
                    <div class="flex justify-end mt-4">
                        <button class="btn-bg-primary text-white py-2 px-4">Save</button>
                    </div>
                </form>
            </div>
            <div id="cityScores" class="tab-content bg-white overflow-hidden shadow-sm p-8">
                <form method="POST" action="{{ route('updateSetting') }}">
                    @csrf
                    <div class="flex flex-wrap gap-4 mb-3">
                        <div class="flex-1">
                            <label>Score</label>
                            <input class="block mt-1 w-full" type="number" name="cityScores" value="{{ $cityScores }}" required autofocus />
                        </div>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button class="btn-bg-primary text-white py-2 px-4">Save</button>
                    </div>
                </form>
            </div>
            <div id="educationScores" class="tab-content bg-white overflow-hidden shadow-sm p-8">
                <form method="POST" action="{{ route('updateSetting') }}">
                    @csrf
                    @foreach($educationScores as $k => $educationScore)
                        <div class="flex flex-wrap gap-4 mb-3">
                            <div class="flex-1">
                                <label>Type</label>
                                <select class="block mt-1 w-full" name="education[{{ $k }}][type]" value="{{ $educationScore['type'] }}" required>
                                    <option value="">Select</option>
                                    <option value="public" {{ $educationScore['type'] == 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="private" {{ $educationScore['type'] == 'private' ? 'selected' : '' }}>Private</option>
                                    <option value="premiere" {{ $educationScore['type'] == 'premiere' ? 'selected' : '' }}>Premiere</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <label>Score</label>
                                <input class="block mt-1 w-full" type="number" name="education[{{ $k }}][score]" value="{{ $educationScore['score'] }}" required autofocus/>
                            </div>
                        </div>
                    @endforeach
                    <div class="flex justify-end mt-4">
                        <button class="btn-bg-primary text-white py-2 px-4">Save</button>
                    </div>
                </form>
            </div>
            <div id="sharedContactScores" class="tab-content bg-white overflow-hidden shadow-sm p-8">
                <form method="POST" action="{{ route('updateSetting') }}">
                    @csrf
                    @foreach($sharedContactScores as $k => $sharedContactScore)
                        <div class="flex flex-wrap gap-4 mb-3">
                            <div class="flex-1">
                                <label>Min</label>
                                <input class="block mt-1 w-full" type="number" name="sharedContact[{{ $k }}][min]" value="{{ $sharedContactScore['min'] }}" required autofocus/>
                            </div>
                            <div class="flex-1">
                                <label>Max</label>
                                <input class="block mt-1 w-full" type="number" name="sharedContact[{{ $k }}][max]" value="{{ $sharedContactScore['max'] }}" required autofocus/>
                            </div>
                            <div class="flex-1">
                                <label>Score</label>
                                <input class="block mt-1 w-full" type="number" name="sharedContact[{{ $k }}][score]" value="{{ $sharedContactScore['score'] }}" required autofocus/>
                            </div>
                        </div>
                    @endforeach
                    <div class="flex justify-end mt-4">
                        <button class="btn-bg-primary text-white py-2 px-4">Save</button>
                    </div>
                </form>
            </div>
            <div id="sharedOrganizationScores" class="tab-content bg-white overflow-hidden shadow-sm p-8">
                <form method="POST" action="{{ route('updateSetting') }}">
                    @csrf
                    @foreach($sharedOrganizationScores as $k => $sharedOrganizationScore)
                        <div class="flex flex-wrap gap-4 mb-3">
                            <div class="flex-1">
                                <label>Min</label>
                                <input class="block mt-1 w-full" type="number" name="sharedOrganization[{{ $k }}][min]" value="{{ $sharedOrganizationScore['min'] }}" required autofocus/>
                            </div>
                            <div class="flex-1">
                                <label>Max</label>
                                <input class="block mt-1 w-full" type="number" name="sharedOrganization[{{ $k }}][max]" value="{{ $sharedOrganizationScore['max'] }}" required autofocus/>
                            </div>
                            <div class="flex-1">
                                <label>Score</label>
                                <input class="block mt-1 w-full" type="number" name="sharedOrganization[{{ $k }}][score]" value="{{ $sharedOrganizationScore['score'] }}" required autofocus/>
                            </div>
                        </div>
                    @endforeach
                    <div class="flex justify-end mt-4">
                        <button class="btn-bg-primary text-white py-2 px-4">Save</button>
                    </div>
                </form>
            </div>
            <div id="activityScores" class="tab-content bg-white overflow-hidden shadow-sm p-8">
                <form method="POST" action="{{ route('updateSetting') }}">
                    @csrf
                    @foreach($activityScores as $k => $activityScore)
                        <div class="flex flex-wrap gap-4 mb-3">
                            <div class="flex-1">
                                <label>Min</label>
                                <input class="block mt-1 w-full" type="number" name="activity[{{ $k }}][min]" value="{{ $activityScore['min'] }}" required autofocus/>
                            </div>
                            <div class="flex-1">
                                <label>Max</label>
                                <input class="block mt-1 w-full" type="number" name="activity[{{ $k }}][max]" value="{{ $activityScore['max'] }}" required autofocus/>
                            </div>
                            <div class="flex-1">
                                <label>Score</label>
                                <input class="block mt-1 w-full" type="number" name="activity[{{ $k }}][score]" value="{{ $activityScore['score'] }}" required autofocus/>
                            </div>
                        </div>
                    @endforeach
                    <div class="flex justify-end mt-4">
                        <button class="btn-bg-primary text-white py-2 px-4">Save</button>
                    </div>
                </form>
            </div>
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
        });
    </script>
    <style>
        .tabs-wrapper{display:flex;gap:10px;}
        .tabs-wrapper .tab-item{cursor:pointer;}
        .tabs-wrapper .tab-item.active{border-color:#2563EB;color:#2563EB;}
        .tab-content{display:none;}
        .tab-content.active{display:block;}
    </style>
</x-app-layout>