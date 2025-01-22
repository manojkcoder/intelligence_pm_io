<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Activity;
use GuzzleHttp\Client;

class KeywordsResearch implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    private $client;
    private $id;
    private $content;
    public function __construct($id,$content){
        $this->id = $id;
        $this->content = $content;
    }
    public function handle(): void{
        $this->client = new Client();
        $output = $this->fetchInfo($this->content);
        $activity = Activity::where("id",$this->id)->first();
        try{
            if($activity){
                if(stripos($output,"relevant") !== false && stripos($output,"not relevant") !== false){
                    $activity->is_relevant = 1;
                }else{
                    $activity->is_relevant = 0;
                }
                $activity->save();
            }
            sleep(2);
        }catch(\Exception $e){
            \Log::error("Error: " . $e->getMessage());
        }
    }
    private function fetchInfo($content){
        try{
            $response = $this->client->post("https://api.openai.com/v1/chat/completions",[
                "headers" => [
                    "Authorization" => "Bearer " . env("OPENAI_API_KEY"),
                    "Content-Type" => "application/json"
                ],
                "json" => [
                    "model" => "gpt-4o-mini",
                    "messages" => [
                        [
                            "role" => "system",
                            "content" => "You are a model that evaluates the relevance of content to business offers related to process improvement, efficiency, and technology adoption."
                        ],
                        [
                            "role" => "user",
                            "content" => "Is the following content relevant to a business offer related to efficiency, process optimization, or technology adoption?\n\n$content"
                        ]
                    ],
                    "max_tokens" => 200
                ]
            ]);
            $body = $response->getBody();
            $data = json_decode($body,true);
            \Log::info("API Response: ",$data);
            return trim($data["choices"][0]["message"]["content"]);
        }catch(\Exception $e){
            \Log::error("API Error: " . $e->getMessage());
            dd($e);
            return "Error";
        }
    }
}