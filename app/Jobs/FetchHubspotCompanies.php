<?php
namespace App\Jobs;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Models\Company;

class FetchHubspotCompanies implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;
    public $afterToken;
    public $count = 0;
    public function __construct($afterToken = null){
        $this->afterToken = $afterToken;
    }
    public function handle(){
        $baseUrl = 'https://api.hubapi.com/crm/v3/objects/companies?limit=100';
        $client = new Client();
        $url = $this->afterToken ? $baseUrl . '&after=' . $this->afterToken : $baseUrl;
        try{
            $response = $client->get($url,['headers' => ['Authorization' => "Bearer ".env("HUBSPOT_API_KEY"),'Content-Type' => 'application/json']]);
            $data = json_decode($response->getBody()->getContents(),true);
            foreach($data['results'] as $company){
                // $companyExist = Company::where("legal_name",$company["properties"]["name"])->whereNull("hubspot_id")->first();
                // if($companyExist){
                //     ++$this->count;
                // }
                // if(!empty($company["properties"]["domain"])){
                //     $companyExist = Company::where("domain",$company["properties"]["domain"])->first();
                //     if($companyExist){
                //         $companyExist->hubspot_id = $company["id"];
                //         $companyExist->save();
                //     }
                // }
                // $companyExist = Company::where("name",$company["properties"]["name"])->first();
                // if($companyExist){
                //     $companyExist->hubspot_id = $company["id"];
                //     $companyExist->save();
                // }
            }
            if(isset($data['paging']['next']['after'])){
                self::dispatch($data['paging']['next']['after']);
            }else{
                Log::info('Total companies fetched: ' . $this->count);
            }
        }catch(\Exception $e){
            Log::error('Error fetching HubSpot companies: ' . $e->getMessage());
        }
    }
}