<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Models\Company;

class GetCognismCompanyProfile implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;
    public $timeout = 100000;
    private $companyId;
    private $country;
    public function __construct($companyId,$country){
        $this->companyId = $companyId;
        $this->country = $country;
    }
    public function handle(): void{
        \Log::info('Fetching company Profile #'.$this->companyId);
        $companyData = $this->fetchCompanyProfile($this->companyId);
        if(isset($companyData) && !empty($companyData)){
            try{
                $company = Company::withTrashed()->where('name',$companyData['name'])->first();
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
                \Log::error($companyData['name'] . ' ' . $e->getMessage());
            }
        }
    }
    public function fetchCompanyProfile($companyId){
        $url = 'https://app.cognism.com/api/graph/company/'.$companyId.'?employeeCount=true&includeEvents=false';
        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
              'accept: application/json, text/plain, */*',
              'accept-language: en-GB,en-IN;q=0.9,en-US;q=0.8,en;q=0.7',
              'content-type: application/json',
              'cookie: _gid=GA1.2.376773486.1737619232; _lr_uf_-cognism=721c09e0-581b-4fcf-8ab1-4df164554f35; __stripe_mid=70be145a-80eb-4ab0-9af0-56df43b1c18afb71f1; __zlcmid=1PrnLAv5PETJ3De; __stripe_sid=552a134d-bc79-4c02-8b10-62c478c7505a2dd83b; cognism.session=eyJhbGciOiJIUzI1NiJ9.eyJkYXRhIjp7Im5hbWUiOiJQaGlsaXBwIERldGxvZmYiLCJlbWFpbCI6InBoaWxpcHAuZGV0bG9mZkBwcm9ncmVzc21ha2VyLmlvIiwicm9sZXMiOiJVU0VSIiwic3dpdGNoZXIiOiIiLCJzZXNzaW9uIjoiVXNlci1QLTI4MmI1YmIyLTY3ZDgtNGU1OS05MjQ1LTk5OTRhNjIwNmMyNCIsImFwcCI6IkFQUCIsImFjY291bnQiOiJwcm9ncmVzc21ha2VyIiwibG9naW5UeXBlIjoiU1RBTkRBUkRfTE9HSU4ifSwiZXhwIjoxNzM4MDU3NzM1LCJuYmYiOjE3Mzc2MjU3MzUsImlhdCI6MTczNzYyNTczNX0.hW1J-PyII_RjuRQysuX_vymVkdnYtnMAk6DLtWK61zg; _lr_hb_-cognism%2Fapp-production-a0zcv={%22heartbeat%22:1737626344758}; _ga_TMK8TGC1K9=GS1.1.1737625392.2.1.1737626344.0.0.0; _ga=GA1.1.863484502.1737619232; _lr_tabs_-cognism%2Fapp-production-a0zcv={%22recordingID%22:%226-01949299-dd34-71ff-88ab-bc5b34ca97a9%22%2C%22sessionID%22:0%2C%22lastActivity%22:1737626350545%2C%22hasActivity%22:true}',
              'dnt: 1',
              'origin: https://app.cognism.com',
              'priority: u=1, i',
              'sec-ch-ua: "Chromium";v="128", "Not;A=Brand";v="24", "Google Chrome";v="128"',
              'sec-ch-ua-mobile: ?0',
              'sec-ch-ua-platform: "macOS"',
              'sec-fetch-dest: empty',
              'sec-fetch-mode: cors',
              'sec-fetch-site: same-origin',
              'traceparent: 00-46b21fec15c92a6e2ce58f5b8d170252-cce4470e2bf76b87-01',
              'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
              'x-cognism-client: Search',
              'x-cognism-client-version: 0.1380.0'
            ),
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response,true);
    }
}
