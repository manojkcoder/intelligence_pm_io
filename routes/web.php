<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyClassifications;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\NAICSIndustryController;
use App\Models\Activity;
use App\Models\Company;
use App\Models\CompanyClassification;
use App\Models\Industry;
use App\Models\Contact;
use App\Models\ContactJob;
use App\Models\ContactLicence;
use App\Models\ContactSchool;
use App\Models\LikeComment;
use App\Models\QuizResponse;
use App\Models\School;
use App\Models\WzCodesNaicsMapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use GuzzleHttp\Client;

Route::get('/',function(){
    return view('welcome');
});
Route::get('/accounts',[DashboardController::class,'accounts'])->middleware(['auth','verified'])->name('accounts');
Route::get('/companies/all',[DashboardController::class,'companies'])->middleware(['auth','verified'])->name('accounts.all');
Route::get('/companies/search',[DashboardController::class,'searchCompanies'])->middleware(['auth','verified'])->name('companies.search');
Route::get('/stats',[DashboardController::class,'stats'])->middleware(['auth','verified'])->name('stats');
Route::any('/stats/all',[DashboardController::class,'allStats'])->middleware(['auth','verified'])->name('stats.all');
Route::get('/accounts/{id}',[DashboardController::class,'viewCompany'])->middleware(['auth','verified'])->name('viewCompany');
Route::delete('/deleteCompany/{id}',[DashboardController::class,'deleteCompany'])->middleware(['auth','verified'])->name('deleteCompany');
Route::get('/deleteCompanies/{id}',[DashboardController::class,'deleteCompany'])->middleware(['auth','verified'])->name('deleteCompanies');
Route::delete('/trashedCompany/{id}',[DashboardController::class,'trashedCompany'])->middleware(['auth','verified'])->name('trashedCompany');
Route::post('/moveCompany/{id}',[DashboardController::class,'moveCompany'])->middleware(['auth','verified'])->name('moveCompany');
Route::post('/dream/{id}',[DashboardController::class,'dream'])->middleware(['auth','verified'])->name('dream');
Route::post('/existing-client/{id}',[DashboardController::class,'existingClient'])->middleware(['auth','verified'])->name('existing_client');
Route::get('/accounts/edit/{id}',[DashboardController::class,'editCompany'])->middleware(['auth','verified'])->name('editCompany');
Route::patch('/accounts/edit/{id}',[DashboardController::class,'updateCompany'])->middleware(['auth','verified'])->name('updateCompany');
Route::delete('/accounts/edit/{id}',[DashboardController::class,'destroyCompany'])->middleware(['auth','verified'])->name('destroyCompany');
Route::post('/quiz/update',[DashboardController::class,'updateQuiz'])->middleware(['auth','verified'])->name('quiz.update');

Route::get('/dashboard',[DashboardController::class,'dashboard'])->middleware(['auth','verified'])->name('dashboard');
Route::get('/all-accounts',[DashboardController::class,'allAccounts'])->middleware(['auth','verified'])->name('dashboard.all');

Route::get('/contacts',[ContactsController::class,'allContacts'])->middleware(['auth','verified'])->name('contacts.all');
Route::get('/getContacts',[ContactsController::class,'getContacts'])->middleware(['auth','verified'])->name('contacts.get');
Route::get('/contact/{id}',[ContactsController::class,'viewContact'])->middleware(['auth','verified'])->name('viewContact');
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
Route::get('/settings',[SettingController::class,'index'])->middleware(['auth','verified'])->name('settings');
Route::post('/settings',[SettingController::class,'update'])->middleware(['auth','verified'])->name('updateSetting');
// Route::get('/collate_contacts',function(){
    // list all folders under storage/contacts
    // $folders = Storage::directories('contacts');
    // foreach($folders as $folder){
    //     $company_id = basename($folder);
    //     echo 'Collating contacts for company ' . $company_id . '<br>';
    //     // check if already collated
    //     if(Storage::exists('contacts/'.$company_id.'.json')){
    //         echo 'Contacts for company ' . $company_id . ' already collated <br>';
    //         continue;
    //     }
    //     $contacts = [];
    //     $files = Storage::files('contacts/'.$company_id);
    //     foreach($files as $file){
    //         $file_data = Storage::get($file);
    //         $json_data = json_decode($file_data,true);
    //         $contacts = array_merge($contacts,$json_data);
    //     }
    //     // filter duplicate contacts based on id
    //     try{
    //         $contacts = collect($contacts)->unique('id')->values()->all();
    //         Storage::disk('local')->put('contacts/'.$company_id.'.json',json_encode($contacts));
    //         echo 'Collated contacts for company ' . $company_id .',total: '.count($contacts). '<br>';
    //     }catch(\Exception $e){
    //         echo 'Error collating contacts for company ' . $company_id . ' ' . $e->getMessage() . '<br>';
    //     }
    // }
// });
Route::get('/map_classifications',function(){
    $classifications = \App\Models\CompanyClassification::get();
    $map = Storage::get('wz_code_map.json');
    $map = json_decode($map,true);
    $wz_map = [];
    foreach($map as $value){
        $wz_map[$value['wz_code'].''] = $value['naics_codes'];
    }
    foreach($classifications as $classification){
        $nacis_codes = collect(array_map(function($wz_code) use ($wz_map){
            echo $wz_code . ' - ' . print_r($wz_map[intval($wz_code)],true) . '<br>';
            return $wz_map[intval($wz_code)] ?? ($wz_map[$wz_code] ?? '');
        },$classification->wz_codes));
        $classification->naics_codes = $nacis_codes->flatten()->unique()->values()->all();
        $classification->save();
    }
    return $classifications;
});
Route::get('/run_gpt',function(){
    $criterion = ["market_size_competitive_intensity","regulatory_framework_evaluation","technology_adoption","pressure_for_change","consulting_affinity","cost_pressure","financial_strength","urge_for_diversification","openness_to_innovation","agility_index"];
    $count = 0;
    foreach($criterion as $criteria){
        DB::table('naics_industries')->whereNull($criteria)->get()->each(function ($industry) use ($criteria,&$count){
            \App\Jobs\ProcessIterative2::dispatch($industry->id,$criteria);
            $count++;
        });
    }
    dd($count);
});
Route::get('/list_duplicates',[DashboardController::class,'dupes'])->middleware(['auth','verified'])->name('list_duplicates');
Route::get('/list_duplicates/all',[DashboardController::class,'allDupes'])->middleware(['auth','verified'])->name('list_duplicates.all');
Route::get('/missing_wz_codes',function(){
    $unique_wz_codes = Company::whereNotNull('wz_code')->select('wz_code',DB::raw('COUNT(*) as count'))->groupBy('wz_code')->get()->mapWithKeys(function($item){
        return [$item->wz_code => $item->count];
    });
    $wz_codes = collect(["01","01.1","01.11","01.11.0","01.12","01.12.0","01.13","01.13.1","01.13.2","01.14","01.14.0","01.15","01.15.0","01.16","01.16.0","01.19","01.19.1","01.19.2","01.19.9","01.2","01.21","01.21.0","01.22","01.22.0","01.23","01.23.0","01.24","01.24.0","01.25","01.25.1","01.25.9","01.26","01.26.0","01.27","01.27.0","01.28","01.28.0","01.29","01.29.0","01.3","01.30","01.30.1","01.30.2","01.4","01.41","01.41.0","01.42","01.42.0","01.43","01.43.0","01.44","01.44.0","01.45","01.45.0","01.46","01.46.0","01.47","01.47.1","01.47.2","01.47.9","01.49","01.49.0","01.5","01.50","01.50.0","01.6","01.61","01.61.0","01.62","01.62.0","01.63","01.63.0","01.64","01.64.0","01.7","01.70","01.70.0","02","02.1","02.10","02.10.0","02.2","02.20","02.20.0","02.3","02.30","02.30.0","02.4","02.40","02.40.0","03","03.1","03.11","03.11.0","03.12","03.12.0","03.2","03.21","03.21.0","03.22","03.22.0","B","05","05.1","05.10","05.10.0","05.2","05.20","05.20.0","06","06.1","06.10","06.10.0","06.2","06.20","06.20.0","07","07.1","07.10","07.10.0","07.2","07.21","07.21.0","07.29","07.29.0","08","08.1","08.11","08.11.0","08.12","08.12.0","08.9","08.91","08.91.0","08.92","08.92.0","08.93","08.93.0","08.99","08.99.0","09","09.1","09.10","09.10.0","09.9","09.90","09.90.0","C","10","10.1","10.11","10.11.0","10.12","10.12.0","10.13","10.13.0","10.2","10.20","10.20.0","10.3","10.31","10.31.0","10.32","10.32.0","10.39","10.39.0","10.4","10.41","10.41.0","10.42","10.42.0","10.5","10.51","10.51.0","10.52","10.52.0","10.6","10.61","10.61.0","10.62","10.62.0","10.7","10.71","10.71.0","10.72","10.72.0","10.73","10.73.0","10.8","10.81","10.81.0","10.82","10.82.0","10.83","10.83.0","10.84","10.84.0","10.85","10.85.0","10.86","10.86.0","10.89","10.89.0","10.9","10.91","10.91.0","10.92","10.92.0","11","11.0","11.01","11.01.0","11.02","11.02.0","11.03","11.03.0","11.04","11.04.0","11.05","11.05.0","11.06","11.06.0","11.07","11.07.0","12","12.0","12.00","12.00.0","13","13.1","13.10","13.10.0","13.2","13.20","13.20.0","13.3","13.30","13.30.0","13.9","13.91","13.91.0","13.92","13.92.0","13.93","13.93.0","13.94","13.94.0","13.95","13.95.0","13.96","13.96.0","13.99","13.99.0","14","14.1","14.11","14.11.0","14.12","14.12.0","14.13","14.13.1","14.13.2","14.13.3","14.14","14.14.1","14.14.2","14.14.3","14.19","14.19.0","14.2","14.20","14.20.0","14.3","14.31","14.31.0","14.39","14.39.0","15","15.1","15.11","15.11.0","15.12","15.12.0","15.2","15.20","15.20.0","16","16.1","16.10","16.10.0","16.2","16.21","16.21.0","16.22","16.22.0","16.23","16.23.0","16.24","16.24.0","16.29","16.29.0","17","17.1","17.11","17.11.0","17.12","17.12.0","17.2","17.21","17.21.0","17.22","17.22.0","17.23","17.23.0","17.24","17.24.0","17.29","17.29.0","18","18.1","18.11","18.11.0","18.12","18.12.0","18.13","18.13.0","18.14","18.14.0","18.2","18.20","18.20.0","19","19.1","19.10","19.10.0","19.2","19.20","19.20.0","20","20.1","20.11","20.11.0","20.12","20.12.0","20.13","20.13.0","20.14","20.14.0","20.15","20.15.0","20.16","20.16.0","20.17","20.17.0","20.2","20.20","20.20.0","20.3","20.30","20.30.0","20.4","20.41","20.41.0","20.42","20.42.0","20.5","20.51","20.51.0","20.52","20.52.0","20.53","20.53.0","20.59","20.59.0","20.6","20.60","20.60.0","21","21.1","21.10","21.10.0","21.2","21.20","21.20.0","22","22.1","22.11","22.11.0","22.19","22.19.0","22.2","22.21","22.21.0","22.22","22.22.0","22.23","22.23.0","22.29","22.29.0","23","23.1","23.11","23.11.0","23.12","23.12.0","23.13","23.13.0","23.14","23.14.0","23.19","23.19.0","23.2","23.20","23.20.0","23.3","23.31","23.31.0","23.32","23.32.0","23.4","23.41","23.41.0","23.42","23.42.0","23.43","23.43.0","23.44","23.44.0","23.49","23.49.0","23.5","23.51","23.51.0","23.52","23.52.0","23.6","23.61","23.61.0","23.62","23.62.0","23.63","23.63.0","23.64","23.64.0","23.65","23.65.0","23.69","23.69.0","23.7","23.70","23.70.0","23.9","23.91","23.91.0","23.99","23.99.0","24","24.1","24.10","24.10.0","24.2","24.20","24.20.1","24.20.2","24.20.3","24.3","24.31","24.31.0","24.32","24.32.0","24.33","24.33.0","24.34","24.34.0","24.4","24.41","24.41.0","24.42","24.42.0","24.43","24.43.0","24.44","24.44.0","24.45","24.45.0","24.46","24.46.0","24.5","24.51","24.51.0","24.52","24.52.0","24.53","24.53.0","24.54","24.54.0","25","25.1","25.11","25.11.0","25.12","25.12.0","25.2","25.21","25.21.0","25.29","25.29.0","25.3","25.30","25.30.0","25.4","25.40","25.40.0","25.5","25.50","25.50.1","25.50.2","25.50.3","25.50.4","25.50.5","25.6","25.61","25.61.0","25.62","25.62.0","25.7","25.71","25.71.0","25.72","25.72.0","25.73","25.73.1","25.73.2","25.73.3","25.73.4","25.73.5","25.9","25.91","25.91.0","25.92","25.92.0","25.93","25.93.0","25.94","25.94.0","25.99","25.99.1","25.99.2","25.99.3","26","26.1","26.11","26.11.1","26.11.9","26.12","26.12.0","26.2","26.20","26.20.0","26.3","26.30","26.30.0","26.4","26.40","26.40.0","26.5","26.51","26.51.1","26.51.2","26.51.3","26.52","26.52.0","26.6","26.60","26.60.0","26.7","26.70","26.70.0","26.8","26.80","26.80.0","27","27.1","27.11","27.11.0","27.12","27.12.0","27.2","27.20","27.20.0","27.3","27.31","27.31.0","27.32","27.32.0","27.33","27.33.0","27.4","27.40","27.40.0","27.5","27.51","27.51.0","27.52","27.52.0","27.9","27.90","27.90.0","28","28.1","28.11","28.11.0","28.12","28.12.0","28.13","28.13.0","28.14","28.14.0","28.15","28.15.0","28.2","28.21","28.21.1","28.21.9","28.22","28.22.0","28.23","28.23.0","28.24","28.24.0","28.25","28.25.0","28.29","28.29.0","28.3","28.30","28.30.0","28.4","28.41","28.41.0","28.49","28.49.1","28.49.2","28.49.3","28.49.9","28.9","28.91","28.91.0","28.92","28.92.1","28.92.2","28.93","28.93.0","28.94","28.94.0","28.95","28.95.0","28.96","28.96.0","28.99","28.99.0","29","29.1","29.10","29.10.1","29.10.2","29.2","29.20","29.20.0","29.3","29.31","29.31.0","29.32","29.32.0","30","30.1","30.11","30.11.0","30.12","30.12.0","30.2","30.20","30.20.1","30.20.2","30.3","30.30","30.30.0","30.4","30.40","30.40.0","30.9","30.91","30.91.0","30.92","30.92.0","30.99","30.99.0","31","31.0","31.01","31.01.1","31.01.2","31.02","31.02.0","31.03","31.03.0","31.09","31.09.1","31.09.9","32","32.1","32.11","32.11.0","32.12","32.12.0","32.13","32.13.0","32.2","32.20","32.20.0","32.3","32.30","32.30.0","32.4","32.40","32.40.0","32.5","32.50","32.50.1","32.50.2","32.50.3","32.9","32.91","32.91.0","32.99","32.99.0","33","33.1","33.11","33.11.0","33.12","33.12.0","33.13","33.13.0","33.14","33.14.0","33.15","33.15.0","33.16","33.16.0","33.17","33.17.0","33.19","33.19.0","33.2","33.20","33.20.0","D","35","35.1","35.11","35.11.1","35.11.2","35.11.3","35.12","35.12.0","35.13","35.13.0","35.14","35.14.0","35.2","35.21","35.21.1","35.21.2","35.21.3","35.22","35.22.0","35.23","35.23.0","35.3","35.30","35.30.0","E","36","36.0","36.00","36.00.1","36.00.2","36.00.3","37","37.0","37.00","37.00.1","37.00.2","38","38.1","38.11","38.11.0","38.12","38.12.0","38.2","38.21","38.21.0","38.22","38.22.0","38.3","38.31","38.31.0","38.32","38.32.0","39","39.0","39.00","39.00.0","F","41","41.1","41.10","41.10.1","41.10.2","41.10.3","41.2","41.20","41.20.1","41.20.2","42","42.1","42.11","42.11.0","42.12","42.12.0","42.13","42.13.0","42.2","42.21","42.21.0","42.22","42.22.0","42.9","42.91","42.91.0","42.99","42.99.0","43","43.1","43.11","43.11.0","43.12","43.12.0","43.13","43.13.0","43.2","43.21","43.21.0","43.22","43.22.0","43.29","43.29.1","43.29.9","43.3","43.31","43.31.0","43.32","43.32.0","43.33","43.33.0","43.34","43.34.1","43.34.2","43.39","43.39.0","43.9","43.91","43.91.1","43.91.2","43.99","43.99.1","43.99.2","43.99.9","G","45","45.1","45.11","45.11.0","45.19","45.19.0","45.2","45.20","45.20.1","45.20.2","45.20.3","45.20.4","45.3","45.31","45.31.0","45.32","45.32.0","45.4","45.40","45.40.0","46","46.1","46.11","46.11.0","46.12","46.12.0","46.13","46.13.1","46.13.2","46.14","46.14.1","46.14.2","46.14.3","46.14.4","46.14.5","46.14.6","46.14.7","46.14.9","46.15","46.15.1","46.15.2","46.15.3","46.15.4","46.15.5","46.16","46.16.1","46.16.2","46.16.3","46.16.4","46.16.5","46.17","46.17.1","46.17.2","46.17.9","46.18","46.18.1","46.18.2","46.18.3","46.18.4","46.18.5","46.18.6","46.18.7","46.18.9","46.19","46.19.0","46.2","46.21","46.21.0","46.22","46.22.0","46.23","46.23.0","46.24","46.24.0","46.3","46.31","46.31.0","46.32","46.32.0","46.33","46.33.0","46.34","46.34.0","46.35","46.35.0","46.36","46.36.0","46.37","46.37.0","46.38","46.38.1","46.38.2","46.38.9","46.39","46.39.1","46.39.9","46.4","46.41","46.41.0","46.42","46.42.1","46.42.2","46.43","46.43.1","46.43.2","46.43.3","46.44","46.44.1","46.44.2","46.45","46.45.0","46.46","46.46.1","46.46.2","46.47","46.47.0","46.48","46.48.0","46.49","46.49.1","46.49.2","46.49.3","46.49.4","46.49.5","46.5","46.51","46.51.0","46.52","46.52.0","46.6","46.61","46.61.0","46.62","46.62.0","46.63","46.63.0","46.64","46.64.0","46.65","46.65.0","46.66","46.66.0","46.69","46.69.1","46.69.2","46.69.3","46.7","46.71","46.71.1","46.71.2","46.72","46.72.1","46.72.2","46.73","46.73.1","46.73.2","46.73.3","46.73.4","46.73.5","46.73.6","46.73.7","46.73.8","46.74","46.74.1","46.74.2","46.74.3","46.75","46.75.0","46.76","46.76.0","46.77","46.77.0","46.9","46.90","46.90.1","46.90.2","46.90.3","47","47.1","47.11","47.11.1","47.11.2","47.19","47.19.1","47.19.2","47.2","47.21","47.21.0","47.22","47.22.0","47.23","47.23.0","47.24","47.24.0","47.25","47.25.0","47.26","47.26.0","47.29","47.29.0","47.3","47.30","47.30.1","47.30.2","47.4","47.41","47.41.0","47.42","47.42.0","47.43","47.43.0","47.5","47.51","47.51.0","47.52","47.52.1","47.52.3","47.53","47.53.0","47.54","47.54.0","47.59","47.59.1","47.59.2","47.59.3","47.59.9","47.6","47.61","47.61.0","47.62","47.62.1","47.62.2","47.63","47.63.0","47.64","47.64.1","47.64.2","47.65","47.65.0","47.7","47.71","47.71.0","47.72","47.72.1","47.72.2","47.73","47.73.0","47.74","47.74.0","47.75","47.75.0","47.76","47.76.1","47.76.2","47.77","47.77.0","47.78","47.78.1","47.78.2","47.78.3","47.78.9","47.79","47.79.1","47.79.2","47.79.9","47.8","47.81","47.81.0","47.82","47.82.0","47.89","47.89.0","47.9","47.91","47.91.1","47.91.9","47.99","47.99.1","47.99.9","H","49","49.1","49.10","49.10.0","49.2","49.20","49.20.0","49.3","49.31","49.31.0","49.32","49.32.0","49.39","49.39.1","49.39.2","49.39.9","49.4","49.41","49.41.0","49.42","49.42.0","49.5","49.50","49.50.0","50","50.1","50.10","50.10.0","50.2","50.20","50.20.0","50.3","50.30","50.30.0","50.4","50.40","50.40.0","51","51.1","51.10","51.10.0","51.2","51.21","51.21.0","51.22","51.22.0","52","52.1","52.10","52.10.0","52.2","52.21","52.21.1","52.21.2","52.21.3","52.21.4","52.21.5","52.21.9","52.22","52.22.1","52.22.2","52.22.3","52.22.9","52.23","52.23.1","52.23.9","52.24","52.24.0","52.29","52.29.1","52.29.2","52.29.9","53","53.1","53.10","53.10.0","53.2","53.20","53.20.0","I","55","55.1","55.10","55.10.1","55.10.2","55.10.3","55.10.4","55.2","55.20","55.20.1","55.20.2","55.20.3","55.20.4","55.3","55.30","55.30.0","55.9","55.90","55.90.1","55.90.9","56","56.1","56.10","56.10.1","56.10.2","56.10.3","56.10.4","56.10.5","56.2","56.21","56.21.0","56.29","56.29.0","56.3","56.30","56.30.1","56.30.2","56.30.3","56.30.4","56.30.9","J","58","58.1","58.11","58.11.0","58.12","58.12.0","58.13","58.13.0","58.14","58.14.0","58.19","58.19.0","58.2","58.21","58.21.0","58.29","58.29.0","59","59.1","59.11","59.11.0","59.12","59.12.0","59.13","59.13.0","59.14","59.14.0","59.2","59.20","59.20.1","59.20.2","59.20.3","60","60.1","60.10","60.10.0","60.2","60.20","60.20.0","61","61.1","61.10","61.10.0","61.2","61.20","61.20.0","61.3","61.30","61.30.0","61.9","61.90","61.90.1","61.90.9","62","62.0","62.01","62.01.1","62.01.9","62.02","62.02.0","62.03","62.03.0","62.09","62.09.0","63","63.1","63.11","63.11.0","63.12","63.12.0","63.9","63.91","63.91.0","63.99","63.99.0","K","64","64.1","64.11","64.11.0","64.19","64.19.1","64.19.2","64.19.3","64.19.4","64.19.5","64.19.6","64.2","64.20","64.20.0","64.3","64.30","64.30.0","64.9","64.91","64.91.0","64.92","64.92.1","64.92.2","64.99","64.99.1","64.99.9","65","65.1","65.11","65.11.0","65.12","65.12.1","65.12.2","65.2","65.20","65.20.0","65.3","65.30","65.30.0","66","66.1","66.11","66.11.0","66.12","66.12.0","66.19","66.19.0","66.2","66.21","66.21.0","66.22","66.22.0","66.29","66.29.0","66.3","66.30","66.30.0","L","68","68.1","68.10","68.10.1","68.10.2","68.2","68.20","68.20.1","68.20.2","68.3","68.31","68.31.1","68.31.2","68.32","68.32.1","68.32.2","M","69","69.1","69.10","69.10.1","69.10.2","69.10.3","69.10.4","69.10.9","69.2","69.20","69.20.1","69.20.2","69.20.3","69.20.4","70","70.1","70.10","70.10.1","70.10.9","70.2","70.21","70.21.0","70.22","70.22.0","71","71.1","71.11","71.11.1","71.11.2","71.11.3","71.11.4","71.12","71.12.1","71.12.2","71.12.3","71.12.9","71.2","71.20","71.20.0","72","72.1","72.11","72.11.0","72.19","72.19.0","72.2","72.20","72.20.0","73","73.1","73.11","73.11.0","73.12","73.12.0","73.2","73.20","73.20.0","74","74.1","74.10","74.10.1","74.10.2","74.10.3","74.2","74.20","74.20.1","74.20.2","74.3","74.30","74.30.1","74.30.2","74.9","74.90","74.90.0","75","75.0","75.00","75.00.1","75.00.9","N","77","77.1","77.11","77.11.0","77.12","77.12.0","77.2","77.21","77.21.0","77.22","77.22.0","77.29","77.29.0","77.3","77.31","77.31.0","77.32","77.32.0","77.33","77.33.0","77.34","77.34.0","77.35","77.35.0","77.39","77.39.0","77.4","77.40","77.40.0","78","78.1","78.10","78.10.0","78.2","78.20","78.20.0","78.3","78.30","78.30.0","79","79.1","79.11","79.11.0","79.12","79.12.0","79.9","79.90","79.90.0","80","80.1","80.10","80.10.0","80.2","80.20","80.20.0","80.3","80.30","80.30.0","81","81.1","81.10","81.10.0","81.2","81.21","81.21.0","81.22","81.22.1","81.22.9","81.29","81.29.1","81.29.2","81.29.9","81.3","81.30","81.30.1","81.30.9","82","82.1","82.11","82.11.0","82.19","82.19.0","82.2","82.20","82.20.0","82.3","82.30","82.30.0","82.9","82.91","82.91.1","82.91.2","82.92","82.92.0","82.99","82.99.1","82.99.9","O","84","84.1","84.11","84.11.0","84.12","84.12.0","84.13","84.13.0","84.2","84.21","84.21.0","84.22","84.22.0","84.23","84.23.0","84.24","84.24.0","84.25","84.25.0","84.3","84.30","84.30.0","P","85","85.1","85.10","85.10.1","85.10.2","85.2","85.20","85.20.0","85.3","85.31","85.31.1","85.31.2","85.32","85.32.0","85.4","85.41","85.41.0","85.42","85.42.1","85.42.2","85.42.3","85.42.4","85.5","85.51","85.51.0","85.52","85.52.0","85.53","85.53.0","85.59","85.59.1","85.59.2","85.59.9","85.6","85.60","85.60.0","Q","86","86.1","86.10","86.10.1","86.10.2","86.10.3","86.2","86.21","86.21.0","86.22","86.22.0","86.23","86.23.0","86.9","86.90","86.90.1","86.90.2","86.90.3","86.90.9","87","87.1","87.10","87.10.0","87.2","87.20","87.20.0","87.3","87.30","87.30.0","87.9","87.90","87.90.0","88","88.1","88.10","88.10.1","88.10.2","88.9","88.91","88.91.0","88.99","88.99.0","R","90","90.0","90.01","90.01.1","90.01.2","90.01.3","90.01.4","90.02","90.02.0","90.03","90.03.1","90.03.2","90.03.3","90.03.4","90.03.5","90.04","90.04.1","90.04.2","90.04.3","91","91.0","91.01","91.01.0","91.02","91.02.0","91.03","91.03.0","91.04","91.04.0","92","92.0","92.00","92.00.1","92.00.2","92.00.3","93","93.1","93.11","93.11.0","93.12","93.12.0","93.13","93.13.0","93.19","93.19.0","93.2","93.21","93.21.0","93.29","93.29.0","S","94","94.1","94.11","94.11.0","94.12","94.12.0","94.2","94.20","94.20.0","94.9","94.91","94.91.0","94.92","94.92.0","94.99","94.99.1","94.99.2","94.99.3","94.99.4","94.99.9","95","95.1","95.11","95.11.0","95.12","95.12.0","95.2","95.21","95.21.0","95.22","95.22.0","95.23","95.23.0","95.24","95.24.0","95.25","95.25.0","95.29","95.29.0","96","96.0","96.01","96.01.0","96.02","96.02.1","96.02.2","96.03","96.03.1","96.03.2","96.04","96.04.0","96.09","96.09.0","T","97","97.0","97.00","97.00.0","98","98.1","98.10","98.10.0","98.2","98.20","98.20.0","U","99","99.0","99.00","99.00.0"]);
    $data = $unique_wz_codes->filter(function($value,$key) use ($wz_codes){
        try{
            if(!$wz_codes->contains($key) && $wz_codes->contains(substr($key,0,strlen($key) - 1))){
                echo $key . ' updated to ' . substr($key,0,strlen($key) - 1) . '<br>';
                Company::where('wz_code',$key)->update(['wz_code' => substr($key,0,strlen($key) - 1)]);
            }else if(!$wz_codes->contains($key) && $wz_codes->contains(substr($key,0,strlen($key) - 2))){
                echo $key . ' updated to ' . substr($key,0,strlen($key) - 2) . '<br>';
                Company::where('wz_code',$key)->update(['wz_code' => substr($key,0,strlen($key) - 2)]);
            }
        }catch(\Exception $e){
            echo $key . ' ' . $e->getMessage() . '<br>';
        }
    })->sortDesc();
    exit;
    $csv = fopen('php://output','w');
    header('Content-Type: text/csv');
    fputcsv($csv,['WZ Code','Count']);
    foreach($data as $key => $value){
        fputcsv($csv,[$key,$value]);
    }
    fclose($csv);
    exit;
});
Route::get('/de_dupe_companies',function(){
    $duplicates = Company::select('legal_name',DB::raw('COUNT(*) as count'))->whereNotNull('legal_name')->groupBy('legal_name')->having('count','>',1)->pluck('legal_name');
    foreach($duplicates as $legalName){
        $companies = Company::where('legal_name',$legalName)->get();
        $companies = $companies->sortBy(function ($company){
            return $company->getAttributesCount();
        });
        $companies->pop();
        foreach($companies as $company){
            $company->delete();
        }
        echo 'Deleted ' . count($companies) . ' companies with lesser data for ' . $legalName . '<br>';
    }
    return "Duplicate companies with lesser data have been deleted.";
});
Route::get('/research',function(){
    $companies = Company::withTrashed()->where('processed',0)->get();
    $companyIds = $companies->pluck('id')->toArray();
    foreach($companyIds as $companyId){
        \App\Jobs\DescriptionResearch::dispatch($companyId);
    }
});
Route::get('/new-wz-code',function(){
    $companies = Company::withTrashed()->whereNull('new_wz_code')->get();
    $companyIds = $companies->pluck('id')->toArray();
    foreach($companyIds as $companyId){
        \App\Jobs\NewWZCodeResearch::dispatch($companyId);
    }
});
Route::get('/classify',function(){
    DB::table('company_company_classification')->truncate();
    $companies = Company::withTrashed()->whereNotNull('wz_code')->orWhereNotNull('naics')->get();
    $companyIds = $companies->pluck('id')->toArray();
    $chunks = array_chunk($companyIds,100);
    foreach($chunks as $chunk){
        \App\Jobs\ClassifyCompaniesJob::dispatch($chunk);
    }
    dd('Classifications have been scheduled');
})->name('classify');
Route::get('/match_likes_comments',function(){
    $likes_comments = LikeComment::all();
    foreach($likes_comments as $like_comment){
        $contact = Contact::where('linkedin',trim($like_comment->profile_link,'/').'/')->first();
        if($contact){
            $like_comment->contact_id = $contact->id;
            $like_comment->save();
        }
    }
});
Route::get('/import_likes_comments',function(){
    $path = Storage::path('result-2.csv');
    $csv = fopen($path,'r');
    $header = fgetcsv($csv,1000,',');
    while($row = fgetcsv($csv,1000,',')){
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
Route::post('/store-linkedin-profile-data',function(){
    $data = request()->all();
    $linkedinUrl = rtrim(urldecode($data['payload']['url']),'/');
    $linkedinUrl = str_replace('https://www.linkedin.com','https://linkedin.com',$linkedinUrl);
    $contact = Contact::where('linkedin',$linkedinUrl)->first();
    if($contact){
        $contact->connections_data = json_encode($data['payload']['results']);
        $contact->connection_processed = 1;
        \App\Jobs\ContactConnection::dispatch($contact->id,$data['payload']['results']);
        $contact->save();
    }
    return json_encode(["status" => "success","message" => "Data successfully stored in the database"]);
})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/update-contacts',function(){
    // $contacts = Contact::where('processed',1)->whereNull('company_id')->take(100)->get();
    // foreach($contacts as $contact){
        // $dataResponse = json_decode($contact->profile_response,true);
        // if(isset($dataResponse['jobs']) && !empty($dataResponse['jobs'])){
        //     $job = $dataResponse['jobs'][0];
        //     if(isset($job['jobTitle']) && !empty($job['jobTitle'])){
        //         $contact->position = $job['jobTitle'];
        //     }
        // }
        // \App\Jobs\GenderResearch::dispatch($contact->id,$contact->first_name . ' '. $contact->last_name,$contact->company_name);
        // \App\Jobs\DomainResearch::dispatch($contact->id,$contact->first_name . ' '. $contact->last_name,$contact->contact,$contact->company_name);
        // $company = Company::whereRaw('LOWER(legal_name) = ?',[trim(strtolower($contact->company_name))])->first();
        // if($company){
        //     $contact->company_id = $company->id;
        //     $contact->domain = $company->domain;
        //     $contact->save();
        // }
        // $company = Company::where('domain',$contact->domain)->first();
        // if($company){
        //     $contact->company_id = $company->id;
        //     $contact->domain = $company->domain;
        //     $contact->save();
        // }
        // $company = Company::where('id',$contact->company_id)->first();
        // if($company){
        //     $contact->company_id = $company->id;
        //     $contact->domain = $company->domain;
        //     $contact->company_name = $company->name;
        //     $contact->save();
        // }
    // }
    $jsonPath = Storage::path('public/profiles2.json');
    $contacts = json_decode(File::get($jsonPath),true);
    foreach($contacts as $contact){
        if(isset($contact['general']) && isset($contact['general']['profileUrl'])){
            $linkedinUrl = rtrim(urldecode($contact['general']['profileUrl']),'/');
            $linkedinUrl = str_replace('https://www.linkedin.com','https://linkedin.com',$linkedinUrl);
            $contactExist = Contact::where('linkedin',$linkedinUrl)->first();
            if($contactExist){
                if($contactExist->processed == 0 || empty($contactExist->profile_response)){
                    $contactExist->profile_response = json_encode($contact);
                    $contactExist->save();
                }
            }else{
                $contactData = new Contact();
                $contactData->profile_response = json_encode($contact);
                $contactData->processed = 0;
                $contactData->save();
            }
        }
    }
});
Route::get('/update-contacts-qa',function(){
    // $parentCompanies = QuizResponse::where('question_id',15)->where('answer','yes')->orderBy('company_id','asc')->get();
    // foreach($parentCompanies as $parentCompany){
    //     $companyHeadquarter = QuizResponse::where('company_id',$parentCompany->company_id)->where('question_id',17)->first();
    //     if(!$companyHeadquarter){
    //         \App\Jobs\CompanyParentHeadquarter::dispatch($parentCompany->company_id)->onQueue('perplexity');
    //     }
    // }
});
Route::get('/update-contacts-profile',function(){
    $contacts = Contact::whereNotNull('profile_response')->where('processed',0)->get();
    foreach($contacts as $contact){
        $dataResponse = json_decode($contact->profile_response,true);
        \App\Jobs\ContactProfile::dispatch($contact->id,$dataResponse);
    }
});
Route::get('/update-contacts-age',function(){
    $contacts = Contact::whereNotNull('age')->where('age','unknown')->get();
    foreach($contacts as $contact){
        \App\Jobs\GenderResearch::dispatch($contact->id,$contact->first_name . ' '. $contact->last_name,$contact->company_name);
    }
});
Route::get('/update-contacts-connections',function(){
    $contacts = Contact::whereNotNull('connections_data')->where("connection_processed",0)->get();
    echo count($contacts) . ' contacts found<br>';
    foreach($contacts as $contact){
        $dataResponse = json_decode($contact->connections_data,true);
        \App\Jobs\ContactConnection::dispatch($contact->id,$dataResponse);
        $contact->connection_processed = 1;
        $contact->save();
    }
});
Route::get('/fetch-cognism-companies',function(){
    // \App\Jobs\GetCognismATCompanies::dispatch('profile')->onQueue('cognism');
    // \App\Jobs\GetCognismCHCompanies::dispatch('profile')->onQueue('cognism');
    // \App\Jobs\GetCognismDE51Companies::dispatch('profile')->onQueue('cognism');
    // \App\Jobs\GetCognismDE501Companies::dispatch('profile')->onQueue('cognism');
    // \App\Jobs\GetCognismDE1001Companies::dispatch('profile')->onQueue('cognism');
    // \App\Jobs\GetCognismES50Companies::dispatch('profile')->onQueue('cognism');
    // \App\Jobs\GetCognismES200Companies::dispatch('profile')->onQueue('cognism');
    // \App\Jobs\GetCognismES500Companies::dispatch('profile')->onQueue('cognism');
    // \App\Jobs\GetCognismIT51Companies::dispatch('profile')->onQueue('cognism');
    // \App\Jobs\GetCognismIT200Companies::dispatch('profile')->onQueue('cognism');
    // \App\Jobs\GetCognismUK51Companies::dispatch('profile')->onQueue('cognism');
    // \App\Jobs\GetCognismUK201Companies::dispatch('profile')->onQueue('cognism');
    // \App\Jobs\GetCognismUK1000Companies::dispatch('profile')->onQueue('cognism');
    // \App\Jobs\GetCognismUK5000Companies::dispatch('profile')->onQueue('cognism');

    // $directory = storage_path('app/contacts');
    // $folders = array_filter(glob($directory.'/*'),'is_dir');
    // foreach($folders as $folder){
    //     $files = array_filter(glob($folder.'/*'),'is_file');
    //     foreach($files as $file){
    //         $companyId = basename($folder);
    //         $fileName = 'contacts/' . $companyId .'/' . basename($file);
    //         \App\Jobs\ImportCognismContacts::dispatch($companyId,$fileName)->onQueue('cognism');
    //     }
    // }
});
// Route::get('/wzcode-naics-mapping',function(){
//     $jsonPath = Storage::path('public/wzcodemapping.json');
//     $wzcodes = json_decode(File::get($jsonPath),true);
//     foreach($wzcodes as $wzcodeData){
//         WzCodesNaicsMapping::create([
//             'wz_codes' => $wzcodeData['wz_codes'],
//             'wz_codes_description' => $wzcodeData['wz_codes_description'],
//             'naics_codes' => $wzcodeData['naics_codes'],
//             'naics_description' => $wzcodeData['naics_description']
//         ]);
//     }
// });
Route::get('/update-wz-codes',function(){
    $jsonPath = Storage::path('public/wz-codes.json');
    $wzcodes = json_decode(File::get($jsonPath),true);
    $updated = 0;
    foreach($wzcodes as $wzcodeData){
        $companies = Company::withTrashed()->where('domain',$wzcodeData['domain'])->get();
        foreach($companies as $company){
            if(isset($wzcodeData['country']) && !empty($wzcodeData['country'])){
                $company->country = $wzcodeData['country'];
            }
            if(isset($wzcodeData['wz_code']) && !empty($wzcodeData['wz_code'])){
                $company->wz_code = $wzcodeData['wz_code'];
            }
            $company->save();
            $updated++;
        }
        // if($company){
        //     $companyExist = Company::withTrashed()->whereRaw('LOWER(name) = ?',[trim(strtolower($wzcodeData['name']))])->where('id','!=',$company->id)->first();
        //     if(!$companyExist){
        //         $company->name = $wzcodeData['name'];
        //     }else{
        //         echo $company->name ." => " . $wzcodeData['name'] . "<br/>";
        //     }
        //     $company->wz_code = $wzcodeData['wz_code'];
        //     $company->country = $wzcodeData['country'];
        //     $company->save();
        //     $updated++;
        // }
    }
    echo $updated . ' companies updated';
});
Route::get('/company-qa',function(){
    // \App\Jobs\CompanyQuiz::dispatch();
    // $tamClass = CompanyClassification::where('name','TAM')->first();
    // $samClass = CompanyClassification::where('name','SAM')->first();
    // $somClass = CompanyClassification::where('name','SOM')->first();
    // $tam4Class = CompanyClassification::where('name','TAM - 4')->first();
    // $sam4Class = CompanyClassification::where('name','SAM - 4')->first();
    // $som4Class = CompanyClassification::where('name','SOM - 4')->first();
    // $companIds = Company::withTrashed()->whereHas('classifications',function($q) use($tamClass,$samClass,$somClass,$tam4Class,$sam4Class,$som4Class){
    //     $q->where('company_classification_id',$tamClass->id)->orWhere('company_classification_id',$tam4Class->id);
    //     // $q->where('company_classification_id',$samClass->id)->orWhere('company_classification_id',$sam4Class->id);
    //     // $q->where('company_classification_id',$somClass->id)->orWhere('company_classification_id',$som4Class->id);
    // })->whereIn('country',['Germany','Austria','Switzerland'])->orderBy('id','asc')->skip(0)->take(8000)->get(['id'])->pluck('id')->toArray();
    // foreach($companIds as $companId){
    //     \App\Jobs\CompanyQAs::dispatch($companId);
    // }
});
// Route::get('/update-hubspot-ids',function(){
//     $jsonPath = Storage::path('public/hubspotIds.json');
//     $companies = json_decode(File::get($jsonPath),true);
//     foreach($companies as $companyData){
//         $company = Company::where('name',$companyData['name'])->first();
//         if($company){
//             if(empty($company->legal_name) || $company->legal_name != $companyData['legal_name']){
//                 $company->legal_name = $companyData['legal_name'];
//             }
//             $company->hubspot_id = $companyData['hubspot_id'];
//             $company->save();
//         }
//     }
// });
// Route::get('/fetch-companies-revenue',function(){
//     $companiesIds = Company::where('country',"Germany")->whereNull('revenue')->orderBy('id','asc')->get()->pluck('id')->toArray();
//     foreach($companiesIds as $companyId){
//         \App\Jobs\CompanyRevenue::dispatch($companyId)->onQueue('perplexity');
//     }
// });
// Route::get('/fetch-companies-headcount',function(){
//     $companiesIds = Company::where('country',"Germany")->whereNull('headcount')->orderBy('id','asc')->get()->pluck('id')->toArray();
//     foreach($companiesIds as $companyId){
//         \App\Jobs\CompanyHeadcount::dispatch($companyId)->onQueue('perplexity');
//     }
// });
// Route::get('/activity-keywords',function(){
    // $activities = Activity::whereNull('is_relevant')->take(5000)->get();
    // foreach($activities as $activity){
    //     \App\Jobs\KeywordsResearch::dispatch($activity->id,$activity->post_content);
    // }
// });
Route::get('/company-scores',function(){
    $companyIds = Company::withTrashed()->whereNotNull('industry')->where('existing_client',0)->get()->pluck('id')->toArray();
    $chunks = array_chunk($companyIds,100);
    foreach($chunks as $chunk){
        \App\Jobs\CompanyScore::dispatch($chunk);
    }
    dd('Company Score Jobs have been scheduled');
});
Route::get('/import-activities',function(){
    \App\Jobs\ImportActivities::dispatch('public/activities4.json');
});
Route::get('/update-activities',function(){
    $activities = Activity::whereNotNull('response')->where('processed',0)->get();
    foreach($activities as $activity){
        \App\Jobs\ContactActivity::dispatch($activity->id);
    }
});
Route::get('/dup-activities',function(){
    $duplicates = DB::table('activities')->select('post_url',DB::raw('COUNT(*) as count'))->groupBy('post_url')->having('count','>',1)->orderByDesc('count')->get();
    foreach($duplicates as $duplicate){
        $activities = Activity::where('post_url',$duplicate->post_url)->orderBy('id','asc')->skip(1)->take(10)->get();
        foreach($activities as $activity){
            $activity->delete();
        }
    }
});
Route::get('/fix_wz_codes',function(){
    $companies = Company::where('wz_code','Like','%.')->get();
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
        'count' => 10,
        'responseFilter' => 'Webpages',
        'textDecorations' => 'true',
        'textFormat' => 'HTML'
    ];
    $url = $endpoint . '?' . http_build_query($params);
    $ch = curl_init($url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
    $response = curl_exec($ch);
    if(curl_errno($ch)){
        echo 'Error:' . curl_error($ch);
    }else{
        $searchResults = json_decode($response,true);
        echo 'Search results for query: ' . $query . '<br><br>';
        if(isset($searchResults['webPages']['value'])){
            foreach($searchResults['webPages']['value'] as $result){
                echo '<a href="' . $result['url'] . '">' . $result['name'] . '</a><br>';
            }
        }else{
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
    $header = fgetcsv($csv,1000,';');
    while($row = fgetcsv($csv,1000,';')){
        $legal_name = $row[1];
        $revenue = $row[2];
        $domain = $row[3];
        $wz_code = $row[4];
        $headcount = $row[5];
        $company = Company::where('name',$row[0])->first();
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
        ->select('name',DB::raw('count(*) as count'))
        ->groupBy('name')
        ->having('count','>',1)
        ->orderBy('count','desc')
        ->take(200)
        ->get();
    foreach($companies as $companyGroup){
        $name = $companyGroup->name;
        $duplicates = Company::where('name',$name)->get();
        $companyToRetain = $duplicates->sortByDesc(function ($company){
            return count(array_filter($company->toArray(),function ($value){
                return !is_null($value);
            }));
        })->first();
        foreach($duplicates as $duplicate){
            if ($duplicate->id !== $companyToRetain->id){
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
        $companies = array_merge($companies,array_map(function($company){ 
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
                ],$company['location']);
            }
        ,$data));
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
        Company::where('name',$company['name'])->update([
            'industry' => $company['raw_industry']
        ]);
    }
});
Route::get('/temp',function(){
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
        $companies = array_merge($companies,array_map(function($company){ 
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
                ],$company['location']);
            }
        ,$data));
        // move json to processed/italy folder
        Storage::move($file,'processed/germany/' . basename($file));
    }
    Storage::disk('local')->put('processed.json',json_encode($companies));
    header('Content-Type: text/csv');
    $csv = fopen('php://output','w');
    fputcsv($csv,['Name','Website','Headcount Min','Headcount Max','Revenue String','Revenue Min','Revenue Max','Raw Industry','Primary Industry','Sub Industry','City','Country','Raw Location']);
    foreach($companies as $company){
        fputcsv($csv,[
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