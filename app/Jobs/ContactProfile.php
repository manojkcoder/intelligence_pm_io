<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\ContactJob;
use App\Models\ContactLicence;
use App\Models\ContactSchool;
use App\Models\School;
use App\Jobs\ActivityRate;
use App\Jobs\GenderResearch;
use App\Jobs\DomainResearch;

class ContactProfile implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    private $id;
    private $profileData;
    public function __construct($id,$data){
        $this->id = $id;
        $this->profileData = $data;
    }
    public function handle(): void{
        try{
            $contact = Contact::where("id",$this->id)->first();
            if($contact){
                if(isset($this->profileData['general']['firstName']) && !empty($this->profileData['general']['firstName']) && empty($contact->first_name)){
                    $contact->first_name = $this->profileData['general']['firstName'];
                }
                if(isset($this->profileData['general']['lastName']) && !empty($this->profileData['general']['lastName']) && empty($contact->last_name)){
                    $contact->last_name = $this->profileData['general']['lastName'];
                }
                if(isset($this->profileData['general']['company']) && !empty($this->profileData['general']['company']) && empty($contact->company_name)){
                    $contact->company_name = $this->profileData['general']['company'];
                }
                if(isset($this->profileData['general']['profileUrl']) && !empty($this->profileData['general']['profileUrl']) && empty($contact->linkedin)){
                    $linkedinUrl = rtrim(urldecode($this->profileData['general']['profileUrl']),'/');
                    $linkedinUrl = str_replace('https://www.linkedin.com','https://linkedin.com',$linkedinUrl);
                    $contact->linkedin = $linkedinUrl;
                }
                if(isset($this->profileData['general']['location']) && !empty($this->profileData['general']['location']) && empty($contact->location)){
                    $contact->location = $this->profileData['general']['location'];
                }
                if(isset($this->profileData['general']['countryCode']) && !empty($this->profileData['general']['countryCode'])){
                    $contact->country = $this->profileData['general']['countryCode'];
                }
                if(isset($this->profileData['general']['imgUrl']) && !empty($this->profileData['general']['imgUrl']) && empty($contact->profile_image)){
                    $contact->profile_image = $this->profileData['general']['imgUrl'];
                }
                if(isset($this->profileData['general']['headline']) && !empty($this->profileData['general']['headline']) && empty($contact->position)){
                    $contact->position = $this->profileData['general']['headline'];
                }
                if(isset($this->profileData['general']['description']) && !empty($this->profileData['general']['description']) && empty($contact->summary)){
                    $contact->summary = $this->profileData['general']['description'];
                }
                if(isset($this->profileData['details']['mail']) && !empty($this->profileData['details']['mail']) && empty($contact->email)){
                    $contact->email = $this->profileData['details']['mail'];
                }
                if(isset($this->profileData['details']['phone']) && !empty($this->profileData['details']['phone']) && empty($contact->phone)){
                    $contact->phone = $this->profileData['details']['phone'];
                }
                if(isset($this->profileData['details']['birthday']) && !empty($this->profileData['details']['birthday']) && empty($contact->birthday)){
                    $contact->birthday = $this->profileData['details']['birthday'];
                }
                if(isset($this->profileData['details']['websites']) && !empty($this->profileData['details']['websites']) && empty($contact->websites)){
                    $contact->websites = $this->profileData['details']['websites'];
                }
                if(isset($this->profileData['allSkills']) && !empty($this->profileData['allSkills']) && empty($contact->skills)){
                    $contact->skills = $this->profileData['allSkills'];
                }
                if(isset($this->profileData['accomplishments']) && !empty($this->profileData['accomplishments']) && isset($this->profileData['accomplishments']['courses']) && !empty($this->profileData['accomplishments']['courses'])){
                    $contact->courses = count($this->profileData['accomplishments']['courses']) > 0 ? json_encode($this->profileData['accomplishments']['courses']) : null;
                }
                if(isset($this->profileData['accomplishments']) && !empty($this->profileData['accomplishments']) && isset($this->profileData['accomplishments']['languages']) && !empty($this->profileData['accomplishments']['languages'])){
                    $contact->languages = count($this->profileData['accomplishments']['languages']) > 0 ? json_encode($this->profileData['accomplishments']['languages']) : null;
                }
                $contact->processed = 1;
                $contact->save();
                if(isset($this->profileData['jobs']) && !empty($this->profileData['jobs'])){
                    $this->contactJobs($contact->id,$this->profileData['jobs']);
                }
                if(isset($this->profileData['schools']) && !empty($this->profileData['schools'])){
                    $this->contactEducations($contact->id,$this->profileData['schools']);
                    
                }
                if(isset($this->profileData['licences']) && !empty($this->profileData['licences'])){
                    $this->contactLicences($contact->id,$this->profileData['licences']);
                }
                ActivityRate::dispatch($contact->id);
                if(empty($contact->gender) || empty($contact->age)){
                    GenderResearch::dispatch($contact->id,$contact->first_name . ' '. $contact->last_name,$contact->company_name);
                }
                if(empty($contact->domain)){
                    DomainResearch::dispatch($contact->id,$contact->first_name . ' '. $contact->last_name,$contact->position,$contact->company_name);
                }
            }
        }catch(\Exception $e){
            \Log::error("Error: " . $e->getMessage());
        }
    }
    private function contactJobs($contactId,$jobs){
        foreach($jobs as $job){
            $contactJob = new ContactJob();
            $contactJob->contact_id = $contactId;
            if(isset($job['companyUrl']) && !empty($job['companyUrl'])){
                $contactJob->linkedin_company_url = $job['companyUrl'];
            }
            if(isset($job['companyName']) && !empty($job['companyName'])){
                $contactJob->company_name = $job['companyName'];
            }
            if(isset($job['logoUrl']) && !empty($job['logoUrl'])){
                $contactJob->company_logo_url = $job['logoUrl'];
            }
            if(isset($job['jobTitle']) && !empty($job['jobTitle'])){
                $contactJob->job_title = $job['jobTitle'];
            }
            if(isset($job['dateRange']) && !empty($job['dateRange'])){
                $contactJob->date_range = $job['dateRange'];
            }
            if(isset($job['startedSince']) && !empty($job['startedSince'])){
                $contactJob->started_since = date("Y-m-d",strtotime($job['startedSince']));
            }
            if(isset($job['isCurrent'])){
                $contactJob->is_current = $job['isCurrent'];
            }
            if(isset($job['duration']) && !empty($job['duration'])){
                $contactJob->duration = $job['duration'];
            }
            if(isset($job['description']) && !empty($job['description'])){
                $contactJob->description = $job['description'];
            }
            if(isset($job['location']) && !empty($job['location'])){
                $contactJob->location = $job['location'];
            }
            $contactJob->response = json_encode($job);
            $contactJob->save();
        }
    }
    private function contactEducations($contactId,$schools){
        foreach($schools as $school){
            $contactSchool = new ContactSchool();
            $contactSchool->contact_id = $contactId;
            if(isset($school['schoolUrl']) && !empty($school['schoolUrl'])){
                $contactSchool->linkedin_school_url = $school['schoolUrl'];
            }
            if(isset($school['schoolName']) && !empty($school['schoolName'])){
                $contactSchool->school_name = $school['schoolName'];
                $contactSchool->type_of_control = $this->calculateSchoolType($school['schoolName']);
            }
            if(isset($school['logoUrl']) && !empty($school['logoUrl'])){
                $contactSchool->school_logo_url = $school['logoUrl'];
            }
            if(isset($school['degree']) && !empty($school['degree'])){
                $contactSchool->degree = $school['degree'];
                $contactSchool->score = $this->calculateEducationScore($school['degree']);
            }
            if(isset($school['dateRange']) && !empty($school['dateRange'])){
                $contactSchool->date_range = $school['dateRange'];
            }
            if(isset($school['description']) && !empty($school['description'])){
                $contactSchool->description = $school['description'];
            }
            $contactSchool->response = json_encode($school);
            $contactSchool->save();
        }
    }
    private function contactLicences($contactId,$licences){
        foreach($licences as $licence){
            $contactLicence = new ContactLicence();
            $contactLicence->contact_id = $contactId;
            if(isset($licence['name']) && !empty($licence['name'])){
                $contactLicence->licence_name = $licence['name'];
            }
            if(isset($licence['credentialUrl']) && !empty($licence['credentialUrl'])){
                $contactLicence->credential_url = $licence['credentialUrl'];
            }
            if(isset($licence['companyName']) && !empty($licence['companyName'])){
                $contactLicence->company_name = $licence['companyName'];
            }
            if(isset($licence['date']) && !empty($licence['date'])){
                $contactLicence->licence_date = $licence['date'];
            }
            $contactLicence->response = json_encode($licence);
            $contactLicence->save();
        }
    }
    private function calculateSchoolType($schoolName){
        $typeOfControl = School::where('name',$schoolName)->where(function($query){
            $query->whereRaw("LOWER(type_of_control) LIKE ?",["%private%"])->orWhereRaw("LOWER(type_of_control) LIKE ?",["%public%"]);
        })->value('type_of_control');
        if($typeOfControl){
            return str_contains(strtolower($typeOfControl),'private') ? 'private' : 'public';
        }
        return null;
    }
    private function calculateEducationScore($degreeName){
        $score = 0;
        if(strpos($degreeName,'MBA') !== false){
            $score = 10;
        }else if(strpos($degreeName,'Master in Engineering/IT') !== false || strpos($degreeName,'Master in Engineering') !== false || strpos($degreeName,'Master in IT') !== false){
            $score = 9;
        }else if(strpos($degreeName,'Master in Economics/Finance') !== false || strpos($degreeName,'Master in Economics') !== false || strpos($degreeName,'Master in Finance') !== false){
            $score = 8;
        }else if(strpos($degreeName,'Master in Strategy/Management') !== false || strpos($degreeName,'Master in Strategy') !== false || strpos($degreeName,'Master in Management') !== false){
            $score = 7;
        }else if(strpos($degreeName,'Bachelor in Engineering/IT') !== false || strpos($degreeName,'Bachelor in Engineering') !== false || strpos($degreeName,'Bachelor in IT') !== false){
            $score = 6;
        }else if(strpos($degreeName,'PhD') !== false || strpos($degreeName,'Doctor of Philosophy') !== false){
            $score = 5;
        }else if(strpos($degreeName,'Bachelor in Business/Economics') !== false || strpos($degreeName,'Bachelor in Business') !== false || strpos($degreeName,'Bachelor in Economics') !== false || strpos($degreeName,'Bachelor in Computer Science') !== false){
            $score = 4;
        }else if(strpos($degreeName,'Master in Humanities') !== false){
            $score = 3;
        }else if(strpos($degreeName,'Bachelor in Humanities') !== false){
            $score = 2;
        }
        return $score;
    }
}