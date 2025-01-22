<?php
namespace App\Jobs;
use App\Models\Company;
use App\Models\CompanyClassification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClassifyCompaniesJob implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;
    protected $companyIds;

    /**
     * Create a new job instance.
     *
     * @param array $companyIds
     * @return void
     */
    public function __construct(array $companyIds){
        $this->companyIds = $companyIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){
        $companies = Company::whereIn('id',$this->companyIds)->get();
        foreach($companies as $company){
            $classifications = CompanyClassification::all();
            foreach($classifications as $classification){
                $wz_codes = $classification->wz_codes;
                $negative_wz_codes = $classification->negative_wz_codes;
                if(!empty($negative_wz_codes) && collect($negative_wz_codes)->contains(function($negative_wz_code) use ($company){
                    return $company->wz_code == $negative_wz_code;
                })){
                    continue;
                }
                $matchesRevenue = $company->revenue >= $classification->revenue_threshold && $company->revenue <= $classification->revenue_max;
                $matchesHeadcount = $company->headcount >= $classification->employee_threshold && $company->headcount <= $classification->employee_max;
                $matcheswz_code = empty($wz_codes) || collect($wz_codes)->contains(function($wz_code) use ($company){
                    return strpos($company->wz_code,$wz_code) === 0;
                });
                if(strpos($classification->name, 'Oversized') !== false){
                    if(($matchesRevenue || $matchesHeadcount) && $matcheswz_code){
                        $company->classifications()->syncWithoutDetaching([$classification->id]);
                    }
                }else{
                    if($matchesRevenue && $matchesHeadcount && $matcheswz_code){
                        $company->classifications()->syncWithoutDetaching([$classification->id]);
                        if($classification->id == 2 || $classification->id == 3){
                            $company->classifications()->syncWithoutDetaching([1]);
                            if($classification->id == 3){
                                $company->classifications()->detach([2]);
                            }
                        }else if($classification->id == 5 || $classification->id == 6){
                            $company->classifications()->syncWithoutDetaching([4]);
                            if($classification->id == 6){
                                $company->classifications()->detach([5]);
                            }
                        }
                    }
                }
                // $matchesNaicsCode = empty($naicsCodes) || collect($naicsCodes)->contains(function($naicsCode) use ($company){
                //     return strpos($company->naics_code, $naicsCode) === 0;
                // });
                // if($matchesRevenue && $matchesHeadcount && $matchesNaicsCode){
                //     $company->classifications()->syncWithoutDetaching([$classification->id]);
                // }
            }
        }
    }
}