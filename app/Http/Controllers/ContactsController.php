<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Company;
use App\Models\CompanyClassification;
use Illuminate\Support\Facades\DB;

class ContactsController extends Controller
{
    public function getContacts(Request $request){
        $contactsCountQuery = Contact::query();
        $contactsQuery = Contact::with('company');
        if($request->input('type') && $request->input('type') != "all"){
            if($request->input('type') == 'likes'){
                $contactsCountQuery->whereHas('likeComments',function($query){
                    $query->where('is_like',1);
                });
                $contactsQuery->whereHas('likeComments',function($query){
                    $query->where('is_like',1);
                });
            }elseif($request->input('type') == 'comments'){
                $contactsCountQuery->whereHas('likeComments',function($query){
                    $query->where('is_comment',1);
                });
                $contactsQuery->whereHas('likeComments',function($query){
                    $query->where('is_comment',1);
                });
            }else{
                $contactsCountQuery->whereHas('likeComments',function($query){
                    $query->where('is_comment',1);
                    $query->orWhere('is_like',1);
                });
                $contactsQuery->whereHas('likeComments',function($query){
                    $query->where('is_comment',1);
                    $query->orWhere('is_like',1);
                });
            }
        }
        $country = ($request->country ? $request->country : "");
        if($country && $country != "all"){
            $contactsCountQuery->where('country', $country);
            $contactsQuery->where('country', $country);
        }
        $dream = ($request->dream ? $request->dream : "");
        if($dream && $dream == "1"){
            $contactsQuery->whereHas('company', function($query){
                $query->where('dream', 1);
            });
            $contactsCountQuery->whereHas('company', function($query){
                $query->where('dream', 1);
            });
        }
        $flag = ($request->flag ? $request->flag : "");
        if($flag && $flag != "all"){
            $contactsQuery->whereHas('company', function($query) use($flag){
                $query->where('flag',$flag);
            });
            $contactsCountQuery->whereHas('company', function($query) use($flag){
                $query->where('flag',$flag);
            });
        }
        $search = $request->has('search') ? $request->search['value'] : "";
        if($search){
            $contactsCountQuery->where(function($query) use($search){
                $query->where('first_name','like','%'.$search.'%')
                    ->orWhere('last_name','like','%'.$search.'%')
                    ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"),'like','%'.$search.'%');
            });
            $contactsQuery->where(function($query) use($search){
                $query->where('first_name','like','%'.$search.'%')
                    ->orWhere('last_name','like','%'.$search.'%')
                    ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"),'like','%'.$search.'%');
            });
        }
        if($request->input('filter') && $request->input('filter') != "all"){
            if($request->input('filter') == 'tam'){
                $class = CompanyClassification::where('name','TAM')->first();
                $contactsCountQuery->whereHas('company',function($query) use ($class){
                    $query->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                });
            }else if($request->input('filter') == 'sam'){
                $class = CompanyClassification::where('name','SAM')->first();
                $contactsCountQuery->whereHas('company',function($query) use ($class){
                    $query->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                });
            }else if($request->input('filter') == 'som'){
                $class = CompanyClassification::where('name','SOM')->first();
                $contactsCountQuery->whereHas('company',function($query) use ($class){
                    $query->whereHas('classifications',function($q) use ($class){
                        $q->where('company_classification_id',$class->id);
                    });
                });
            }
        }
        $contactsQuery->withCount(['likeComments as likes_count' => function($query){
            $query->where('is_like', 1);
        },'likeComments as comments_count' => function($query){
            $query->where('is_comment', 1);
        }]);
        $search = $request->has('search') ? $request->search['value'] : "";
        $offset = $request->start ? $request->start : 0;
        $limit = $request->length ? $request->length : 100;
        $totalRecords = $contactsCountQuery->select("id")->count();
        $contacts = $contactsQuery->orderBy("comments_count", 'DESC')->offset($offset)->take($limit)->get();
        $contacts = $contacts->map(function($contact){
            $contact->actions .= '<a href="'.route('viewContact',$contact->id).'" class="btn-bg-secondary text-white font-bold py-2 px-4 mr-2"><i class="fas fa-eye"></i></a>';
            return $contact;
        });
        return json_encode(["recordsTotal" => $totalRecords,"recordsFiltered" => $totalRecords,"data" => $contacts]);
    }
    public function allContacts(Request $request){
        $countries = Company::select('country')->distinct()->get()->pluck('country');
        $flags = Company::select('flag')->distinct()->get()->pluck('flag');
        $companies = Company::whereIn('id',Contact::select('company_id')->distinct()->get())->get();
        $params = $request->all();
        $pageUrl = route('contacts.get',$params);
        return view('contacts',compact("pageUrl","companies","countries","flags"));
    }
    public function contacts(Request $request,$id){
        $company = Company::find($id);
        $contact = new Contact();
        return view('create_contact',compact("company","contact"));
    }
    public function viewContact($id){
        $contact = Contact::with('company','jobs','licences','schools','connections')->find($id);
        return view('view-contact',compact('contact'));
    }
    public function createContact(Request $request,$id){
        Company::find($id)->contacts()->create($request->all());
        return redirect()->route('viewCompany',$id);
    }
    public function editContact(Request $request,$id){
        $contact = Contact::find($id);
        return view('edit_contact',compact("contact"));
    }
    public function updateContact(Request $request,$id){
        $contact = Contact::find($id);
        $contact->update($request->all());
        return redirect()->route('contacts.all');
    }
    public function deleteContact(Request $request,$id){
        $contact = Contact::find($id);
        $contact->delete();
        return redirect()->route('contacts.all');
    }
}