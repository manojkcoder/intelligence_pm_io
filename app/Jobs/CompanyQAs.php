<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use App\Jobs\CompanyQAParent;
use GuzzleHttp\Client;

class CompanyQAs implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    private $client;
    private $id;
    public function __construct($id){
        $this->id = $id;
    }
    public function handle(): void{
        $this->client = new Client();
        $company = Company::withTrashed()->where('id',$this->id)->first();
        if($company){
            $qaResponses = $company->qa_responses ? json_decode($company->qa_responses,true) : [];
            $output = $this->fetchCompanyInfo($company->name);
            if(strpos($output,"```json") !== false){
                $output = explode("```json",$output);
                $output = explode("```",$output[1]);
                if(strpos($output[0],'\": \"') !== false){
                    $company->qa_responses = json_encode($output[0]);
                }else{
                    $company->qa_responses = json_encode(json_decode(str_replace(["{'","', '","':'","' : '","': '","' :'","'}"],['{"','","','":"','":"','":"','":"','"}'],$output[0]),true));
                }
                $company->save();
                CompanyQAParent::dispatch($company->id)->onQueue('perplexity');
                sleep(2);
            }
        }
    }
    private function fetchCompanyInfo($company){
        try{
            $response = $this->client->post("https://api.perplexity.ai/chat/completions",[
                "headers" => [
                    "Authorization" => "Bearer " . env("PERPLEXITY_API_KEY"),
                    "Content-Type" => "application/json"
                ],
                "json" => [
                    "model" => "llama-3.1-sonar-small-128k-online",
                    "messages" => [
                        [
                            "role" => "user",
                            "content" => "Based on the company name '".$company."', Answer the following questions, The output provide in json format like as [{'question':'','answer':''}]. The Questions are as follow:\nQ1. Is the company a division of a large corporation?\nYes -> TAM\nNo -> SAM\nQ2. Is the company independent (not part of a large group as a division?)\nYes -> SAM\nNo -> TAM\nQ3. Is the headquarters not located in German country?\nQ4. Is it a state-owned company?\nYes -> TAM\nNo -> SAM\nQ5. Is there any information on innovations made (R&D costs, warehouses built, production built, acquisitions, etc.)?\nQ6. How long has the CEO & CFO been at the start?\nQ7. What did the CEO / CFO do before?\nQ8. In which channels are CEO / CFO active online (LinkedIn vs dogs in need vs not at all).\nQ9. Which posts do CEO / CFO react to in online channels?\nQ10. Were CEO / CFO part of a podcast / book / etc. ?\nQ11. Are there signs that the CEO or CFO is interested in a better implementation of one of the topics?\n- Transformation\n- Strategy\n- Performance programme\nQ12. Are CEO & CFO associated with innovation? If Yes, please detail in a paragraph\nQ13. Is there a known strategy of the company publicly available? If Yes, please detail in a paragraph\nQ14. Is there a showcase with consultants for the company? If Yes, please detail in a paragraph"
                        ]
                    ],
                    "max_tokens" => 2000,
                    "temperature" => 0.2,
                    "top_p" => 0.9,
                    "search_domain_filter" => ["perplexity.ai"],
                    "return_images" => false,
                    "return_related_questions" => false,
                    "search_recency_filter" => "month",
                    "top_k" => 0,
                    "stream" => false,
                    "presence_penalty" => 0,
                    "frequency_penalty" => 1
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