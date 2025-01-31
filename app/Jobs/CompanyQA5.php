<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use App\Models\QuizResponse;
use GuzzleHttp\Client;

class CompanyQA5 implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;
    public $timeout = 100000;
    private $client;
    private $id;
    public function __construct($id){
        $this->id = $id;
    }
    public function handle(): void{
        $this->client = new Client();
        $company = Company::withTrashed()->where('id',$this->id)->first();
        if($company){
            $output = $this->fetchCompanyInfo($company->name);
            $QuizResponse = QuizResponse::where('company_id',$company->id)->where('question_id',5)->first();
            if(!$QuizResponse){
                $QuizResponse = new QuizResponse();
                $QuizResponse->company_id = $company->id;
                $QuizResponse->question_id = 5;
            }
            $QuizResponse->answer = $output;
            $QuizResponse->save();
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
                            "content" => "Based on the company name '".$company."', is there any available information on innovations such as R&D costs, new warehouses, production facilities, or acquisitions? If yes, please provide a brief summary."
                        ]
                    ],
                    "max_tokens" => 200,
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