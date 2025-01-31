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

class CompanyParentHeadquarter implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;
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
            $QuizResponse = QuizResponse::where('company_id',$company->id)->where('question_id',17)->first();
            if(!$QuizResponse){
                $QuizResponse = new QuizResponse();
                $QuizResponse->company_id = $company->id;
                $QuizResponse->question_id = 17;
            }
            if(str_contains($output,':**')){
                $output = explode(':**',$output)[1];
                if(str_contains($output,'**')){
                    $output = explode('**',$output)[0];
                }
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'**')){
                $output = explode('**',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'##')){
                $output = explode('##',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'is headquartered in')){
                $output = explode('is headquartered in',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'is located in')){
                $output = explode('is located in',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'is based in')){
                $output = explode('is based in',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'headquarters located in')){
                $output = explode('headquarters located in',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'headquarters located at')){
                $output = explode('headquarters located at',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'headquarters is in')){
                $output = explode('headquarters is in',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'is headquartered at')){
                $output = explode('is headquartered at',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'is located at')){
                $output = explode('is located at',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'are located at')){
                $output = explode('are located at',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'is based at')){
                $output = explode('is based at',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'eadquarters at')){
                $output = explode('eadquarters at',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'eadquarters in')){
                $output = explode('headquarters in',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'headquarters are located in')){
                $output = explode('headquarters are located in',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'headquartered in')){
                $output = explode('headquartered in',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'headquartered at')){
                $output = explode('headquartered at',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'is headquartered near')){
                $output = explode('is headquartered near',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }else if(str_contains($output,'headquarters based in')){
                $output = explode('headquarters based in',$output)[1];
                if(str_contains($output,'.')){
                    $output = explode('.',$output)[0];
                }
            }
            $output = str_replace(['[1]','[2]','[3]','[4]','[5]','[6]','[7]','[8]','[9]','[10]','[11]','[12]','[13]','[14]','[15]','[16]','[17]','[18]','[19]','[20]'],'',$output);
            if(str_contains($output,'The query does not provide any information about the location') || str_contains($output,'The information provided does not specify the location')){
                $output = "-";
            }else if(str_contains($output,', but the specific')){
                $output = explode(', but the specific',$output)[0];
            }
            $QuizResponse->answer = trim($output);
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
                            "content" => "Provide only the location of the parent company's headquarters based on '".$company."'. Do not provide any extra info."
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