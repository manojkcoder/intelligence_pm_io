<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Activity;
use App\Models\Contact;
use App\Jobs\ContactActivity;

class ImportActivities implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;
    public $timeout = 100000;
    // public $tries = 5;
    public $failOnTimeout = false;
    private $filePath;
    public function __construct($filePath){
        $this->filePath = $filePath;
    }
    public function handle(): void{
        try{
            $jsonPath = Storage::path($this->filePath);
            if(!File::exists($jsonPath)){
                \Log::error("Error: JSON file not found at {$jsonPath}");
                return;
            }
            $activities = json_decode(File::get($jsonPath),true);
            if(!is_array($activities)){
                \Log::error("Error: Invalid JSON structure in file.");
                return;
            }
            foreach($activities as $activityData){
                if(!is_array($activityData)){
                    \Log::warning("Skipping invalid activity data: " . json_encode($activityData));
                    continue;
                }
                $this->processActivity($activityData);
            }
        }catch(\Exception $e){
            \Log::error("Error: " . $e->getMessage());
        }
    }
    private function processActivity($activityData){
        $linkedinUrl = isset($activityData["profileUrl"]) && !empty($activityData["profileUrl"]) ? rtrim(urldecode($activityData["profileUrl"]),"/") : null;
        if($linkedinUrl){
            $linkedinUrl = str_replace("https://www.linkedin.com","https://linkedin.com",$linkedinUrl);
        }
        $urlKey = $activityData["postUrl"] ?? $activityData["eventUrl"] ?? null;
        $activity = $urlKey ? Activity::where("post_url",$urlKey)->first() : null;
        if(!$activity){
            $activity = new Activity();
        }else if($activity && $linkedinUrl && $activity->linkedin != $linkedinUrl){
            $activity = new Activity();
        }
        $type = $activityData["type"] ?? null;
        if($type === "Video (LinkedIn Source)"){
            $type = "Video";
        }
        $activity->post_url = $urlKey;
        $activity->img_url = $activityData["imgUrl"] ?? null;
        $activity->type = $type;
        $activity->post_content = $activityData["postContent"] ?? null;
        $activity->like_count = $activityData["likeCount"] ?? 0;
        $activity->comment_count = $activityData["commentCount"] ?? 0;
        $activity->repost_count = $activityData["repostCount"] ?? 0;
        $activity->post_date = $activityData["postDate"] ?? null;
        $activity->comment_content = $activityData["commentContent"] ?? null;
        $activity->comment_url = $activityData["commentUrl"] ?? null;
        $activity->shared_post_url = $activityData["sharedPostUrl"] ?? null;
        $activity->action = $this->mapAction($activityData["action"] ?? null);
        $activity->author = $activityData["author"] ?? null;
        $activity->profile_url = $linkedinUrl;
        if($linkedinUrl){
            $contact = Contact::where("linkedin",$linkedinUrl)->first();
            if ($contact) {
                $activity->contact_id = $contact->id;
            }
        }
        $activity->timestamp = isset($activityData["timestamp"]) ? date("Y-m-d H:i:s",strtotime($activityData["timestamp"])) : null;
        $activity->post_timestamp = isset($activityData["postTimestamp"]) ? date("Y-m-d H:i:s",strtotime($activityData["postTimestamp"])) : null;
        $activity->response = json_encode($activityData);
        $activity->processed = 1;
        $activity->save();
        return $activity;
    }
    private function mapAction($action){
        if(!$action){
            return null;
        }
        $actionMap = [
            "Post" => "Post",
            "geteilt" => "Share",
            "gefällt" => "Like",
            "kommentiert" => "Comment",
            "geantwortet" => "Reply",
            "applaudiert" => "Applauded",
            "findet das informativ" => "Informative",
            "findet das wunderbar" => "Wonderful",
            "findet das lustig" => "Funny",
            "unterstützt dies" => "Supports",
            "beigetragen" => "Contributed",
            "inspirierend" => "Inspiring",
            "Applaus reagiert" => "Respond"
        ];
        foreach($actionMap as $key => $value){
            if(strpos($action,$key) !== false){
                return $value;
            }
        }
        return null;
    }
}