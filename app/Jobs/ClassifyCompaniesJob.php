<?php
namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use App\Models\QuizResponse;
use App\Models\CompanyClassification;
use App\Models\WzCodesNaicsMapping;

class ClassifyCompaniesJob implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;
    protected $companyIds;
    protected $classifications;

    /**
     * Create a new job instance.
     *
     * @param array $companyIds
     * @return void
     */
    public function __construct(array $companyIds){
        $this->companyIds = $companyIds;
        $this->excludeClass = ['TAM','SAM','SOM','TAM - 4','SAM - 4','SOM - 4'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){
        $companies = Company::withTrashed()->whereIn('id',$this->companyIds)->get();
        foreach($companies as $company){
            $classifications = CompanyClassification::all();
            $largeCorporation = QuizResponse::where('company_id',$company->id)->where('question_id',1)->pluck('answer')->first();
            $companyIndependent = QuizResponse::where('company_id',$company->id)->where('question_id',2)->pluck('answer')->first();
            foreach($classifications as $classification){
                $wz_codes = $classification->wz_codes;
                $negative_wz_codes = $classification->negative_wz_codes;
                $matchesRevenue = $company->revenue >= $classification->revenue_threshold && $company->revenue <= $classification->revenue_max;
                $matchesHeadcount = $company->headcount >= $classification->employee_threshold && $company->headcount <= $classification->employee_max;

                // $naicsCodes = $company->naics ? json_decode($company->naics,true) : [];
                // if(count($naicsCodes)){
                //     foreach($naicsCodes as $naicsCode){
                //         $wzCodeNaicsMapping = WzCodesNaicsMapping::where('naics_codes',$naicsCode)->first();
                //         if($wzCodeNaicsMapping){
                //             // if(!empty($negative_wz_codes) && collect($negative_wz_codes)->contains(function($negative_wz_code) use ($wzCodeNaicsMapping){
                //             //     return $wzCodeNaicsMapping->wz_codes == $negative_wz_code;
                //             // })){
                //             //     continue;
                //             // }
                //             $matchesWzNaicsCode = empty($wz_codes) || collect($wz_codes)->contains(function($wz_code) use ($wzCodeNaicsMapping){
                //                 return strpos($wzCodeNaicsMapping->wz_codes,$wz_code) === 0;
                //             });
                //             if(strpos($classification->name, 'Oversized') !== false){
                //                 if(($matchesRevenue || $matchesHeadcount) && $matchesWzNaicsCode){
                //                     $company->classifications()->syncWithoutDetaching([$classification->id]);
                //                 }
                //             }else{
                //                 if($matchesRevenue && $matchesHeadcount && $matchesWzNaicsCode){
                //                     $company->classifications()->syncWithoutDetaching([$classification->id]);
                //                     if($classification->id == 2 || $classification->id == 3){
                //                         $company->classifications()->syncWithoutDetaching([1]);
                //                         if($classification->id == 3){
                //                             $company->classifications()->detach([2]);
                //                         }
                //                     }else if($classification->id == 5 || $classification->id == 6){
                //                         $company->classifications()->syncWithoutDetaching([4]);
                //                         if($classification->id == 6){
                //                             $company->classifications()->detach([5]);
                //                         }
                //                     }
                //                 }
                //             }
                //         }
                //     }
                // }
                if($company->wz_code){
                    if(!empty($negative_wz_codes) && collect($negative_wz_codes)->contains(function($negative_wz_code) use ($company){
                        return $company->wz_code == $negative_wz_code;
                    })){
                        continue;
                    }
                    $matcheswz_code = empty($wz_codes) || collect($wz_codes)->contains(function($wz_code) use ($company){
                        return strpos($company->wz_code,$wz_code) === 0;
                    });
                    // if(in_array($classification->name,$this->excludeClass) && $largeCorporation == 'yes' && $companyIndependent == 'no'){
                    //     continue;
                    // }
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
}