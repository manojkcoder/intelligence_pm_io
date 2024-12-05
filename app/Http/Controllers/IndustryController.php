<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Industry;
use App\Models\CompanyClassification;

class IndustryController extends Controller
{
    // edit
    public function editIndustry(Request $request){
        $industry = Industry::find($request->id);
        return view('industries.edit',compact('industry'));
    }
    // update
    public function updateIndustry(Request $request){
        $industry = Industry::find($request->id);
        $industry->update($request->all());
        return redirect()->route('industries')->with('success','Industry updated successfully');
    }

    public function industries(Request $request){
        if($request->has('all')){
            $industries = Industry::all();
        }else if($request->has('tam')){
            $class = CompanyClassification::where('name','TAM')->first();
            $class->wz_codes = $class->wz_codes ? json_decode($class->wz_codes) : [];
            $industries = Industry::whereIn('wz_code',$class->wz_codes)->get();
        }else if($request->has('sam')){
            $class = CompanyClassification::where('name','SAM')->first();
            $class->wz_codes = $class->wz_codes ? json_decode($class->wz_codes) : [];
            $industries = Industry::whereIn('wz_code',$class->wz_codes)->get();
        }else if($request->has('som')){
            $class = CompanyClassification::where('name','SOM')->first();
            $class->wz_codes = $class->wz_codes ? json_decode($class->wz_codes) : [];
            $industries = Industry::whereIn('wz_code',$class->wz_codes)->get();
        }else{
            $industries = Industry::all();
        }
        return view('industries',compact('industries'));
    }

    public function updateIndustriesStatus(Request $request){
        $enabled_industries = $request->input('industries');
        Industry::whereNotIn('id',$enabled_industries)->update(['enabled' => false]);
        Industry::whereIn('id',$enabled_industries)->update(['enabled' => true]);
        return redirect()->route('industries');
    }
}
