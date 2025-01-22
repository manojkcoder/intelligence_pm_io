<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use App\Models\QuizResponse;
use App\Jobs\CompanyParentName;
use GuzzleHttp\Client;

class CompanyQAParent implements ShouldQueue
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
            // $qaResponses = $company->qa_responses ? json_decode($company->qa_responses,true) : [];
            $output = $this->fetchCompanyInfo($company->name);
            $output = str_contains(strtolower($output),'yes') ? 'yes' : 'no';
            $QuizResponse = QuizResponse::where('company_id',$company->id)->where('question_id',15)->first();
            if(!$QuizResponse){
                $QuizResponse = new QuizResponse();
                $QuizResponse->company_id = $company->id;
                $QuizResponse->question_id = 15;
            }
            $QuizResponse->answer = $output;
            $QuizResponse->save();
            // $qaResponses[] = ['question' => 'Does the company belong to another parent company?','answer' => $output];
            // $company->qa_responses = json_encode($qaResponses);
            // $company->save();
            // if(strtolower($output) === 'yes'){
            //     CompanyParentName::dispatch($company->id)->onQueue('perplexity');
            // }
            sleep(2);
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
                            "content" => "Based on the company name '".$company."', indicate if it belongs to a parent company. Respond with 'yes' or 'no'."
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