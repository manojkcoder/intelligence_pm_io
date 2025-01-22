<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('View Contact') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto py-4 px-6">
            <div class="tabs-wrapper flex text-gray-500 border-b border-gray-200 text-base">
                <div class="tab-item p-4 border-b-2 border-transparent active" data-id="profile">Profile</div>
                @if($contact->jobs->count() > 0)
                    <div class="tab-item p-4 border-b-2 border-transparent" data-id="jobs">Jobs</div>
                @endif
                @if($contact->schools->count() > 0)
                    <div class="tab-item p-4 border-b-2 border-transparent" data-id="educations">Educations</div>
                @endif
                @if($contact->licences->count() > 0)
                    <div class="tab-item p-4 border-b-2 border-transparent" data-id="licences">Licences</div>
                @endif
                @if($contact->activities->count() > 0)
                    <div class="tab-item p-4 border-b-2 border-transparent" data-id="activities">Activities</div>
                @endif
                @if($contact->connections->count() > 0)
                    <div class="tab-item p-4 border-b-2 border-transparent" data-id="connections">Connections</div>
                @endif
            </div>
            <div id="profile" class="tab-content bg-white overflow-hidden shadow-sm rounded p-6 active">
                <div class="flex-1">
                    <div class="responsive-table">
                        <table class="table-auto w-full dataTable">
                            <tbody>
                                @if($contact->profile_image)
                                    <tr>
                                        <td class="px-4 py-2">Profile:</td>
                                        <td class="px-4 py-2">
                                            <img src="{{ $contact->profile_image }}" alt="Profile Image" width="100" height="100"/>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="px-4 py-2">Name:</td>
                                    <td class="px-4 py-2 capitalize">{{ $contact->first_name }} {{ $contact->last_name }}</td>
                                </tr>
                                @if($contact->email)
                                    <tr>
                                        <td class="px-4 py-2">Email:</td>
                                        <td class="px-4 py-2">{{ $contact->email }}</td>
                                    </tr>
                                @endif
                                @if($contact->phone)
                                    <tr>
                                        <td class="px-4 py-2">Phone:</td>
                                        <td class="px-4 py-2">{{ $contact->phone }}</td>
                                    </tr>
                                @endif
                                @if($contact->company)
                                    <tr>
                                        <td class="px-4 py-2">Company:</td>
                                        <td class="px-4 py-2 capitalize">{{ $contact->company->name }}</td>
                                    </tr>
                                @endif
                                @if($contact->domain)
                                    <tr>
                                        <td class="px-4 py-2">Domain:</td>
                                        <td class="px-4 py-2">{{ $contact->domain }}</td>
                                    </tr>
                                @endif
                                @if($contact->position)
                                    <tr>
                                        <td class="px-4 py-2">Position:</td>
                                        <td class="px-4 py-2 capitalize">{{ $contact->position }}</td>
                                    </tr>
                                @endif
                                @if($contact->location)
                                    <tr>
                                        <td class="px-4 py-2">Location:</td>
                                        <td class="px-4 py-2">{{ $contact->location }}</td>
                                    </tr>
                                @endif
                                @if($contact->country)
                                    <tr>
                                        <td class="px-4 py-2">Country:</td>
                                        <td class="px-4 py-2">{{ $contact->country }}</td>
                                    </tr>
                                @endif
                                @if($contact->birthday)
                                    <tr>
                                        <td class="px-4 py-2">Birthday:</td>
                                        <td class="px-4 py-2">{{ $contact->birthday }}</td>
                                    </tr>
                                @endif
                                @if($contact->age)
                                    <tr>
                                        <td class="px-4 py-2">Age:</td>
                                        <td class="px-4 py-2">{{ $contact->age }}</td>
                                    </tr>
                                @endif
                                @if($contact->gender)
                                    <tr>
                                        <td class="px-4 py-2">Gender:</td>
                                        <td class="px-4 py-2 capitalize">{{ $contact->gender }}</td>
                                    </tr>
                                @endif
                                @if($contact->linkedin)
                                    <tr>
                                        <td class="px-4 py-2">Linkedin:</td>
                                        <td class="px-4 py-2"><a href="<?=$contact->linkedin;?>" target="_blank">View Profile</a></td>
                                    </tr>
                                @endif
                                @if($contact->websites)
                                    <tr>
                                        <td class="px-4 py-2">Website:</td>
                                        <td class="px-4 py-2"><a href="<?=((strpos($contact->websites,'http') !== false) ? $contact->websites : '//'.$contact->websites);?>" target="_blank">View Website</a></td>
                                    </tr>
                                @endif
                                @if($contact->summary)
                                    <tr>
                                        <td class="px-4 py-2">Summary:</td>
                                        <td class="px-4 py-2">{{ $contact->summary }}</td>
                                    </tr>
                                @endif
                                @if($contact->skills)
                                    <tr>
                                        <td class="px-4 py-2">Skills:</td>
                                        <td class="px-4 py-2">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach(explode(",",$contact->skills) as $skill)
                                                    <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">{{ $skill }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                @if($contact->courses)
                                    <tr>
                                        <td class="px-4 py-2">Courses:</td>
                                        <td class="px-4 py-2">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach(json_decode($contact->courses) as $course)
                                                    @if($course != 'Noch keine Informationen verfügbar')
                                                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">{{ $course }}</span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                @if($contact->languages)
                                    <tr>
                                        <td class="px-4 py-2">Languages:</td>
                                        <td class="px-4 py-2">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach(json_decode($contact->languages) as $language)
                                                    @if($language != 'Noch keine Informationen verfügbar')
                                                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">{{ $language }}</span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                @if($contact->notes)
                                    <tr>
                                        <td class="px-4 py-2">Notes:</td>
                                        <td class="px-4 py-2">{{ $contact->notes }}</td>
                                    </tr>
                                @endif
                                @if($contact->extra)
                                    <tr>
                                        <td class="px-4 py-2">Extra:</td>
                                        <td class="px-4 py-2">{{ $contact->extra }}</td>
                                    </tr>
                                @endif
                                @if($contact->activity_rate)
                                    <tr>
                                        <td class="px-4 py-2">Weighted Activity Rate:</td>
                                        <td class="px-4 py-2">{{ $contact->activity_rate }}</td>
                                    </tr>
                                @endif
                                @if($contact->avg_activity_rate)
                                    <tr>
                                        <td class="px-4 py-2">Average Activities:</td>
                                        <td class="px-4 py-2">{{ round($contact->avg_activity_rate,2) }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if($contact->jobs->count() > 0)
                <div id="jobs" class="tab-content bg-white overflow-hidden shadow-sm rounded p-6">
                    <div class="flex-1">
                        <div class="responsive-table">
                            <table class="table-auto w-full dataTable myDataTable">
                                <thead class="bg-light-blue">
                                    <tr>
                                        <th class="p-4">Company</th>
                                        <th class="p-4">Job Title</th>
                                        <th class="p-4">Date</th>
                                        <th class="p-4">Duration</th>
                                        <th class="p-4">Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contact->jobs as $job)
                                        <tr>
                                            <td class="p-4">{{ $job->company_name }}</td>
                                            <td class="p-4">{{ $job->job_title }}</td>
                                            <td class="p-4">{{ $job->date_range }}</td>
                                            <td class="p-4">{{ $job->duration }}</td>
                                            <td class="p-4">{{ $job->location }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            @if($contact->schools->count() > 0)
                <div id="educations" class="tab-content bg-white overflow-hidden shadow-sm rounded p-6">
                    <div class="flex-1">
                        <div class="responsive-table">
                            <table class="table-auto w-full dataTable myDataTable">
                                <thead class="bg-light-blue">
                                    <tr>
                                        <th class="p-4">University/School</th>
                                        <th class="p-4">Degree</th>
                                        <th class="p-4 w-100">Date</th>
                                        <th class="p-4 w-100">Type</th>
                                        <th class="p-4 w-100">Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contact->schools as $school)
                                        <tr>
                                            <td class="p-4">{{ $school->school_name }}</td>
                                            <td class="p-4">{{ $school->degree }}</td>
                                            <td class="p-4 w-100">{{ $school->date_range }}</td>
                                            <td class="p-4 w-100 capitalize">{{ $school->type_of_control }}</td>
                                            <td class="p-4 w-100">{{ $school->score }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            @if($contact->licences->count() > 0)
                <div id="licences" class="tab-content bg-white overflow-hidden shadow-sm rounded p-6">
                    <div class="flex-1">
                        <div class="responsive-table">
                            <table class="table-auto w-full dataTable myDataTable">
                                <thead class="bg-light-blue">
                                    <tr>
                                        <th class="p-4">Name</th>
                                        <th class="p-4">Company</th>
                                        <th class="p-4">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contact->licences as $licence)
                                        <tr>
                                            <td class="p-4">{{ $licence->licence_name }}</td>
                                            <td class="p-4">{{ $licence->company_name }}</td>
                                            <td class="p-4">{{ $licence->licence_date }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            @if($contact->activities->count() > 0)
                <div id="activities" class="tab-content bg-white overflow-hidden shadow-sm rounded p-6">
                    <div class="flex-1">
                        <div class="responsive-table">
                            <table class="table-auto w-full dataTable myDataTable">
                                <thead class="bg-light-blue">
                                    <tr>
                                        <th class="p-4">Author</th>
                                        <th class="p-4">Type</th>
                                        <th class="p-4">Action</th>
                                        <th class="p-4">Like</th>
                                        <th class="p-4">Comment</th>
                                        <th class="p-4">Re Post</th>
                                        <th class="p-4">Date</th>
                                        <th class="p-4">Keywords</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contact->activities as $activity)
                                        <tr>
                                            <td class="p-4">{{ $activity->author }}</td>
                                            <td class="p-4">{{ $activity->type }}</td>
                                            <td class="p-4">{{ $activity->action }}</td>
                                            <td class="p-4">{{ $activity->like_count }}</td>
                                            <td class="p-4">{{ $activity->comment_count }}</td>
                                            <td class="p-4">{{ $activity->repost_count }}</td>
                                            <td class="p-4">{{ $activity->post_date }}</td>
                                            <td class="p-4">{{ ($activity->is_relevant == 1 ? "Relevant" : "Not Relevant") }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            @if($contact->connections->count() > 0)
                <div id="connections" class="tab-content bg-white overflow-hidden shadow-sm rounded p-6">
                    <div class="flex-1">
                        <div class="responsive-table">
                            <table class="table-auto w-full dataTable myDataTable">
                                <thead class="bg-light-blue">
                                    <tr>
                                        <th class="p-4">Image</th>
                                        <th class="p-4">Name</th>
                                        <th class="p-4">Position</th>
                                        <th class="p-4">Location</th>
                                        <th class="p-4">Linkedin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contact->connections as $connection)
                                        <tr>
                                            <td class="p-4">
                                                @if($connection->image)
                                                    <img src="{{ $connection->image }}" alt="Profile Image" width="60" height="60" class="rounded-full"/>
                                                @endif
                                            </td>
                                            <td class="p-4">{{ $connection->name }}</td>
                                            <td class="p-4">{{ $connection->position }}</td>
                                            <td class="p-4">{{ $connection->location }}</td>
                                            <td class="p-4">
                                                @if($connection->linkedin)
                                                    <a href="<?=$connection->linkedin;?>" target="_blank">View Profile</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
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
            $('.myDataTable').DataTable({
                "responsive": true,
                "paging": true,
                "pageLength": 10,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "lengthMenu": [[10,25,50],[10,25,50]]
            });
        });
    </script>
    <style>
        .tabs-wrapper{display:flex;gap:10px;}
        .tabs-wrapper .tab-item{cursor:pointer;}
        .tabs-wrapper .tab-item.active{border-color:#2563EB;color:#2563EB;}
        .tab-content{display:none;}
        .tab-content.active{display:block;}
        .w-100{width:100px !important;min-width:100px !important;}
    </style>
</x-app-layout>