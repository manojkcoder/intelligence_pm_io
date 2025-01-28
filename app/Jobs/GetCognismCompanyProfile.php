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
    public function __construct($companyId){
        $this->companyId = $companyId;
    }
    public function handle(): void{
        \Log::info('Fetching company Profile #'.$this->companyId);
        $companyData = $this->fetchCompanyProfile($this->companyId);
        \Log::info('Fetched company Profile '.print_r($companyData,true));
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
                if(isset($companyData['loc']) && isset($companyData['loc']['country']) && !empty($companyData['loc']['country'])){
                    $company->country = $companyData['loc']['country'];
                }
                $company->cognism_id = $companyData['id'];
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
              'cookie: __stripe_mid=70be145a-80eb-4ab0-9af0-56df43b1c18afb71f1; __zlcmid=1PrnLAv5PETJ3De; mutiny.user.token=76aae6f2-b5c7-4207-8dfe-59558677217b; _vwo_uuid_v2=D57B1B1288F95DC7A2C2BE50E369D076E|a7e1f047cde8a8ad9c9655f9bb91949d; _gcl_au=1.1.2123059256.1737707566; ajs_user_id=null; ajs_group_id=null; ajs_anonymous_id=%2222310e5e-4cea-444f-930b-fee5c3d234eb%22; sbjs_migrations=1418474375998%3D1; sbjs_first_add=fd%3D2025-01-24%2014%3A02%3A48%7C%7C%7Cep%3Dhttps%3A%2F%2Fwww.cognism.com%2F%7C%7C%7Crf%3Dhttps%3A%2F%2Fwww.google.com%2F; sbjs_current=typ%3Dorganic%7C%7C%7Csrc%3Dgoogle%7C%7C%7Cmdm%3Dorganic%7C%7C%7Ccmp%3D%28none%29%7C%7C%7Ccnt%3D%28none%29%7C%7C%7Ctrm%3D%28none%29; sbjs_first=typ%3Dorganic%7C%7C%7Csrc%3Dgoogle%7C%7C%7Cmdm%3Dorganic%7C%7C%7Ccmp%3D%28none%29%7C%7C%7Ccnt%3D%28none%29%7C%7C%7Ctrm%3D%28none%29; _fbp=fb.1.1737707569019.881086762880412334; hubspotutk=1e0859ab79a10824aa295a235fe9ec67; _ga_464KJQVP8W=GS1.1.1737712613.3.0.1737712613.60.0.0; _rdt_uuid=1737707566255.620dc3e6-7c8a-4496-8a5e-ed672eea832b; sbjs_current_add=fd%3D2025-01-24%2015%3A26%3A55%7C%7C%7Cep%3Dhttps%3A%2F%2Fwww.cognism.com%2F%7C%7C%7Crf%3Dhttps%3A%2F%2Fwww.google.com%2F; sbjs_udata=vst%3D2%7C%7C%7Cuip%3D%28none%29%7C%7C%7Cuag%3DMozilla%2F5.0%20%28Windows%20NT%2010.0%3B%20Win64%3B%20x64%29%20AppleWebKit%2F537.36%20%28KHTML%2C%20like%20Gecko%29%20Chrome%2F132.0.0.0%20Safari%2F537.36; _hjSessionUser_2622162=eyJpZCI6ImY5YWYxMmU1LWQ1ZDctNTA2YS04N2Q0LWE4MGUyYWI1YmRmNSIsImNyZWF0ZWQiOjE3Mzc3MDc1Njk4NTIsImV4aXN0aW5nIjp0cnVlfQ==; _uetvid=cbc2df80da2d11efa4886ffcc18f2d54; __q_state_5KE73Jag5LbR5m3T=eyJ1dWlkIjoiNzk3OThiNDctODZiNC00YmZhLTg0ODUtZGZlNTViOTc5N2RmIiwiY29va2llRG9tYWluIjoiY29nbmlzbS5jb20iLCJhY3RpdmVTZXNzaW9uSWQiOm51bGwsInNjcmlwdElkIjoiMTU2MTIwMDYwODY5ODA4MjI0MCIsInN0YXRlQnlTY3JpcHRJZCI6eyIxNTYxMjAwNjA4Njk4MDgyMjQwIjp7ImRpc21pc3NlZCI6bnVsbCwic2Vzc2lvbklkIjpudWxsfX0sIm1lc3NlbmdlckV4cGFuZGVkIjpudWxsLCJwcm9tcHREaXNtaXNzZWQiOnRydWUsImNvbnZlcnNhdGlvbklkIjoiMTU3NjQ5NzcxMzYyOTA4NjY2OSJ9; __hstc=70525647.1e0859ab79a10824aa295a235fe9ec67.1737707570052.1737707570052.1737712616304.2; _lr_uf_-cognism=971f2dab-977c-4b58-83ab-d489352152d1; _gid=GA1.2.567162652.1737959367; _gat_gtag_UA_141260460_2=1; _ga_TMK8TGC1K9=GS1.1.1737959366.14.1.1737959367.0.0.0; _ga=GA1.1.863484502.1737619232; _lr_hb_-cognism%2Fapp-production-a0zcv={%22heartbeat%22:1737959367534}; cognism.session=eyJhbGciOiJIUzI1NiJ9.eyJkYXRhIjp7Im5hbWUiOiJQaGlsaXBwIERldGxvZmYiLCJlbWFpbCI6InBoaWxpcHAuZGV0bG9mZkBwcm9ncmVzc21ha2VyLmlvIiwicm9sZXMiOiJVU0VSIiwic3dpdGNoZXIiOiIiLCJzZXNzaW9uIjoiVXNlci1QLTY4MmFhOGU0LTc3NDItNDkyZi1iN2RjLTU1NWI3M2Q5MWM4NSIsImFwcCI6IkFQUCIsImFjY291bnQiOiJwcm9ncmVzc21ha2VyIiwibG9naW5UeXBlIjoiU1RBTkRBUkRfTE9HSU4ifSwiZXhwIjoxNzM4MzkxMzcxLCJuYmYiOjE3Mzc5NTkzNzEsImlhdCI6MTczNzk1OTM3MX0.sNh-eiMylOba0mtmo_ixekI_kf_v7cdOsSXbfTgjqhY; _lr_tabs_-cognism%2Fapp-production-a0zcv={%22recordingID%22:%226-0194a673-6020-7f3a-bd9f-cf48c42b087d%22%2C%22sessionID%22:0%2C%22lastActivity%22:1737959374046%2C%22hasActivity%22:true}; __stripe_sid=96580e94-65c0-425c-822a-d7d395f52c38443adb',
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
