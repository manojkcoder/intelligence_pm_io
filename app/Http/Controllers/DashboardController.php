<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Industry;
use App\Models\CompanyClassification;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use GuzzleHttp\Client;
use App\Models\LikeComment;
use Illuminate\Support\Str;
use App\Jobs\ClassifyCompaniesJob;


class DashboardController extends Controller
{
    public function dashboard(Request $request){
        $countries = Company::select('country')->distinct()->get()->pluck('country');
        $flags = Company::select('flag')->distinct()->get()->pluck('flag');
        $params = $request->all();
        $pageUrl = route('companies.all',$params);
        return view('dashboard',compact("pageUrl","countries","flags"));
    }
    public function dream(Request $request,$id){
        $company = Company::find($id);
        $company->dream = $request->input('checked') == 'true' ? 1 : 0;
        $company->save();
    }
    public function existingClient(Request $request,$id){
        $company = Company::find($id);
        $company->existing_client = $request->input('checked') == 'true' ? 1 : 0;
        $company->save();
    }
    private function like_match($pattern,$subject){
        $pattern = str_replace('%', '.*', preg_quote($pattern, '/'));
        return (bool) preg_match("/^{$pattern}$/i", $subject);
    }
    public function companies(Request $request){
        $type = ($request->filter ? $request->filter : "all");
        $country = ($request->country ? $request->country : "");
       
        if($country && $country != "all"){
            $companies = Company::where('country',$country)->where('processed',1);
        }else{
            if($type == "incomplete" || $type == "no_wz_code"){
                $companies = Company::withTrashed();
            }else{
                $companies = Company::where('processed',1);
            }
        }
        if($type == "deleted"){
            $companies->onlyTrashed();
        }
        $dream = ($request->dream ? $request->dream : "");
        if($dream && $dream == "1"){
            $companies = $companies->where('dream',1);
        }
        $flag = ($request->flag ? $request->flag : "");
        if($flag && $flag != "all"){
            $companies = $companies->where('flag',$flag);
        }
        $search = $request->has('search') ? $request->search['value'] : "";
        $offset = $request->start ? $request->start : 0;
        $limit = $request->length ? $request->length : 100;
        if($request->has('wz_code') && $request->wz_code != "Total" && $request->wz_code != ""){
            $companies = $companies->where('wz_code','LIKE',$request->wz_code.'%');
        }
        if($request->has('export')){
            $limit = 100000;
        }
        if(!empty($search)){
            $companies = $companies->where(function($query) use ($search){
                $query->where('name','LIKE','%'.$search.'%')->orWhere('domain','LIKE','%'.$search.'%')->orWhere('country','LIKE','%'.$search.'%')->orWhere('revenue','LIKE','%'.$search.'%')->orWhere('wz_code','LIKE','%'.$search.'%')->orWhere('headcount','LIKE','%'.$search.'%');
            });
        }
        if($type !== 'all'){
            $companies->where(function($companies) use ($type){
                if($type == "incomplete"){
                    $companies = $companies->where(function($q){
                        $q->where('revenue',null)->orWhere('headcount',null)->orWhere('wz_code',null);
                    });
                }else if($type == "no_wz_code"){
                    $companies = $companies->where(function($q){
                        $q->whereNull('wz_code')->orWhere('wz_code','');
                    });
                }else if($type == "tam"){
                    $class = CompanyClassification::where('name','TAM')->first();
                    $companies->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                }else if($type == "sam"){
                    $class = CompanyClassification::where('name','SAM')->first();
                    $companies->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                }else if($type == "som"){
                    $class = CompanyClassification::where('name','SOM')->first();
                    $companies->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                }else if($type == "tam_samson4"){
                    $class = CompanyClassification::where('name','TAM - 4')->first();
                    $companies->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                }else if($type == "som_samson4"){
                    $class = CompanyClassification::where('name','SOM - 4')->first();
                    $companies->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                }else if($type == "sam_samson4"){
                    $class = CompanyClassification::where('name','SAM - 4')->first();
                    $companies->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                }else if($type == "som_samson4_oversized"){
                    $class = CompanyClassification::where('name','SOM - 4 Oversized')->first();
                    $companies->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                }else if($type == "sam_samson4_oversized"){
                    $class = CompanyClassification::where('name','SAM - 4 Oversized')->first();
                    $companies->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                }else if($type == "sam_4_diff"){
                    $query = clone $companies;
                    $companyIds = $query->whereHas('classifications',function($qy){
                        $qy->where('company_classification_id',5);
                    })->get()->pluck('id');
                    $companies->whereHas('classifications',function($q){
                        $q->where('company_classification_id',2)->where('company_classification_id',"!=",5);
                    })->whereNotIn('id',$companyIds);
                }else if($type == "som_4_diff"){
                    $query = clone $companies;
                    $companyIds = $query->whereHas('classifications',function($qy){
                        $qy->where('company_classification_id',6);
                    })->get()->pluck('id');
                    $companies->whereHas('classifications',function($q){
                        $q->where('company_classification_id',3);
                    })->whereNotIn('id',$companyIds);
                }
            })->orWhere('custom_classification', strtoupper($type));       
        }
        $totalRecords = $companies->select("id")->count();
        $companies = $companies->select(["id","dream","name","domain","legal_name","country","revenue","wz_code","headcount","processed","custom_classification","existing_client"])->orderBy("name","ASC")->offset($offset)->take($limit)->get();
        if(!$request->has('export')){
            $companies = $companies->map(function($company){
                $accountType = [];
                $company->accountType = $company->company_classifications;
                $company->domain = str_replace('www.','',$company->domain);
                if(!empty($company->domain)){
                    $company->domain = '<a href="//'.$company->domain.'" target="_blank">'.$company->domain.'</a>';
                }
                $company->actions = '<a href="'.route('editCompany',$company->id).'" class="btn-bg-primary text-white font-bold py-2 px-4 mr-2"><i class="fas fa-edit"></i></a>';
                $company->actions .= '<a href="'.route('viewCompany',$company->id).'" class="btn-bg-secondary text-white font-bold py-2 px-4 mr-2"><i class="fas fa-eye"></i></a>';
                $company->actions .= '<button class="btn-bg-option text-white font-bold py-2 px-4 mr-2 moveCompany" data-link="'.route('moveCompany',$company->id).'"><i class="fas fa-exchange-alt"></i></button>';
                $company->actions .= '<button class="btn-bg-danger text-white font-bold py-2 px-4 deleteCompany" data-link="'.route('trashedCompany',$company->id).'"><i class="fas fa-trash"></i></button>';
                return $company;
            });
        }
        if($request->has('export')){
            $filename = 'companies_' . date('Y-m-d_H-i-s') . '.csv';
            $filePath = storage_path('app/public/' . $filename);
            $file = fopen($filePath, 'w');

            fputcsv($file, ['Name', 'Domain', 'Legal Name', 'Country', 'Revenue', 'WZ Code', 'Headcount', 'Type']);
            foreach ($companies as $company) {
                $company->domain = str_replace('www.', '', $company->domain);
                fputcsv($file, [
                    $company->name,
                    $company->domain,
                    $company->legal_name,
                    $company->country,
                    $company->revenue,
                    $company->wz_code,
                    $company->headcount,
                    implode(', ', $company->company_classifications),
                ]);
            }
            fclose($file);
            return response()->download(storage_path('app/public/'.$filename));
        }
        return json_encode(["recordsTotal" => $totalRecords,"recordsFiltered" => $totalRecords,"data" => $companies]);
    }
    public function stats(Request $request){
        $statsUrl = route('stats.all');
        return view('stats',compact("statsUrl"));
    }
    public function allStats(Request $request){
        if(!$request->has('country')){
            $countries = Company::select('country')->distinct()->get()->pluck('country');
        }else{
            $countries = Company::select('country')->distinct()->where('country', $request->input('country'))->get()->pluck('country');
        }
        $outputData = [];
        if(count($countries)){
            $tamClass = CompanyClassification::where('name','TAM')->first();
            $samClass = CompanyClassification::where('name','SAM')->first();
            $somClass = CompanyClassification::where('name','SOM')->first();
            $tamWzCodes = $tamClass->wz_codes;
            $samWzCodes = $samClass->wz_codes;
            $somWzCodes = $somClass->wz_codes;

            foreach($countries as $country){
                if(!empty($country)){

                    if(!$request->has('metric')){
                        $incompleteCompanies = Company::where('country',$country)->where('processed',1)->where(function($q){
                            $q->where('revenue',null)->orWhere('headcount',null)->orWhere('wz_code',null);
                        })->count();
                    }

                    if(!$request->has('metric') || $request->metric == "tamLikes" || $request->metric == "tamComments"){
                        $tamCompanyIds = Company::where('country',$country)->whereHas('classifications',function($q) use ($tamClass){
                            $q->where('company_classification_id',$tamClass->id);
                        })->get()->pluck('id');
                        $tamContactIds = Contact::whereIn("company_id",$tamCompanyIds)->get()->pluck('id');
                        if($request->metric == "tamLikes"){
                            return LikeComment::with('contact.company')->whereIn("contact_id",$tamContactIds)->where('is_like',1)->get();
                        }else if($request->metric == "tamComments"){
                            return LikeComment::with('contact.company')->whereIn("contact_id",$tamContactIds)->where('is_comment',1)->get();
                        }
                        $tamLikes = LikeComment::whereIn("contact_id",$tamContactIds)->where('is_like',1)->count();
                        $tamComments = LikeComment::whereIn("contact_id",$tamContactIds)->where('is_comment',1)->count();
                    }
                    if(!$request->has('metric') || $request->metric == "samLikes" || $request->metric == "samComments"){
                        $samCompanyIds = Company::where('country',$country)->whereHas('classifications',function($q) use ($samClass){
                            $q->where('company_classification_id',$samClass->id);
                        })->get()->pluck('id');
                        $samContactIds = Contact::whereIn("company_id",$samCompanyIds)->get()->pluck('id');
                        if($request->metric == "samLikes"){
                            return LikeComment::with('contact.company')->whereIn("contact_id",$samContactIds)->where('is_like',1)->get();
                        }else if($request->metric == "samComments"){
                            return LikeComment::with('contact.company')->whereIn("contact_id",$samContactIds)->where('is_comment',1)->get();
                        }
                        $samLikes = LikeComment::whereIn("contact_id",$samContactIds)->where('is_like',1)->count();
                        $samComments = LikeComment::whereIn("contact_id",$samContactIds)->where('is_comment',1)->count();
                    }
                    if(!$request->has('metric') || $request->metric == "somLikes" || $request->metric == "somComments"){
                        $somCompanyIds = Company::where('country',$country)->whereHas('classifications',function($q) use ($somClass){
                            $q->where('company_classification_id',$somClass->id);
                        })->get()->pluck('id');
                        $somContactIds = Contact::whereIn("company_id",$somCompanyIds)->get()->pluck('id');
                        if($request->metric == "somLikes"){
                            return LikeComment::with('contact.company')->whereIn("contact_id",$somContactIds)->where('is_like',1)->get();
                        }else if($request->metric == "somComments"){
                            return LikeComment::with('contact.company')->whereIn("contact_id",$somContactIds)->where('is_comment',1)->get();
                        }
                        $somLikes = LikeComment::whereIn("contact_id",$somContactIds)->where('is_like',1)->count();
                        $somComments = LikeComment::whereIn("contact_id",$somContactIds)->where('is_comment',1)->count();
                    }

                    $incompleteLikes = LikeComment::whereNull("contact_id")->where('is_like',1)->count();
                    $incompleteComments = LikeComment::whereNull("contact_id")->where('is_comment',1)->count();

                    $outputData[] = ["country" => $country,"incompleteLikes" => $incompleteLikes,"incompleteComments" => $incompleteComments,"tamLikes" => $tamLikes,"tamComments" => $tamComments,"samLikes" => $samLikes,"samComments" => $samComments,"somLikes" => $somLikes,"somComments" => $somComments, "tamCompanies" => $tamCompanyIds->count(), "samCompanies" => $samCompanyIds->count(), "somCompanies" => $somCompanyIds->count()];
                }
            }
        }
        return json_encode(["recordsTotal" => count($outputData),"recordsFiltered" => count($outputData),"data" => $outputData]);
    }
    public function allLikeCommentCounts(Request $request){
        $countries = Company::select('country')->distinct()->get()->pluck('country');
        $outputData = [];
        if(count($countries)){
            $tamClass = CompanyClassification::where('name','TAM')->first();
            $samClass = CompanyClassification::where('name','SAM')->first();
            $somClass = CompanyClassification::where('name','SOM')->first();
            $tamWzCodes = $tamClass->wz_codes ? json_decode($tamClass->wz_codes) : [];
            $samWzCodes = $samClass->wz_codes ? json_decode($samClass->wz_codes) : [];
            $somWzCodes = $somClass->wz_codes ? json_decode($somClass->wz_codes) : [];

            foreach($countries as $country){
                if(!empty($country)){
                    $tamCompanies = Company::where('country',$country)->where('processed',1)->where('revenue','>=',$tamClass->revenue_threshold)->where('headcount','>=',$tamClass->employee_threshold);
                    if(count($tamWzCodes)){
                        $tamCompanies = $tamCompanies->where(function($query) use ($tamWzCodes){
                            $query->where('wz_code','LIKE',$tamWzCodes[0].'%');
                            for($i=1;$i<count($tamWzCodes);$i++){
                                $query->orWhere('wz_code','LIKE',$tamWzCodes[$i].'%');
                            }
                        });
                    }

                    $samCompanies = Company::where('country',$country)->where('processed',1)->where('revenue','>=',$samClass->revenue_threshold)->where('revenue','<=',$samClass->revenue_max)->where('headcount','>=',$samClass->employee_threshold)->where('headcount','<',$samClass->employee_max);
                    if(count($samWzCodes)){
                        $samCompanies = $samCompanies->where(function($query) use ($samWzCodes){
                            $query->where('wz_code','LIKE',$samWzCodes[0].'%');
                            for($i=1;$i<count($samWzCodes);$i++){
                                $query->orWhere('wz_code','LIKE',$samWzCodes[$i].'%');
                            }
                        });
                    }

                    $somCompanies = Company::where('country',$country)->where('processed',1)->where('revenue','>=',$somClass->revenue_threshold)->where('revenue','<=',$somClass->revenue_max)->where('headcount','>=',$somClass->employee_threshold)->where('headcount','<',$somClass->employee_max);
                    if(count($somWzCodes)){
                        $somCompanies = $somCompanies->where(function($query) use ($somWzCodes){
                            $query->where('wz_code','LIKE',$somWzCodes[0].'%');
                            for($i=1;$i<count($somWzCodes);$i++){
                                $query->orWhere('wz_code','LIKE',$somWzCodes[$i].'%');
                            }
                        });
                    }
                    $tamCompanyIds = $tamCompanies->get()->pluck('id');
                    $samCompanyIds = $samCompanies->get()->pluck('id');
                    $somCompanyIds = $somCompanies->get()->pluck('id');

                    $tamContactIds = Contact::whereIn("company_id",$tamCompanyIds)->get()->pluck('id');
                    $samContactIds = Contact::whereIn("company_id",$samCompanyIds)->get()->pluck('id');
                    $somContactIds = Contact::whereIn("company_id",$somCompanyIds)->get()->pluck('id');

                    $outputData[] = ["country" => $country,"incomplete" => $incompleteCompanies,"tam" => $tamCompanies,"sam" => $samCompanies,"som" => $somCompanies];
                }
            }
        }
        return json_encode(["recordsTotal" => count($outputData),"recordsFiltered" => count($outputData),"data" => $outputData]);
    }
    public function viewCompany($id){
        $company = Company::with('contacts','quiz')->withTrashed()->find($id);
        return view('company',compact('company'));
    }
    public function editCompany($id){
        $company = Company::find($id);
        $countries = Company::select('country')->distinct()->get()->pluck('country');
        return view('edit_company',compact('company','countries'));
    }
    public function updateCompany(Request $request,$id){
        $company = Company::find($id);
        $company->revenue = $request->input('revenue');
        $company->name = $request->input('name');
        $company->headcount = $request->input('headcount');
        $company->wz_code = $request->input('wz_code');
        $company->domain = $request->input('domain');
        $company->country = $request->input('country');
        $company->legal_name = $request->input('legal_name');
        $company->save();
        $company->classifications()->detach();
        ClassifyCompaniesJob::dispatchSync([$company->id]);
        return redirect()->route('dashboard');
    }
    public function deleteCompany($id){
        $company = Company::withTrashed()->find($id);
        if($company->trashed()){
            $company->restore();
        }else{
            $company->delete();
        }
        return redirect()->back();
    }
    public function trashedCompany($id){
        Company::find($id)->delete();
        return json_encode(["status" => "success","message" => "Company Removed"]);
    }
    public function moveCompany(Request $request,$id){
        $company = Company::find($id);
        $company->custom_classification = $request->input('moveType');
        $company->save();
        return json_encode(["status" => "success","message" => "Done"]);
    }
    public function wz_code_status(Request $request){
        $countries = Company::select('country')->distinct()->get()->pluck('country');
        $flags = Company::select('flag')->distinct()->get()->pluck('flag');

        $query = Company::where(function($q){
            $q->whereNotNull('wz_code')->orWhereNotNull('naics');
        });
        if($request->has('country') && $request->country != "all" && $request->country != ""){
            $query = $query->where('country',$request->country);
        }
        if($request->has('flag') && $request->flag != "all" && $request->flag != ""){
            $query = $query->where('flag',$request->flag);
        }
        $classes = CompanyClassification::whereIn('name', ['SAM', 'SOM - 4', 'SOM', 'SAM - 4'])->get();
        $counts = [];
        foreach($classes as $class){
            $q = clone $query;
            $q->whereHas('classifications',function($q) use ($class){
                $q->where('company_classification_id',$class->id);
            });
            $wz_codes = $q->get()->pluck('wz_code');
            foreach($wz_codes as $wz_code){
                $wz_code = strtoupper($wz_code."");
                if(strlen($wz_code) < 2){
                    $wz_code = "Unknown";
                }else{
                    $wz_code = substr($wz_code,0,2);
                }
                if(!isset($counts[$wz_code])){
                    $industry = Industry::where('wz_code',$wz_code)->first();
                    $counts[$wz_code] = [
                        "name" => ($industry ? $industry->branch : $wz_code),
                        'SAM' => 0,
                        'SAM - 4' => 0,
                        'SAM 4 - Diff' => 0,
                        'SOM' => 0,
                        'SOM - 4' => 0,
                        'SOM 4 - Diff' => 0,
                    ];
                }
                if(!isset($counts[$wz_code][$class->name])){
                    $counts[$wz_code][$class->name] = 0;
                }
                $counts[$wz_code][$class->name]++;
            }
        }
        ksort($counts);
        foreach($counts as $wz_code => $data){
            if($counts[$wz_code]['SAM'] > $counts[$wz_code]['SAM - 4']){
                $counts[$wz_code]['SAM 4 - Diff'] = $counts[$wz_code]['SAM'] - $counts[$wz_code]['SAM - 4'];
            }
            if($counts[$wz_code]['SOM'] > $counts[$wz_code]['SOM - 4']){
                $counts[$wz_code]['SOM 4 - Diff'] = $counts[$wz_code]['SOM'] - $counts[$wz_code]['SOM - 4'];
            }
        }
        $total = [
            "name" => "",
            'SAM' => 0,
            'SAM - 4' => 0,
            'SAM 4 - Diff' => 0,
            'SOM' => 0,
            'SOM - 4' => 0,
            'SOM 4 - Diff' => 0,
        ];
        foreach($counts as $wz_code => $data){
            foreach($data as $class => $count){
                if($class != "name"){
                    $total[$class] += $count;
                }
            }
        }
        if($request->expectsJson()){
            arsort($counts);
            $counts = array_slice($counts,0,10,true);
            $data = ['labels' => [], 'data' => []];
            foreach($counts as $key => $value){
                $data['labels'][] = $key;
                $values = 0;
                foreach($value as $k => $v){
                    if($k != "name"){
                        $values += $v;
                    }
                }
                $data['data'][] = $values;
            }
            return response()->json($data);
        }
        $counts['Total'] = $total;
        $map = [
            'SAM' => 'sam',
            'SAM - 4' => 'sam_samson4',
            'SAM 4 - Diff' => 'sam_4_diff',
            'SOM' => 'som',
            'SOM - 4' => 'som_samson4',
            'SOM 4 - Diff' => 'som_4_diff'
        ];
        return view('wz_code_status',compact('counts','countries','flags','map'));
    }
    public function gpt(){
        return view('gpt');
    }
    public function prompt(){
        $client = new Client();
        
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => request('prompt'),
                    ],
                ],
                'max_tokens' => 4096,
            ],
        ]);
        
        $body = $response->getBody();
        $data = json_decode($body, true);

        \Log::info('API Response: ', $data);

        $data = trim($data['choices'][0]['message']['content']);
        // format markdown
        $data = str_replace("\n\n", "<br>", $data);
        // format bold using markdown
        $data = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $data);
        return $data;
        
    }
    public function dupes(Request $request){
        $countries = Company::select('country')->distinct()->get()->pluck('country');
        $flags = Company::select('flag')->distinct()->get()->pluck('flag');
        $params = $request->all();
        $pageUrl = route('list_duplicates.all',$params);
        return view('dupes',compact("pageUrl","countries","flags"));
    }
    public function allDupes(Request $request){
        $type = ($request->filter ? $request->filter : "all");
        $country = ($request->country ? $request->country : "");
       
        if($country && $country != "all"){
            $companies = Company::where('country',$country)->where('processed',1);
        }else{
            if($type == "incomplete" || $type == "no_wz_code"){
                $companies = Company::withTrashed();
            }else{
                $companies = Company::where('processed',1);
            }
        }
        if($type == "deleted"){
            $companies->onlyTrashed();
        }
        $dream = ($request->dream ? $request->dream : "");
        if($dream && $dream == "1"){
            $companies = $companies->where('dream',1);
        }
        $flag = ($request->flag ? $request->flag : "");
        if($flag && $flag != "all"){
            $companies = $companies->where('flag',$flag);
        }
        $search = $request->has('search') ? $request->search['value'] : "";
        if(!empty($search)){
            $companies = $companies->where(function($query) use ($search){
                $query->where('name','LIKE','%'.$search.'%')->orWhere('domain','LIKE','%'.$search.'%')->orWhere('country','LIKE','%'.$search.'%')->orWhere('revenue','LIKE','%'.$search.'%')->orWhere('wz_code','LIKE','%'.$search.'%')->orWhere('headcount','LIKE','%'.$search.'%');
            });
        }
        if($type !== 'all'){
            $companies->where(function($companies) use ($type){
                if($type == "incomplete"){
                    $companies = $companies->where(function($q){
                        $q->where('revenue',null)->orWhere('headcount',null)->orWhere('wz_code',null);
                    });
                }else if($type == "no_wz_code"){
                    $companies = $companies->where(function($q){
                        $q->whereNull('wz_code')->orWhere('wz_code','');
                    });
                }else if($type == "tam"){
                    $class = CompanyClassification::where('name','TAM')->first();
                    $companies->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                }else if($type == "sam"){
                    $class = CompanyClassification::where('name','SAM')->first();
                    $companies->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                }else if($type == "som"){
                    $class = CompanyClassification::where('name','SOM')->first();
                    $companies->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                }else if($type == "tam_samson4"){
                    $class = CompanyClassification::where('name','TAM - 4')->first();
                    $companies->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                }else if($type == "som_samson4"){
                    $class = CompanyClassification::where('name','SOM - 4')->first();
                    $companies->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                }else if($type == "sam_samson4"){
                    $class = CompanyClassification::where('name','SAM - 4')->first();
                    $companies->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                }else if($type == "som_samson4_oversized"){
                    $class = CompanyClassification::where('name','SOM - 4 Oversized')->first();
                    $companies->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                }else if($type == "sam_samson4_oversized"){
                    $class = CompanyClassification::where('name','SAM - 4 Oversized')->first();
                    $companies->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                }else if($type == "sam_4_diff"){
                    $query = clone $companies;
                    $companyIds = $query->whereHas('classifications',function($qy){
                        $qy->where('company_classification_id',5);
                    })->get()->pluck('id');
                    $companies->whereHas('classifications',function($q){
                        $q->where('company_classification_id',2)->where('company_classification_id',"!=",5);
                    })->whereNotIn('id',$companyIds);
                }else if($type == "som_4_diff"){
                    $query = clone $companies;
                    $companyIds = $query->whereHas('classifications',function($qy){
                        $qy->where('company_classification_id',6);
                    })->get()->pluck('id');
                    $companies->whereHas('classifications',function($q){
                        $q->where('company_classification_id',3);
                    })->whereNotIn('id',$companyIds);
                }
            })->orWhere('custom_classification', strtoupper($type));       
        }
        $offset = $request->start ? $request->start : 0;
        $limit = $request->length ? $request->length : 100;
        $duplicates = Company::where('domain','!=','')->select('domain',DB::raw('COUNT(*) as count'))->groupBy('domain')->havingRaw('count(*) > 1')->orderBy('count','desc')->get();
        $totalRecords = $companies->whereIn('domain',$duplicates->pluck('domain'))->select("id")->count();
        $companies = $companies->whereIn('domain',$duplicates->pluck('domain'))->select(["id","dream","name","domain","legal_name","country","revenue","headcount"])->orderBy('domain')->orderBy('revenue','asc')->offset($offset)->take($limit)->get();
        $companies = $companies->map(function($company){
            $company->domain = str_replace('www.','',$company->domain);
            if(!empty($company->domain)){
                $company->domain = '<a href="//'.$company->domain.'" target="_blank">'.$company->domain.'</a>';
            }
            if(!empty($company->name)){
                $company->name = '<a href="'.route('viewCompany',$company->id).'" target="_blank">'.$company->name.'</a>';
            }
            $company->actions = '<a href="'.route('editCompany',$company->id).'" target="_blank">Edit</a> | <a href="'.route('deleteCompanies',$company->id).'">Delete</a>';
            return $company;
        });
        return json_encode(["recordsTotal" => $totalRecords,"recordsFiltered" => $totalRecords,"data" => $companies]);
    }
}