<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Contact;
use App\Models\Setting;

class ContactScore implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    private $id;
    public function __construct($id){
        $this->id = $id;
    }
    public function handle(): void{
        try{
            $contact = Contact::with('schools')->find($this->id);
            if($contact){
                \Log::info("Contact ID: " . $contact->id);
                $totalScore = 0;
                $ageScores = Setting::where('site_key','age_score')->pluck('site_value')->first();
                $genderScores = Setting::where('site_key','gender_score')->pluck('site_value')->first();
                $cityScores = Setting::where('site_key','city_score')->pluck('site_value')->first();
                $educationScores = Setting::where('site_key','educational_score')->pluck('site_value')->first();
                $ageScores = $ageScores ? json_decode($ageScores,true) : [];
                $genderScores = $genderScores ? json_decode($genderScores,true) : [];
                $educationScores = $educationScores ? json_decode($educationScores,true) : [];

                if($contact->age){
                    foreach($ageScores as $ageScore){
                        if($contact->age >= $ageScore['min'] && $contact->age <= $ageScore['max']){
                            if($ageScore['score'] > 0){
                                $totalScore += (int) $ageScore['score'];
                            }
                            break;
                        }
                    }
                }
                if($contact->gender){
                    foreach($genderScores as $genderScore){
                        if($contact->gender == $genderScore['gender']){
                            if($genderScore['score'] > 0){
                                $totalScore += (int) $genderScore['score'];
                            }
                            break;
                        }
                    }
                }
                if($contact->location && $cityScores){
                    $totalScore += (int) $cityScores;
                }
                if($contact->schools->count() > 0){
                    foreach($contact->schools as $school){
                        foreach($educationScores as $educationScore){
                            if($school->type_of_control == $educationScore['type']){
                                if($educationScore['score'] > 0){
                                    $totalScore += (int) $educationScore['score'];
                                }
                                break;
                            }
                        }
                    }
                }
                $contact->total_score = $totalScore;
                $contact->save();
            }
        }catch(\Exception $e){
            \Log::error("Error: " . $e->getMessage());
        }
    }
}