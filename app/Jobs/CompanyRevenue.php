<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use GuzzleHttp\Client;

class CompanyRevenue implements ShouldQueue
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
            $output = $this->fetchCompanyRevenue($company->name,$company->country);
            $revenue = (float) $output;
            $revenue = floatval($revenue / 1000000);
            $company->revenue = $revenue;
            $company->save();
        }
    }
    private function fetchCompanyInfo($companyName,$country){
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
                            "content" => "As a research assistant for a consultancy company, you are tasked to research the yearly revenue (in millions) for a company named $companyName based in $country. providing only the numeric value without any additional text, explanations, or labels"
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
    private function fetchCompanyRevenue($companyName,$country){
        try{
            $response = $this->client->post('https://api.openai.com/v1/chat/completions',[
                'headers' => [
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => "As a research assistant for a consultancy company, you are tasked to research the following details for a company named $companyName based in $country: \n\n1.	Yearly revenue.\n\nUtilize the company’s annual reports or other reliable internet sources, or else make an educated guess. Please respond with each detail on a separate line, providing only the numeric value without any additional text, explanations, or labels."
                        ],
                    ],
                    'max_tokens' => 200
                ],
            ]);
            $body = $response->getBody();
            $data = json_decode($body,true);
            \Log::info('API Response: ',$data);
            return trim($data['choices'][0]['message']['content']);
        }catch(\Exception $e){
            \Log::error('API Error: ' . $e->getMessage());
            return 'Error';
        }
    }
}