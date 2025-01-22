<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Activity;
use App\Models\Contact;

class ActivityRate implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    private $id;
    public function __construct($id){
        $this->id = $id;
    }
    public function handle(): void{
        try{
            $contact = Contact::where("id",$this->id)->first();
            if($contact){
                $posts = Activity::where('contact_id',$contact->id)->where('action','Post')->count();
                $comments = Activity::where('contact_id',$contact->id)->where('action','Comment')->count();
                $shares = Activity::where('contact_id',$contact->id)->where('action','Share')->count();
                $reactions = Activity::where('contact_id',$contact->id)->whereIn('action',['Comment Wonderful','Wonderful'])->count();
                $contact->posts = $posts;
                $contact->comments = $comments;
                $contact->shares = $shares;
                $contact->reactions = $reactions;
                $contact->activity_rate = (($posts * 3) + ($comments * 2) + ($reactions * 1) + ($shares * 2));
                $contact->avg_activity_rate = round((($posts + $comments + $reactions + $shares)/12),2);
                $contact->save();
            }
        }catch(\Exception $e){
            \Log::error("Error: " . $e->getMessage());
        }
    }
}