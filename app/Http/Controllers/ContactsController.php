<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Company;
use App\Models\CompanyClassification;

class ContactsController extends Controller
{

    public function getContacts(Request $request){
        $contactsCountQuery = Contact::query();
        $contactsQuery = Contact::with('company');
        if($request->input('type') && $request->input('type') != "all"){
            if($request->input('type') == 'likes'){
                $contactsCountQuery->whereHas('likeComments', function($query){
                    $query->where('is_like', 1);
                });
                $contactsQuery->whereHas('likeComments', function($query){
                    $query->where('is_like', 1);
                });
            }elseif($request->input('type') == 'comments'){
                $contactsCountQuery->whereHas('likeComments', function($query){
                    $query->where('is_comment', 1);
                });
                $contactsQuery->whereHas('likeComments', function($query){
                    $query->where('is_comment', 1);
                });
            }else{
                $contactsCountQuery->whereHas('likeComments', function($query){
                    $query->where('is_comment', 1);
                    $query->orWhere('is_like', 1);
                });
                $contactsQuery->whereHas('likeComments', function($query){
                    $query->where('is_comment', 1);
                    $query->orWhere('is_like', 1);
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
        if($request->input('filter') && $request->input('filter') != "all"){
            if($request->input('filter') == 'tam'){
                $class = CompanyClassification::where('name','TAM')->first();
                $wz_codes = $class->wz_codes ? json_decode($class->wz_codes) : [];
            }else if($request->input('filter') == 'sam'){
                $class = CompanyClassification::where('name','SAM')->first();
                $wz_codes = $class->wz_codes ? json_decode($class->wz_codes) : [];
            }else if($request->input('filter') == 'som'){
                $class = CompanyClassification::where('name','SOM')->first();
                $wz_codes = $class->wz_codes ? json_decode($class->wz_codes) : [];
            }
            $contactsCountQuery->whereHas('company', function($query) use ($wz_codes){
                $query->where('wz_code','LIKE','%'. $wz_codes[0]);
                for($i = 1; $i < count($wz_codes); $i++){
                    $query->orWhere('wz_code','LIKE','%'. $wz_codes[$i]);
                }
            });
            $contactsQuery->whereHas('company', function($query) use ($wz_codes){
                $query->where('wz_code','LIKE','%'. $wz_codes[0]);
                for($i = 1; $i < count($wz_codes); $i++){
                    $query->orWhere('wz_code','LIKE','%'. $wz_codes[$i]);
                }
            });
        }
        $contactsQuery->withCount(['likeComments as likes_count' => function($query){
            $query->where('is_like', 1);
        }, 'likeComments as comments_count' => function($query){
            $query->where('is_comment', 1);
        }]);
        $search = $request->has('search') ? $request->search['value'] : "";
        $offset = $request->start ? $request->start : 0;
        $limit = $request->length ? $request->length : 100;
        $totalRecords = $contactsCountQuery->select("id")->count();
        $contacts = $contactsQuery->orderBy("comments_count", "DESC")->offset($offset)->take($limit)->get();
        return json_encode(["recordsTotal" => $totalRecords,"recordsFiltered" => $totalRecords,"data" => $contacts]);
    }

    public function allContacts(Request $request){
        $countries = Company::select('country')->distinct()->get()->pluck('country');
        $flags = Company::select('flag')->distinct()->get()->pluck('flag');
        $companies = Company::whereIn('id', Contact::select('company_id')->distinct()->get())->get();
        $params = $request->all();
        $pageUrl = route('contacts.get', $params);
        return view('contacts',compact("pageUrl", "companies", "countries", "flags"));
    }

    public function contacts(Request $request, $id){
        $company = Company::find($id);
        $contact = new Contact();
        return view('create_contact',compact("company", "contact"));
    }

    public function createContact(Request $request, $id){
        Company::find($id)->contacts()->create($request->all());
        return redirect()->route('viewCompany', $id);
    }

    public function editContact(Request $request, $id){
        $contact = Contact::find($id);
        return view('edit_contact',compact("contact"));
    }

    public function updateContact(Request $request, $id){
        $contact = Contact::find($id);
        $contact->update($request->all());
        return redirect()->route('contacts.all');
    }

    public function deleteContact(Request $request, $id){
        $contact = Contact::find($id);
        $contact->delete();
        return redirect()->route('contacts.all');
    }

}
