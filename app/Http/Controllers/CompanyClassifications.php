<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ClassificationRequest;
use App\Models\CompanyClassification;
use App\Models\Industry;
use App\Models\NaicsIndustry;

class CompanyClassifications extends Controller
{
    public function index(){
        $classifications = CompanyClassification::get();
        return view("classifications.list",compact("classifications"));
    }
    public function edit($id){
        $classification = CompanyClassification::findOrFail($id);
        $industries = Industry::orderBy("wz_code","ASC")->get();
        $naics_industries = NaicsIndustry::orderBy("code","ASC")->get();
        return view("classifications.edit",compact("classification","industries","naics_industries"));
    }
    public function update(ClassificationRequest $request,$id){
        $classification = CompanyClassification::findOrFail($id);
        $classification->update($request->only("name","description","revenue_threshold","revenue_max","employee_threshold","employee_max"));
        $wz_codes = $request->wz_codes;
        $wz_codes = array_unique($wz_codes);
        sort($wz_codes);
        $naics_codes = $request->naics_codes;
        $naics_codes = array_unique($naics_codes);
        sort($naics_codes);
        $classification->wz_codes = $wz_codes;
        $classification->naics_codes = $naics_codes;
        $classification->save();
        if($request->expectsJson()){
            return response()->json(["status"=>"Classification updated successfully."]);
        }
        return Redirect::route("classifications.index")->with("status","Classification updated successfully.");
    }
    public function destroy($id){
        $classification = CompanyClassification::findOrFail($id);
        $classification->delete();
        return Redirect::route("classifications.index")->with("status","Classification deleted successfully.");
    }
}