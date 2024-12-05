<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;


class GetCognismCompanies implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::info('Fetching companies');
        $start = 0;
        $companies = [];
        $totalCompanies = 0;
        $perRequest = 100;
        do{
            $coms = $this->fetchCompanies($start,$perRequest);
            if(isset($coms->totalResults)){
                $totalCompanies = $coms->totalResults;
                if(isset($coms->results) && count($coms->results)){
                    foreach($coms->results as $company){
                        GetCognismCompanyContacts::dispatch($company->id);
                        array_push($companies,$company);
                    }
                }
                $start += $perRequest;
            }
        }while(count($companies) < $totalCompanies);
        $path = Storage::path('companies/all.json');
        if(!file_exists(dirname($path))){
            mkdir(dirname($path),0777,true);
        }
        file_put_contents($path,json_encode($companies));
    }


    public function fetchCompanies($from = 0,$limit = 20){
        \Log::info('Fetching companies from '.$from.' to '.($from+$limit));
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
                "revenue": {"from": 100000000,"to":10000000000},
                "multiHqLocations": {"includeCountries": ["Germany"]},
                "multiOfficeLocations": {"includeCountries": ["Germany"]},
                "options": {"match_exact_company_name": false,"match_exact_domain": false,"filter_domain": "exists","location_Type": "ALL","events_operator": "OR","sort_fields": ["weight","revenue"],"merge_industries": false,"include_events": false,"show_max_events": 100,"operators": {},"show_max_techs": 0},
                "headcount": {"from": 500,"to": 10000000}
            }',
            CURLOPT_HTTPHEADER => array(
              'accept: application/json, text/plain, */*',
              'accept-language: en-GB,en-IN;q=0.9,en-US;q=0.8,en;q=0.7',
              'content-type: application/json',
              'cookie: _lr_uf_-cognism=ef6f2943-1678-4ce9-90f8-d895f267d26f; __stripe_mid=4ebc2b50-51fd-430b-b2ec-f34a0035409f16bf7f; __zlcmid=1Ncn01prRiYFEhM; cognism.session=eyJhbGciOiJIUzI1NiJ9.eyJkYXRhIjp7ImVtYWlsIjoicGhpbGlwcC5kZXRsb2ZmQHByb2dyZXNzbWFrZXIuaW8iLCJyb2xlcyI6IlsnVXNlciddIiwic2Vzc2lvbiI6IlVzZXItUC05YjM2MDIzZS1iMDZhLTRjOWUtYjMzZS1lYWQ3M2ZmYmY2ZjciLCJhcHAiOiJBUFAiLCJhY2NvdW50IjoicHJvZ3Jlc3NtYWtlciIsImxvZ2luVHlwZSI6IlNUQU5EQVJEX0xPR0lOIiwic3dpdGNoZXIiOiIifSwiZXhwIjoxNzI3ODY4ODE5LCJuYmYiOjE3Mjc0MzY4MTksImlhdCI6MTcyNzQzNjgxOX0.yUNy1C8hG3YlDec3l9dbjvMOu6Mwed46m2Ct4023CpM; _gid=GA1.2.342003547.1727604976; __stripe_sid=2ea4fbd2-48bc-4c71-aff1-c45ae51d7769bcad13; _gat_gtag_UA_141260460_2=1; _ga_TMK8TGC1K9=GS1.1.1727604976.7.1.1727605591.0.0.0; _ga=GA1.1.416436521.1725607943; _lr_hb_-cognism%2Fapp-production-a0zcv={%22heartbeat%22:1727605591249}; _lr_tabs_-cognism%2Fapp-production-a0zcv={%22sessionID%22:0%2C%22recordingID%22:%225-1e3e86c4-f938-4fe7-aa56-b22fc430ee28%22%2C%22lastActivity%22:1727605612644}',
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
