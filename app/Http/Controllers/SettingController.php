<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index(Request $request){
        $ageScores = Setting::where('site_key','age_score')->pluck('site_value')->first();
        $genderScores = Setting::where('site_key','gender_score')->pluck('site_value')->first();
        $cityScores = Setting::where('site_key','city_score')->pluck('site_value')->first();
        $educationScores = Setting::where('site_key','educational_score')->pluck('site_value')->first();
        $sharedContactScores = Setting::where('site_key','shared_contacts_score')->pluck('site_value')->first();
        $sharedOrganizationScores = Setting::where('site_key','shared_organizations_score')->pluck('site_value')->first();
        $activityScores = Setting::where('site_key','activities_score')->pluck('site_value')->first();
        $ageScores = $ageScores ? json_decode($ageScores,true) : [];
        $genderScores = $genderScores ? json_decode($genderScores,true) : [];
        $educationScores = $educationScores ? json_decode($educationScores,true) : [];
        $sharedContactScores = $sharedContactScores ? json_decode($sharedContactScores,true) : [];
        $sharedOrganizationScores = $sharedOrganizationScores ? json_decode($sharedOrganizationScores,true) : [];
        $activityScores = $activityScores ? json_decode($activityScores,true) : [];
        return view('settings',compact("ageScores","genderScores","cityScores","educationScores","sharedContactScores","sharedOrganizationScores","activityScores"));
    }
    public function update(Request $request){
        if($request->has('age')){
            Setting::updateOrCreate(['site_key' => 'age_score'],['site_value' => json_encode($request->age)]);
        }else if($request->has('gender')){
            Setting::updateOrCreate(['site_key' => 'gender_score'],['site_value' => json_encode($request->gender)]);
        }else if($request->has('cityScores')){
            Setting::updateOrCreate(['site_key' => 'city_score'],['site_value' => $request->cityScores]);
        }else if($request->has('education')){
            Setting::updateOrCreate(['site_key' => 'educational_score'],['site_value' => json_encode($request->education)]);
        }else if($request->has('sharedContact')){
            Setting::updateOrCreate(['site_key' => 'shared_contacts_score'],['site_value' => json_encode($request->sharedContact)]);
        }else if($request->has('sharedOrganization')){
            Setting::updateOrCreate(['site_key' => 'shared_organizations_score'],['site_value' => json_encode($request->sharedOrganization)]);
        }else if($request->has('activity')){
            Setting::updateOrCreate(['site_key' => 'activities_score'],['site_value' => json_encode($request->activity)]);
        }
        return redirect()->route('settings');
    }
}