<?php
namespace App\Jobs;
use App\Models\Company;
use App\Models\CompanyClassification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ImportCognismCompanies implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;
    public $timeout = 1000000;
    protected $country;
    protected $fileName;
    public function __construct($country,$fileName){
        $this->country = $country;
        $this->fileName = $fileName;
    }
    public function handle(){
        ini_set('memory_limit','3G');
        $data = Storage::disk('local')->get($this->fileName);
        $companies = json_decode($data,true);
        \Log::info('Importing ' . count($companies) . ' companies from ' . $this->country . ' file ' . $this->fileName);
        foreach($companies as $companyData){
            try{
                $company = Company::withTrashed()->where('name',$companyData['name'])->where('country',$this->country)->first();
                if(!$company){
                    $company = new Company();
                }
                if(isset($companyData['name']) && !empty($companyData['name'])){
                    $company->name = $companyData['name'];
                }
                if(isset($companyData['domain']) && !empty($companyData['domain'])){
                    $company->domain = $companyData['domain'];
                }
                if(isset($companyData['li_industry']) && !empty($companyData['li_industry'])){
                    $company->industry = $companyData['li_industry'];
                }
                if(isset($companyData['headcounts']) && !empty($companyData['headcounts'])){
                    $company->headcount = $companyData['headcounts'];
                }
                if(isset($companyData['revenue']) && !empty($companyData['revenue'])){
                    $companyData['revenue'] = round($companyData['revenue'] ? $companyData['revenue'] / 1000000 : 0,2);
                    $company->revenue = $companyData['revenue'];
                }
                if(isset($companyData['desc']) && !empty($companyData['desc'])){
                    $company->description = $companyData['desc'];
                }
                if(isset($companyData['sic']) && count($companyData['sic']) > 0 && empty($company->wz_code)){
                    $company->wz_code = $companyData['sic'][count($companyData['sic']) - 1];
                }
                if(isset($companyData['founded'])){
                    $company->founded = $companyData['founded'];
                }
                if(isset($companyData['type'])){
                    $company->type = $companyData['type'];
                }
                if(isset($companyData['sic']) && count($companyData['sic'])){
                    $company->sics = json_encode($companyData['sic']);
                }
                if(isset($companyData['naics']) && count($companyData['naics'])){
                    $company->naics = json_encode($companyData['naics']);
                }
                if(isset($companyData['tech']) && count($companyData['tech'])){
                    $company->technologies = json_encode($companyData['tech']);
                }
                if(isset($companyData['locations']) && count($companyData['locations'])){
                    $company->locations = json_encode($companyData['locations']);
                }
                if(isset($companyData['datapoints']) && count($companyData['datapoints'])){
                    $company->datapoints = json_encode($companyData['datapoints']);
                }
                $company->cognism_id = $companyData['id'];
                $company->country = $this->country;
                $company->cognism_response = json_encode($companyData);
                $company->save();
            }catch(\Exception $e){
                echo $companyData['name'] . ' ' . $e->getMessage() . '<br>';
                continue;
            }
        }
    }
}