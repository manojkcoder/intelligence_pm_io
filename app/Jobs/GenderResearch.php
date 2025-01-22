<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Contact;
use GuzzleHttp\Client;

class GenderResearch implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    private $client;
    private $id;
    private $name;
    private $company;
    public function __construct($id,$name,$company){
        $this->id = $id;
        $this->name = $name;
        $this->company = $company;
    }
    public function handle(): void{
        $this->client = new Client();
        $output = $this->fetchContactsInfo($this->name,$this->company);
        $contact = Contact::where("id",$this->id)->first();
        try{
            if($contact){
                $lines = explode("\n",$output);
                foreach($lines as $line){
                    if(!empty($line)){
                        if(strpos($line,'Gender:') !== false){
                            $gender = trim(explode(":",$line)[1]);
                            $contact->gender = $gender ?? null;
                            $contact->save();
                        }else if(strpos($line,'Age:') !== false){
                            $age = trim(explode(":",$line)[1]);
                            $contact->age = $age ?? null;
                            $contact->save();
                        }
                    }
                }
            }
            sleep(2);
        }catch(\Exception $e){
            \Log::error("Error: " . $e->getMessage());
        }
    }
    private function fetchContactsInfo($name,$company){
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
                            "content" => "Based on the name '".$name."' and the company name '".$company."', determine the likely gender and age of the person. Please respond with 'male', 'female', or 'unknown' for gender, and an age (e.g., 20,25,30,35,40,45,50,55,60) and output like Gender: \nAge: "
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