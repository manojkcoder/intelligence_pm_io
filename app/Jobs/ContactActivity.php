<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Activity;
use App\Models\Contact;

class ContactActivity implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    private $id;
    public function __construct($id){
        $this->id = $id;
    }
    public function handle(): void{
        try{
            $activity = Activity::where("id",$this->id)->first();
            if($activity){
                $activityData = json_decode($activity->response,true);
                if(isset($activityData["postUrl"]) && !empty($activityData["postUrl"])){
                    $activity->post_url = $activityData["postUrl"];
                }else if(isset($activityData["eventUrl"]) && !empty($activityData["eventUrl"])){
                    $activity->post_url = $activityData["eventUrl"];
                }
                if(isset($activityData["imgUrl"]) && !empty($activityData["imgUrl"])){
                    $activity->img_url = $activityData["imgUrl"];
                }
                if(isset($activityData["type"]) && !empty($activityData["type"])){
                    $activity->type = $activityData["type"];
                }
                if(isset($activityData["postContent"]) && !empty($activityData["postContent"])){
                    $activity->post_content = $activityData["postContent"];
                }
                if(isset($activityData["likeCount"])){
                    $activity->like_count = $activityData["likeCount"];
                }
                if(isset($activityData["commentCount"])){
                    $activity->comment_count = $activityData["commentCount"];
                }
                if(isset($activityData["repostCount"])){
                    $activity->repost_count = $activityData["repostCount"];
                }
                if(isset($activityData["postDate"]) && !empty($activityData["postDate"])){
                    $activity->post_date = $activityData["postDate"];
                }
                if(isset($activityData["commentContent"]) && !empty($activityData["commentContent"])){
                    $activity->comment_content = $activityData["commentContent"];
                }
                if(isset($activityData["commentUrl"]) && !empty($activityData["commentUrl"])){
                    $activity->comment_url = $activityData["commentUrl"];
                }
                if(isset($activityData["sharedPostUrl"]) && !empty($activityData["sharedPostUrl"])){
                    $activity->shared_post_url = $activityData["sharedPostUrl"];
                }
                if(isset($activityData["action"]) && !empty($activityData["action"])){
                    if($activityData["action"] == 'Post'){
                        $activity->action = "Post";
                    }else if(strpos($activityData["action"],'geteilt') !== false){
                        $activity->action = "Share";
                    }else if(strpos($activityData["action"],'gefällt') !== false){
                        $activity->action = "Like";
                    }else if(strpos($activityData["action"],'kommentiert') !== false || strpos($activityData["action"],'Kommentar lustig') !== false){
                        $activity->action = "Comment";
                    }else if(strpos($activityData["action"],'geantwortet') !== false){
                        $activity->action = "Reply";
                    }else if(strpos($activityData["action"],'applaudiert') !== false){
                        $activity->action = "Applauded";
                    }else if(strpos($activityData["action"],'findet das informativ') !== false){
                        $activity->action = "Informative";
                    }else if(strpos($activityData["action"],'findet das wunderbar') !== false || strpos($activityData["action"],'Network wunderbar') !== false){
                        $activity->action = "Wonderful";
                    }else if(strpos($activityData["action"],'findet das lustig') !== false){
                        $activity->action = "findet das lustig";
                    }else if(strpos($activityData["action"],'unterstützt dies') !== false){
                        $activity->action = "Supports";
                    }else if(strpos($activityData["action"],'Kommentar wunderbar') !== false){
                        $activity->action = "Comment Wonderful";
                    }else if(strpos($activityData["action"],'unterstützt') !== false){
                        $activity->action = "Supports Comment";
                    }else if(strpos($activityData["action"],'beigetragen') !== false){
                        $activity->action = "Contributed";
                    }else if(strpos($activityData["action"],'inspirierend') !== false){
                        $activity->action = "Inspiring";
                    }else if(strpos($activityData["action"],'Applaus reagiert') !== false){
                        $activity->action = "Respond";
                    }
                }
                if(isset($activityData["author"]) && !empty($activityData["author"])){
                    $activity->author = $activityData["author"];
                }
                if(isset($activityData["profileUrl"]) && !empty($activityData["profileUrl"])){
                    $linkedinUrl = rtrim(urldecode($activityData["profileUrl"]),'/');
                    $linkedinUrl = str_replace('https://www.linkedin.com','https://linkedin.com',$linkedinUrl);
                    $activity->profile_url = $linkedinUrl;
                    $contact = Contact::where('linkedin',$linkedinUrl)->first();
                    if($contact){
                        $activity->contact_id = $contact->id;
                    }
                }
                if(isset($activityData["timestamp"]) && !empty($activityData["timestamp"])){
                    $activity->timestamp = date("Y-m-d H:i:s",strtotime($activityData["timestamp"]));
                }
                if(isset($activityData["postTimestamp"]) && !empty($activityData["postTimestamp"])){
                    $activity->post_timestamp = date("Y-m-d H:i:s",strtotime($activityData["postTimestamp"]));
                }
                $activity->processed = 1;
                $activity->save();
            }
        }catch(\Exception $e){
            \Log::error("Error: " . $e->getMessage());
        }
    }
}