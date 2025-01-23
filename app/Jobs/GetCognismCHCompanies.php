<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GetCognismCHCompanies implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;
    public $timeout = 100000;
    public function __construct(){
        //
    }
    public function handle(): void{
        \Log::info('Fetching Switzerland companies');
        $start = 0;
        $totalCompanies = 0;
        $perRequest = 100;
        $path = Storage::path('companies/ch-all.json');
        $fileExists = file_exists($path);
        if(!file_exists(dirname($path))){
            mkdir(dirname($path),0777,true);
        }
        $file = fopen($path,'c+');
        if($file === false){
            \Log::error('Failed to open file for appending');
            return;
        }
        if($fileExists){
            fseek($file,-1,SEEK_END);
            fwrite($file,',');
        }else{
            fwrite($file,'[');
        }
        $firstRecord = !$fileExists;
        do{
            $coms = $this->fetchCompanies($start,$perRequest);
            if(isset($coms->totalResults)){
                $totalCompanies = $coms->totalResults;
                if(isset($coms->results) && count($coms->results)){
                    foreach($coms->results as $index => $company){
                        GetCognismCompanyContacts::dispatch($company->id)->onQueue('cognism');
                        if(!$firstRecord){
                            fwrite($file,',' . PHP_EOL);
                        }
                        fwrite($file,json_encode($company));
                        $firstRecord = false;
                    }
                }
                $start += $perRequest;
            }
        }while($start < $totalCompanies);
        fwrite($file,']');
        fclose($file);
        \Log::info('Switzerland Companies fetched and written to file');
    }
    public function fetchCompanies($from = 0,$limit = 20){
        \Log::info('Fetching Switzerland companies from '.$from.' to '.($from+$limit));
        $url = 'https://app.cognism.com/api/graph/company/search?indexFrom='.$from.'&indexSize='.$limit;
        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "revenue": {"from": 100000000},
                "multiOfficeLocations": {"includeCountries": ["Switzerland"]},
                "excludeIndustries": ["Religious Institutions","Political Organization","Government Administration","Civil Engineering","Architecture & Planning","Hospitality","Hospital & Health Care","Civic & Social Organization"],
                "sizes": ["D","E","G","F","H","I"],
                "options": {"match_exact_company_name": false,"match_exact_domain": false,"filter_domain": "exists","location_Type": "ALL","events_operator": "OR","sort_fields": ["weight","revenue"],"merge_industries": false,"include_events": false,"show_max_events": 100,"operators": {},"show_max_techs": 0}
            }',
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
        return json_decode($response);
    }
}