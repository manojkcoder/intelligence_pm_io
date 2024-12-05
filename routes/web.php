<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyClassifications;
use App\Http\Controllers\ContactsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\NAICSIndustryController;
use App\Models\Company;
use App\Models\Industry;
use App\Models\Contact;
use App\Models\LikeComment;
use Illuminate\Support\Facades\DB;

Route::get('/',function(){
    return view('welcome');
});

Route::get('/dashboard',[DashboardController::class,'dashboard'])->middleware(['auth','verified'])->name('dashboard');
Route::get('/companies/all',[DashboardController::class,'companies'])->middleware(['auth','verified'])->name('companies.all');
Route::get('/stats',[DashboardController::class,'stats'])->middleware(['auth','verified'])->name('stats');
Route::any('/stats/all',[DashboardController::class,'allStats'])->middleware(['auth','verified'])->name('stats.all');

Route::get('/accounts/{id}',[DashboardController::class,'viewCompany'])->middleware(['auth','verified'])->name('viewCompany');
Route::delete('/deleteCompany/{id}',[DashboardController::class,'deleteCompany'])->middleware(['auth','verified'])->name('deleteCompany');
Route::delete('/trashedCompany/{id}',[DashboardController::class,'trashedCompany'])->middleware(['auth','verified'])->name('trashedCompany');
Route::post('/moveCompany/{id}',[DashboardController::class,'moveCompany'])->middleware(['auth','verified'])->name('moveCompany');
Route::post('/dream/{id}',[DashboardController::class,'dream'])->middleware(['auth','verified'])->name('dream');
Route::get('/accounts/edit/{id}',[DashboardController::class,'editCompany'])->middleware(['auth','verified'])->name('editCompany');
Route::patch('/accounts/edit/{id}',[DashboardController::class,'updateCompany'])->middleware(['auth','verified'])->name('updateCompany');
Route::delete('/accounts/edit/{id}',[DashboardController::class,'destroyCompany'])->middleware(['auth','verified'])->name('destroyCompany');

Route::get('/contacts',[ContactsController::class,'allContacts'])->middleware(['auth','verified'])->name('contacts.all');
Route::get('/getContacts',[ContactsController::class,'getContacts'])->middleware(['auth','verified'])->name('contacts.get');
Route::get('/accounts/{id}/contacts',[ContactsController::class,'contacts'])->middleware(['auth','verified'])->name('contacts');
Route::post('/accounts/{id}/contacts',[ContactsController::class,'createContact'])->middleware(['auth','verified'])->name('createContact');
Route::get('/accounts/{id}/contacts/edit/{contact_id}',[ContactsController::class,'editContact'])->middleware(['auth','verified'])->name('editContact');
Route::patch('/accounts/{id}/contacts/edit/{contact_id}',[ContactsController::class,'updateContact'])->middleware(['auth','verified'])->name('updateContact');
Route::delete('/accounts/{id}/contacts/edit/{contact_id}',[ContactsController::class,'deleteContact'])->middleware(['auth','verified'])->name('deleteContact');


Route::get('/classifications',[CompanyClassifications::class,'index'])->middleware(['auth','verified'])->name('classifications.index');
Route::get('/classifications/edit/{id}',[CompanyClassifications::class,'edit'])->middleware(['auth','verified'])->name('classifications.edit');
Route::patch('/classifications/update/{id}',[CompanyClassifications::class,'update'])->middleware(['auth','verified'])->name('classifications.update');
Route::delete('/classifications/delete/{id}',[CompanyClassifications::class,'destroy'])->middleware(['auth','verified'])->name('classifications.destroy');

Route::get('/industries',[IndustryController::class,'industries'])->middleware(['auth','verified'])->name('industries');
Route::get('/industries/edit/{id}',[IndustryController::class,'editIndustry'])->middleware(['auth','verified'])->name('industries.edit');
Route::patch('/industries/update/{id}',[IndustryController::class,'updateIndustry'])->middleware(['auth','verified'])->name('industries.update');
Route::post('/industries',[IndustryController::class,'updateIndustriesStatus'])->middleware(['auth','verified'])->name('industries.updateStatus');

Route::get('/naics-industries',[NAICSIndustryController::class,'industries'])->middleware(['auth','verified'])->name('naics_industries');
Route::get('/naics-industries/edit/{id}',[NAICSIndustryController::class,'editIndustry'])->middleware(['auth','verified'])->name('naics_industries.edit');
Route::patch('/naics-industries/update/{id}',[NAICSIndustryController::class,'updateIndustry'])->middleware(['auth','verified'])->name('naics_industries.update');
Route::post('/naics-industries',[NAICSIndustryController::class,'updateIndustriesStatus'])->middleware(['auth','verified'])->name('naics_industries.updateStatus');

Route::get('/wz_code_status',[DashboardController::class,'wz_code_status'])->middleware(['auth','verified'])->name('wz_code_status');

Route::get('/gpt',[DashboardController::class,'gpt'])->middleware(['auth','verified'])->name('gpt');
Route::post('/prompt',[DashboardController::class,'prompt'])->middleware(['auth','verified'])->name('prompt');


// Route::get('/collate_contacts',function(){
//     // list all folders under storage/contacts
//     $folders = Storage::directories('contacts');
//     foreach($folders as $folder){
//         $company_id = basename($folder);
//         echo 'Collating contacts for company ' . $company_id . '<br>';
//         // check if already collated
//         if(Storage::exists('contacts/'.$company_id.'.json')){
//             echo 'Contacts for company ' . $company_id . ' already collated <br>';
//             continue;
//         }
//         $contacts = [];
//         $files = Storage::files('contacts/'.$company_id);
//         foreach($files as $file){
//             $file_data = Storage::get($file);
//             $json_data = json_decode($file_data,true);
//             $contacts = array_merge($contacts, $json_data);
//         }
//         // filter duplicate contacts based on id
//         try{
//             $contacts = collect($contacts)->unique('id')->values()->all();
//             Storage::disk('local')->put('contacts/'.$company_id.'.json', json_encode($contacts));
//             echo 'Collated contacts for company ' . $company_id .', total: '.count($contacts). '<br>';
//         }catch(\Exception $e){
//             echo 'Error collating contacts for company ' . $company_id . ' ' . $e->getMessage() . '<br>';
//         }
//     }
// });


Route::get('/map_classifications',function() {
    $classifications = \App\Models\CompanyClassification::get();
    $map = Storage::get('wz_code_map.json');
    $map = json_decode($map,true);
    $wz_map = [];
    foreach($map as $value){
        $wz_map[$value['wz_code'].''] = $value['naics_codes'];
    }
    foreach($classifications as $classification){
        $nacis_codes = collect(array_map(function($wz_code) use ($wz_map){
            echo $wz_code . ' - ' . print_r($wz_map[intval($wz_code)], true) . '<br>';
            return $wz_map[intval($wz_code)] ?? ($wz_map[$wz_code] ?? '');
        }, $classification->wz_codes));
        $classification->naics_codes = $nacis_codes->flatten()->unique()->values()->all();
        $classification->save();
    }
    return $classifications;
});
Route::get('/run_gpt',function() {
    $criterion = ["market_size_competitive_intensity",
        "regulatory_framework_evaluation",
        "technology_adoption",
        "pressure_for_change",
        "consulting_affinity",
        "cost_pressure",
        "financial_strength",
        "urge_for_diversification",
        "openness_to_innovation",
        "agility_index"
    ];
    $count = 0;
    foreach($criterion as $criteria){
        DB::table('naics_industries')->whereNull($criteria)->get()->each(function ($industry) use ($criteria, &$count) {
            \App\Jobs\ProcessIterative2::dispatch($industry->id, $criteria);
            $count++;
        });
    }
    dd($count);
});
Route::get('/de_dupe_companies',function() {
        // First, find all the companies with duplicate legal_name
        $duplicates = Company::select('legal_name', DB::raw('COUNT(*) as count'))
            ->whereNotNull('legal_name')
            ->groupBy('legal_name')
            ->having('count', '>', 1)
            ->pluck('legal_name');

        // Loop through each duplicate legal_name to find the ones with lesser data
        foreach ($duplicates as $legalName) {
            // Fetch all companies with the same legal_name
            $companies = Company::where('legal_name', $legalName)->get();

            // Sort by the number of non-null fields (assuming lesser non-null data is the criteria)
            $companies = $companies->sortBy(function ($company) {
                return $company->getAttributesCount();  // Assuming you define this function to count non-null fields
            });

            // Delete all but the one with the most data (the last one in the sorted collection)
            $companies->pop();  // Keep the one with the most data
            foreach ($companies as $company) {
                $company->delete();
            }
            echo 'Deleted ' . count($companies) . ' companies with lesser data for ' . $legalName . '<br>';
        }

        return "Duplicate companies with lesser data have been deleted.";
    });
    
    Route::get('/classify',function(){
        DB::table('company_company_classification')->truncate();
        $companies = Company::whereNotNull('wz_code')->get();
        $companyIds = $companies->pluck('id')->toArray();
        $chunks = array_chunk($companyIds, 100);
        foreach($chunks as $chunk){
            \App\Jobs\ClassifyCompaniesJob::dispatchSync($chunk);
        }
    });
    
    Route::get('/import_cognism_companies',function(){
    $data = Storage::disk('local')->get('companies/all.json');
    $companies = json_decode($data,true);
    foreach($companies as $company){
        if(!isset($company['sic'])){
            dd($company);
        }
        try{
            // change revenue to million
            $company['revenue'] = round($company['revenue'] ? $company['revenue'] / 1000000 : 0, 2);
            Company::updateOrCreate([
                'name' => $company['name']
            ],[
                'domain' => $company['domain'] ?? '',
                'industry' => $company['li_industry'] ?? '',
                'headcount' => $company['headcounts'] ?? '',
                'revenue' => $company['revenue'] ?? '',
                'country' => 'Germany',
                'wz_code' => isset($company['sic']) && count($company['sic']) > 0 ? $company['sic'][count($company['sic']) - 1] : '',
            ]);

        }catch(\Exception $e){
            echo $company['name'] . ' ' . $e->getMessage() . '<br>';
            continue;
        }
    }
});
Route::get('/match_likes_comments',function(){
    $likes_comments = LikeComment::all();
    foreach($likes_comments as $like_comment){
        $contact = Contact::where('linkedin',trim($like_comment->profile_link, '/').'/')->first();
        if($contact){
            $like_comment->contact_id = $contact->id;
            $like_comment->save();
        }
    }
});
Route::get('/import_likes_comments',function(){
    $path = Storage::path('result-2.csv');
    $csv = fopen($path,'r');
    $header = fgetcsv($csv, 1000, ',');
    while($row = fgetcsv($csv, 1000, ',')){
        if(count($row) < 8){
            continue;
        }
        LikeComment::create([
            'profile_link' => $row[0],
            'first_name' => $row[1],
            'last_name' => $row[2],
            'post_url' => $row[4],
            'comment' => $row[5],
            'is_comment' => $row[6] == 'true' ? 1 : 0,
            'is_like' => $row[7] == 'true' ? 1 : 0,

        ]);
    }

});

// Route::get('/import_contacts',function(){
//     $path = Storage::path('contacts.csv');
//     $csv = fopen($path,'r');
//     $header = fgetcsv($csv, 1000, ',');
//     while($row = fgetcsv($csv, 1000, ',')){
//         Contact::create([
//             'first_name' => $row[1],
//             'last_name' => $row[2],
//             'email_domain' => $row[3],
//             'approach' => $row[5],
//             'target_category' => $row[6],
//             'linkedin_hub_url' => $row[7],
//             'linkedin' => $row[8],
//             'country' => $row[9] ?? 'Germany',
//         ]);
//     }

// });
Route::get('/fix_wz_codes',function(){
    $companies = Company::where('wz_code', 'Like', '%.')->get();
    foreach($companies as $company){
        $company->wz_code .= '0';
        $company->save();
    }
    
});
Route::get('/bing',function(){
    $subscriptionKey = '5680f63eb06a4fe586658ca3d595916a';
    $endpoint = 'https://api.bing.microsoft.com/v7.0/search';
    $query = 'Company Plan Do See America, Inc 2023 financial report';

    $headers = [
        "Ocp-Apim-Subscription-Key: $subscriptionKey"
    ];

    $params = [
        'q' => $query,
        'count' => 10, // Number of results
        'responseFilter' => 'Webpages',
        'textDecorations' => 'true',
        'textFormat' => 'HTML'
    ];

    $url = $endpoint . '?' . http_build_query($params);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if(curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    } else {
        $searchResults = json_decode($response, true);
        echo 'Search results for query: ' . $query . '<br><br>';
        // Print the names and URLs of the search results
        if (isset($searchResults['webPages']['value'])) {
            foreach ($searchResults['webPages']['value'] as $result) {
                echo '<a href="' . $result['url'] . '">' . $result['name'] . '</a><br>';
            }
        } else {
            echo "No results found.\n";
        }
    }

    curl_close($ch);
});
Route::get('/wz_import',function(){
    $data = json_decode(file_get_contents(Storage::path('wz_codes.json')));
    foreach($data as $industry){
        $wz_code = $industry->code;
        $parent = explode('.',$wz_code)[0];
        $parent = Industry::where('wz_code',$parent)->first();
        if($parent){
            if(Industry::where('wz_code',$wz_code)->first()){
                //update industry
                $sub_industry = Industry::where('wz_code',$wz_code)->first();
                $sub_industry->branch = $industry->industry;
                $sub_industry->parent_industry_id = $parent->id;
                $sub_industry->save();
                echo 'Updated ' . $sub_industry->branch . ' under ' . $parent->branch . '<br>';
            }else{
                $industry = Industry::create([
                    'branch' => $industry->industry,
                    'score' => 0,
                    'associated_industries' => ' ',
                    'wz_code' => $wz_code,
                    'parent_industry_id' => $parent->id
                ]);
                echo 'Added ' . $industry->branch . ' under ' . $parent->branch . '<br>';
        }
        }
        else{
            $industry = Industry::create([
                'branch' => $industry->industry,
                'score' => 0,
                'associated_industries' => ' ',
                'wz_code' => $wz_code
            ]);
            echo 'Added ' . $industry->branch . '<br>';
        }
    }
});
Route::get('/updates',function(){
    $csv = fopen(Storage::path('updates.csv'),'r');
    $header = fgetcsv($csv, 1000, ';');
    while($row = fgetcsv($csv, 1000, ';')){
        $legal_name = $row[1];
        $revenue = $row[2];
        $domain = $row[3];
        $wz_code = $row[4];
        $headcount = $row[5];
        $company = Company::where('name',$row[0])->first();
        // update only empty fields
        if($company){
            try{
                if(!$company->legal_name){
                    $company->legal_name = $legal_name;
                }
                if(!$company->revenue){
                    $company->revenue = $revenue;
                }
                if(!$company->domain){
                    $company->domain = $domain;
                }
                if(!$company->wz_code){
                    $company->wz_code = $wz_code;
                }
                if(!$company->headcount){
                    $company->headcount = $headcount;
                }
                $company->flag = 'Nico';
                $company->save();
            }catch(\Exception $e){
                echo $company->name . ' ' . $e->getMessage() . '<br>';
                continue;
            }
        }
    }
    echo 'done';
});
Route::get('/dupes',function(){
    $companies = DB::table('companies')
        ->select('name', DB::raw('count(*) as count'))
        ->groupBy('name')
        ->having('count', '>', 1)
        ->orderBy('count', 'desc')
        ->take(200)
        ->get();
    foreach ($companies as $companyGroup) {
        $name = $companyGroup->name;

        // Fetch all companies with the same name
        $duplicates = Company::where('name', $name)->get();

        // Determine the company to retain (the one with the most non-null fields)
        $companyToRetain = $duplicates->sortByDesc(function ($company) {
            return count(array_filter($company->toArray(), function ($value) {
                return !is_null($value);
            }));
        })->first();

        // Delete all other duplicates
        foreach ($duplicates as $duplicate) {
            if ($duplicate->id !== $companyToRetain->id) {
                $duplicate->delete();
            }
        }
    }
    if($companies->count() > 0){
        die('<script>window.location.href = "/dupes";</script>');
    }
})->name('dupes');
Route::get('/import',function(){
    $files = Storage::files('processed/germany');
    $companies = [];
    foreach($files as $file){
        $file_data = Storage::get($file);
        $json_data = json_decode($file_data,true);
        if(!isset($json_data['companies']['results'])){
            continue;
        }
        $data = $json_data['companies']['results'];
        $companies = array_merge($companies, array_map(function($company){ 
            return array_merge([
                    'name' => $company['name'], 
                    'website' => $company['fqdn'] ?? '', 
                    'headcount_min' => $company['company_size']['min'], 
                    'headcount_max' => $company['company_size']['max'], 
                    'revenue_string' => isset($company['revenue_range']) ? $company['revenue_range']['string'] : '', 
                    'revenue_min' => isset($company['revenue_range']) ? intval($company['revenue_range']['min'])/1000000 : '', 
                    'revenue_max' => isset($company['revenue_range']) ? (isset($company['revenue_range']['max']) ? intval($company['revenue_range']['max'])/1000000 : '') : '', 
                    'raw_industry' => $company['industry']['raw_industry'] ?? '', 
                    'primary_industry' => $company['industry']['primary_industry']['key'], 
                    'sub_industry' => isset($company['industry']['primary_industry']['sub_industry']) ? $company['industry']['primary_industry']['sub_industry']['key'] : ''
                ], $company['location']);
            }
        , $data));
    }
    foreach($companies as $company){
        // Company::create([
        //     'name' => $company['name'],
        //     'domain' => $company['website'],
        //     'industry' => $company['raw_industry'],
        //     'headcount' => isset($company['headcount_min']) && isset($company['headcount_max']) ? intval((intval($company['headcount_min']) + intval($company['headcount_max']))/2) : (isset($company['headcount_min']) ? $company['headcount_min'] : (isset($company['headcount_max']) ? $company['headcount_max'] : 0)),
        //     'revenue' => isset($company['revenue_min']) && isset($company['revenue_max']) ? intval((intval($company['revenue_min']) + intval($company['revenue_max']))/2) : (isset($company['revenue_min']) ? $company['revenue_min'] : (isset($company['revenue_max']) ? $company['revenue_max'] : 0)),
        //     'country' => $company['country'],
        // ]);
        Company::where('name', $company['name'])->update([
            'industry' => $company['raw_industry']
        ]);
    }
});
Route::get('/temp',function(){
    // get all json files from storage raw folder
    $companies = [];
    $files = Storage::files('raw');
    foreach($files as $file){
        if(strpos($file,'.json') === false){
            continue;
        }
        $file_data = Storage::get($file);
        $json_data = json_decode($file_data,true);
        if(!isset($json_data['companies']['results'])){
            continue;
        }
        $data = $json_data['companies']['results'];
        $companies = array_merge($companies, array_map(function($company){ 
            return array_merge([
                    'name' => $company['name'], 
                    'website' => $company['fqdn'] ?? '', 
                    'headcount_min' => $company['company_size']['min'], 
                    'headcount_max' => $company['company_size']['max'], 
                    'revenue_string' => isset($company['revenue_range']) ? $company['revenue_range']['string'] : '', 
                    'revenue_min' => isset($company['revenue_range']) ? $company['revenue_range']['min'] : '', 
                    'revenue_max' => isset($company['revenue_range']) ? ($company['revenue_range']['max'] ?? '') : '', 
                    'raw_industry' => $company['industry']['raw_industry'] ?? '', 
                    'primary_industry' => $company['industry']['primary_industry']['key'], 
                    'sub_industry' => isset($company['industry']['primary_industry']['sub_industry']) ? $company['industry']['primary_industry']['sub_industry']['key'] : ''
                ], $company['location']);
            }
        , $data));
        // move json to processed/italy folder
        Storage::move($file, 'processed/germany/' . basename($file));
    }
    Storage::disk('local')->put('processed.json', json_encode($companies));
    header('Content-Type: text/csv');
    $csv = fopen('php://output', 'w');
    fputcsv($csv, ['Name','Website','Headcount Min','Headcount Max','Revenue String','Revenue Min','Revenue Max','Raw Industry','Primary Industry','Sub Industry','City','Country','Raw Location']);
    foreach($companies as $company){
        fputcsv($csv, [
            $company['name'],
            $company['website'],
            $company['headcount_min'],
            $company['headcount_max'],
            $company['revenue_string'],
            $company['revenue_min'],
            $company['revenue_max'],
            $company['raw_industry'],
            $company['primary_industry'],
            $company['sub_industry'],
            $company['city'] ?? '',
            $company['country'] ?? '',
            $company['raw_location' ?? '']
        ]);
    }
    fclose($csv);
})->name('export');

Route::get('/companies/process/{id}',function($id){
    \App\Jobs\ProcessCompany::dispatchSync($id);
    return redirect()->back();
})->middleware(['auth','verified'])->name('companies.process');

Route::middleware('auth')->group(function(){
    Route::get('/profile',[ProfileController::class,'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class,'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class,'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';

// SELECT `id`, `title`, `description`, `code`, `market_size_competitive_intensity`, `market_size_competitive_intensity_weight`, `regulatory_framework_evaluation`, `regulatory_framework_evaluation_weight`, `technology_adoption`, `technology_adoption_weight`, `pressure_for_change`, `pressure_for_change_weight`, `consulting_affinity`, `consulting_affinity_weight`, `cost_pressure`, `cost_pressure_weight`, `financial_strength`, `financial_strength_weight`, `openness_to_innovation`, `openness_to_innovation_weight`, `agility_index`, `agility_index_weight`, `urge_for_diversification`, `urge_for_diversification_weight` FROM `naics_industries` WHERE 1