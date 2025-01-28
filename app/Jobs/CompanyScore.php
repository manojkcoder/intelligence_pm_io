<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Setting;

class CompanyScore implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    private $id;
    public function __construct($id){
        $this->id = $id;
    }
    public function handle(): void{
        try{
            $company = Company::withTrashed()->where('id',$this->id)->first();
            if($company){

            }
        }catch(\Exception $e){
            \Log::error("Error: " . $e->getMessage());
        }
    }
}
// industry_score
// headcount_score
// location_match
// revenue_score
// network_overlap_score
// total_score