<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Contact;
use GuzzleHttp\Client;

class DomainResearch implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    private $client;
    private $id;
    private $name;
    private $title;
    private $company;
    public function __construct($id,$name,$title,$company){
        $this->id = $id;
        $this->name = $name;
        $this->title = $title;
        $this->company = $company;
    }
    public function handle(): void{
        $this->client = new Client();
        $output = $this->fetchContactsInfo($this->name,$this->title,$this->company);
        $contact = Contact::where("id",$this->id)->first();
        try{
            if($contact){
                $contact->domain = $output ?? null;
                $contact->save();
            }
            sleep(2);
        }catch(\Exception $e){
            \Log::error("Error: " . $e->getMessage());
        }
    }
    private function fetchContactsInfo($name,$title,$company){
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
                            "role" => "user",
                            "content" => "Try to deduce the website domain of this person's company: " . $name . ", " . $title . " of " . $company . ", return just the domain of the website like amazon.com"
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