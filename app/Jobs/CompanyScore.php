<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use App\Models\CompanyCompanyClassification;

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
            $industryScore = $headcountScore = $revenueScore = $networkOverlapScore = 0;
            $sameClient = Company::where('industry',$company->industry)->where('existing_client',1)->first();
            if($sameClient){
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
                }
                if($samClassification){
                    $industryScore = 10;
                    $headcountScore = 10;
                    $revenueScore = 10;
                }
                $company->industry_score = $industryScore;
                $company->headcount_score = $headcountScore;
                $company->location_match = (($company->country == $sameClient->country) ? 'yes' : 'no');
                $company->revenue_score = $revenueScore;
                $company->network_overlap_score = $networkOverlapScore;
                $company->total_score = ($industryScore + $headcountScore + $revenueScore + $networkOverlapScore);
                $company->save();
            }
        }
    }
}