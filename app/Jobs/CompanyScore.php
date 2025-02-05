<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use App\Models\CompanyCompanyClassification;
use App\Models\ContactConnection;
use App\Models\Activity;
use App\Models\Setting;

class CompanyScore implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;
    public $timeout = 100000;
    // public $tries = 5;
    public $failOnTimeout = false;
    protected $companyIds;
    public function __construct(array $companyIds){
        $this->companyIds = $companyIds;
    }
    public function handle(): void{
        try{
            $companies = Company::withTrashed()->whereIn('id',$this->companyIds)->where('existing_client',0)->get();
            foreach($companies as $company){
                $output = $this->calculateScore($company);
            }
        }catch(\Exception $e){
            \Log::error("Error: " . $e->getMessage());
        }
    }
    private function calculateScore($company){
        if($company->industry){
            $industryScore = $headcountScore = $revenueScore = $networkOverlapScore = $matchingScore = $activityLevelScore = 0;
            $client = Company::where('industry',$company->industry)->where('existing_client',1)->first();
            if($client){
                $locationMatched = $this->isLocationMatched($company,$client);
                $activityLevelScore = $this->calculateActivityLevelScore($company);
                $networkOverlapScore = $this->calculateNetworkingScore($company,$client);
                $industryScore = 30;
                $somClassification = CompanyCompanyClassification::where('company_id',$company->id)->where(function($q){
                    $q->where('company_classification_id',3)->orWhere('company_classification_id',6); // SOM or SOM - 4
                })->first();
                $samClassification = CompanyCompanyClassification::where('company_id',$company->id)->where(function($q){
                    $q->where('company_classification_id',2)->orWhere('company_classification_id',5); // SAM or SAM - 4
                })->first();
                if($somClassification){
                    $industryScore = 20;
                    $headcountScore = 20;
                    $revenueScore = 20;
                }else if($samClassification){
                    $industryScore = 10;
                    $headcountScore = 10;
                    $revenueScore = 10;
                }
                $matchingScore += 20;
                if($company->headcount == $client->headcount){
                    $matchingScore += 20;
                }
                if($company->revenue == $client->revenue){
                    $matchingScore += 20;
                }
                if($company->country == $client->country){
                    $matchingScore += 20;
                }
                $company->client_id = $client->id;
                $company->industry_similarity_score = $industryScore;
                $company->headcount_similarity_score = $headcountScore;
                $company->location_matched = $locationMatched;
                $company->revenue_similarity_score = $revenueScore;
                $company->activity_level_score = $activityLevelScore;
                $company->network_overlap_score = $networkOverlapScore;
                $company->general_matching_score = ($matchingScore + $networkOverlapScore);
                $company->save();
            }
        }
    }
    private function isLocationMatched($company,$client){
        return ($company->country == $client->country) ? 'yes' : 'no';
    }
    private function calculateActivityLevelScore($company){
        $companyContacts = $company->contacts()->pluck('id')->toArray();
        if(count($companyContacts) == 0){
            return 0;
        }
        $interactionActivities = Activity::whereIn('contact_id',$companyContacts)->whereIn('action',["Like","Comment","Share"])->get()->count();
        $publicActivities = Activity::whereIn('contact_id',$companyContacts)->where('type','Article')->get()->count();
        $interactionActivities = (($interactionActivities > 3 ? 3 : $interactionActivities) * 10);
        $publicActivities = (($publicActivities > 2 ? 2 : $publicActivities) * 10);
        return ($interactionActivities + $publicActivities);
    }
    private function calculateNetworkingScore($company,$client){
        $commonConnections = $commonConnectionScore = $commonOrganizations = $commonOrganizationScore = 0;
        // $sharedContactScores = Setting::where('site_key','shared_contacts_score')->pluck('site_value')->first();
        // $sharedOrganizationScores = Setting::where('site_key','shared_organizations_score')->pluck('site_value')->first();
        // $sharedContactScores = $sharedContactScores ? json_decode($sharedContactScores,true) : [];
        // $sharedOrganizationScores = $sharedOrganizationScores ? json_decode($sharedOrganizationScores,true) : [];
        $companyContacts = $company->contacts()->pluck('id')->toArray();
        $clientContacts = $client->contacts()->pluck('id')->toArray();
        $companyConnections = ContactConnection::whereIn('contact_id',$companyContacts)->pluck('connection_id')->toArray();
        $clientConnections = ContactConnection::whereIn('contact_id',$clientContacts)->pluck('connection_id')->toArray();
        $commonConnections = array_intersect($companyConnections,$clientConnections);
        $commonConnections = count($commonConnections);
        return ($commonConnections >= 2) ? 20 : ($commonConnections * 10);
        // if(count($sharedContactScores) > 0){
        //     foreach($sharedContactScores as $sharedContactScore){
        //         if(count($commonConnections) >= $sharedContactScore['min'] && count($commonConnections) <= $sharedContactScore['max']){
        //             if($sharedContactScore['score'] > 0){
        //                 $commonConnectionScore = (int) $sharedContactScore['score'];
        //             }
        //             break;
        //         }
        //     }
        // }
        // if(count($sharedOrganizationScores) > 0){
        //     foreach($sharedOrganizationScores as $sharedOrganizationScore){
        //         if(count($commonOrganizations) >= $sharedOrganizationScore['min'] && count($commonOrganizations) <= $sharedOrganizationScore['max']){
        //             if($sharedOrganizationScore['score'] > 0){
        //                 $commonOrganizationScore = (int) $sharedOrganizationScore['score'];
        //             }
        //             break;
        //         }
        //     }
        // }
    }
}