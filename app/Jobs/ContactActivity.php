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
    private $activityData;
    public function __construct($id,$data){
        $this->id = $id;
        $this->activityData = $data;
    }
    public function handle(): void{
        try{
            $activity = Activity::where("id",$this->id)->first();
            if($activity){
                if(isset($this->activityData["postUrl"]) && !empty($this->activityData["postUrl"])){
                    $activity->post_url = $this->activityData["postUrl"];
                }
                if(isset($this->activityData["imgUrl"]) && !empty($this->activityData["imgUrl"])){
                    $activity->img_url = $this->activityData["imgUrl"];
                }
                if(isset($this->activityData["type"]) && !empty($this->activityData["type"])){
                    $activity->type = $this->activityData["type"];
                }
                if(isset($this->activityData["postContent"]) && !empty($this->activityData["postContent"])){
                    $activity->post_content = $this->activityData["postContent"];
                }
                if(isset($this->activityData["likeCount"])){
                    $activity->like_count = $this->activityData["likeCount"];
                }
                if(isset($this->activityData["commentCount"])){
                    $activity->comment_count = $this->activityData["commentCount"];
                }
                if(isset($this->activityData["repostCount"])){
                    $activity->repost_count = $this->activityData["repostCount"];
                }
                if(isset($this->activityData["postDate"]) && !empty($this->activityData["postDate"])){
                    $activity->post_date = $this->activityData["postDate"];
                }
                if(isset($this->activityData["commentContent"]) && !empty($this->activityData["commentContent"])){
                    $activity->comment_content = $this->activityData["commentContent"];
                }
                if(isset($this->activityData["commentUrl"]) && !empty($this->activityData["commentUrl"])){
                    $activity->comment_url = $this->activityData["commentUrl"];
                }
                if(isset($this->activityData["sharedPostUrl"]) && !empty($this->activityData["sharedPostUrl"])){
                    $activity->shared_post_url = $this->activityData["sharedPostUrl"];
                }
                if(isset($this->activityData["action"]) && !empty($this->activityData["action"])){
                    if($this->activityData["action"] == 'Post'){
                        $activity->action = "Post";
                    }else if(strpos($this->activityData["action"],'geteilt') !== false){
                        $activity->action = "Share";
                    }else if(strpos($this->activityData["action"],'gefällt') !== false){
                        $activity->action = "Like";
                    }else if(strpos($this->activityData["action"],'kommentiert') !== false || strpos($this->activityData["action"],'Kommentar lustig') !== false){
                        $activity->action = "Comment";
                    }else if(strpos($this->activityData["action"],'geantwortet') !== false){
                        $activity->action = "Reply";
                    }else if(strpos($this->activityData["action"],'applaudiert') !== false){
                        $activity->action = "Applauded";
                    }else if(strpos($this->activityData["action"],'findet das informativ') !== false){
                        $activity->action = "Informative";
                    }else if(strpos($this->activityData["action"],'findet das wunderbar') !== false || strpos($this->activityData["action"],'Network wunderbar') !== false){
                        $activity->action = "Wonderful";
                    }else if(strpos($this->activityData["action"],'findet das lustig') !== false){
                        $activity->action = "findet das lustig";
                    }else if(strpos($this->activityData["action"],'unterstützt dies') !== false){
                        $activity->action = "Supports";
                    }else if(strpos($this->activityData["action"],'Kommentar wunderbar') !== false){
                        $activity->action = "Comment Wonderful";
                    }else if(strpos($this->activityData["action"],'unterstützt') !== false){
                        $activity->action = "Supports Comment";
                    }else if(strpos($this->activityData["action"],'beigetragen') !== false){
                        $activity->action = "Contributed";
                    }else if(strpos($this->activityData["action"],'inspirierend') !== false){
                        $activity->action = "Inspiring";
                    }else if(strpos($this->activityData["action"],'Applaus reagiert') !== false){
                        $activity->action = "Respond";
                    }
                }
                if(isset($this->activityData["author"]) && !empty($this->activityData["author"])){
                    $activity->author = $this->activityData["author"];
                }
                if(isset($this->activityData["profileUrl"]) && !empty($this->activityData["profileUrl"])){
                    $linkedinUrl = rtrim(urldecode($this->activityData["profileUrl"]),'/');
                    $linkedinUrl = str_replace('https://www.linkedin.com','https://linkedin.com',$linkedinUrl);
                    $activity->profile_url = $linkedinUrl;
                }
                if(isset($this->activityData["timestamp"]) && !empty($this->activityData["timestamp"])){
                    $activity->timestamp = date("Y-m-d H:i:s",strtotime($this->activityData["timestamp"]));
                }
                if(isset($this->activityData["postTimestamp"]) && !empty($this->activityData["postTimestamp"])){
                    $activity->post_timestamp = date("Y-m-d H:i:s",strtotime($this->activityData["postTimestamp"]));
                }
                $activity->save();
            }
        }catch(\Exception $e){
            \Log::error("Error: " . $e->getMessage());
        }
    }
}