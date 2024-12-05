<?php
set_time_limit(0);
ini_set('display_errors', 1);
ini_set('memory_limit','1024M');

$data = file_get_contents("companies.json");
$companies = json_decode($data);
$company = $companies[0];
echo $company->name."<br>";
echo 'Total Contacts: '.count($company->contacts)."<br>";
echo "<pre>";
print_r($company->contacts[0]);
die();

$start = 0;
$companies = [];
$totalCompanies = 0;
function fetchCompanies($from = 0,$limit = 20){
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
        CURLOPT_HTTPHEADER => [
            'cache-control: no-cache',
            'content-type: application/json',
            'cookie: _lr_uf_-cognism=18c8f2f5-841e-4be0-a8c3-29f09b3e46c8; __stripe_mid=2eeebdfa-2065-4ed9-8f9d-8b66df77930044c350; cognism.session=eyJhbGciOiJIUzI1NiJ9.eyJkYXRhIjp7ImVtYWlsIjoicGhpbGlwcC5kZXRsb2ZmQHByb2dyZXNzbWFrZXIuaW8iLCJzZXNzaW9uIjoiVXNlci1QLTVlNDQyMGZlLTI0NzQtNDdlNC1hNGZiLTk4OTJkNDRlMTg1NCIsImFwcCI6IkFQUCIsImFjY291bnQiOiJwcm9ncmVzc21ha2VyIiwibG9naW5UeXBlIjoiU1RBTkRBUkRfTE9HSU4iLCJzd2l0Y2hlciI6IiJ9LCJleHAiOjE3MjU5NTYxNjksIm5iZiI6MTcyNTUyNDE2OSwiaWF0IjoxNzI1NTI0MTY5fQ.gpUvuD3R464cad_UYvHOZxU8M_Er76jwdSIoAyQQ2Bw; __zlcmid=1Nbmzsgfy3ObggB; _gid=GA1.2.1391605726.1725620115; _gat_gtag_UA_141260460_2=1; _ga_TMK8TGC1K9=GS1.1.1725620114.3.1.1725620847.0.0.0; _ga=GA1.1.1446625191.1725524134; _lr_tabs_-cognism%2Fapp-production-a0zcv={%22sessionID%22:0%2C%22recordingID%22:%225-27c177be-36cb-4dc4-b712-1d90cedcda57%22%2C%22lastActivity%22:1725620847739}; _lr_hb_-cognism%2Fapp-production-a0zcv={%22heartbeat%22:1725620847740}'
        ]
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response);
}
function fetchCompanyContacts($companyId,$from = 0,$limit = 100){
    $url = 'https://app.cognism.com/api/graph/person/search?indexFrom='.$from.'&indexSize='.$limit;
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
        CURLOPT_POSTFIELDS =>'{
            "company": {
                "ids": ["'.$companyId.'"],
                "options": {
                    "match_exact_company_name": false,
                    "match_exact_domain": false,
                    "filter_domain": "exists",
                    "location_Type": "ALL",
                    "events_operator": "OR",
                    "sort_fields": ["weight","revenue"],
                    "merge_industries": false,
                    "include_events": false,
                    "show_max_events": 100,
                    "show_max_techs": 0
                }
            },
            "options": {
                "match_exact_job_title": false,
                "show_company_events": true,
                "show_contact_data": false,
                "ai_job_title": true,
                "sort_fields": ["com.profile_score;DESC","com.email.src.at;DESC"],
                "operators": {}
            },
            "icpSearch": []
        }',
        CURLOPT_HTTPHEADER => [
            'cache-control: no-cache',
            'content-type: application/json',
            'cookie: _lr_uf_-cognism=18c8f2f5-841e-4be0-a8c3-29f09b3e46c8; __stripe_mid=2eeebdfa-2065-4ed9-8f9d-8b66df77930044c350; cognism.session=eyJhbGciOiJIUzI1NiJ9.eyJkYXRhIjp7ImVtYWlsIjoicGhpbGlwcC5kZXRsb2ZmQHByb2dyZXNzbWFrZXIuaW8iLCJzZXNzaW9uIjoiVXNlci1QLTVlNDQyMGZlLTI0NzQtNDdlNC1hNGZiLTk4OTJkNDRlMTg1NCIsImFwcCI6IkFQUCIsImFjY291bnQiOiJwcm9ncmVzc21ha2VyIiwibG9naW5UeXBlIjoiU1RBTkRBUkRfTE9HSU4iLCJzd2l0Y2hlciI6IiJ9LCJleHAiOjE3MjU5NTYxNjksIm5iZiI6MTcyNTUyNDE2OSwiaWF0IjoxNzI1NTI0MTY5fQ.gpUvuD3R464cad_UYvHOZxU8M_Er76jwdSIoAyQQ2Bw; __zlcmid=1Nbmzsgfy3ObggB; _gid=GA1.2.1391605726.1725620115; _ga_TMK8TGC1K9=GS1.1.1725620114.3.1.1725620847.0.0.0; _ga=GA1.1.1446625191.1725524134; _lr_hb_-cognism%2Fapp-production-a0zcv={%22heartbeat%22:1725621447239}; _lr_tabs_-cognism%2Fapp-production-a0zcv={%22sessionID%22:0%2C%22recordingID%22:%225-27c177be-36cb-4dc4-b712-1d90cedcda57%22%2C%22lastActivity%22:1725621550790}'
        ]
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response);
}
do{
    $coms = fetchCompanies($start,1);
    if(isset($coms->totalResults)){
        $totalCompanies = $coms->totalResults;
        if(isset($coms->results) && count($coms->results)){
            foreach($coms->results as $company){
                $contacts = [];
                $cStart = 0;
                $totalContacts = 0;
                do{
                    // $comContacts = fetchCompanyContacts($company->id,$cStart,100);
                    if(isset($comContacts->totalResults)){
                        $totalContacts = $comContacts->totalResults;
                        if(isset($comContacts->results) && count($comContacts->results)){
                            $contacts = array_merge($contacts,$comContacts->results);
                        }
                        $cStart += 100;
                    }
                    echo count($contacts)."\n";

                }while(count($contacts) < $totalContacts);
                $company->contacts = $contacts;
                array_push($companies,$company);
            }
        }
        $start += 100;
    }
    break;
}while(count($companies) <= $totalCompanies);
file_put_contents("companies.json",json_encode($companies));