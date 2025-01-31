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

class CompanyQAs implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    private $client;
    private $companyId;
    public function __construct($companyId){
        $this->companyId = $companyId;
    }
    public function handle(): void{
        $this->client = new Client();
        $company = Company::withTrashed()->where('id',$this->companyId)->first();
        if($company){
            $quiz1 = QuizResponse::where('company_id',$this->companyId)->where('question_id',1)->first();
            if(!$quiz1){
                $output = $this->fetchQA1($company->name);
                $output = str_contains(strtolower($output),'yes') ? 'yes' : 'no';
                $company->quiz()->create(['question_id' => 1,'answer' => trim($output)]);
            }
            $quiz2 = QuizResponse::where('company_id',$this->companyId)->where('question_id',2)->first();
            if(!$quiz2){
                $output = $this->fetchQA2($company->name);
                $output = str_contains(strtolower($output),'yes') ? 'yes' : 'no';
                $company->quiz()->create(['question_id' => 2,'answer' => trim($output)]);
            }
            $quiz3 = QuizResponse::where('company_id',$this->companyId)->where('question_id',3)->first();
            if(!$quiz3){
                $output = $this->fetchQA3($company->name);
                $output = str_contains(strtolower($output),'yes') ? 'yes' : 'no';
                $company->quiz()->create(['question_id' => 3,'answer' => trim($output)]);
            }
            $quiz4 = QuizResponse::where('company_id',$this->companyId)->where('question_id',4)->first();
            if(!$quiz4){
                $output = $this->fetchQA4($company->name);
                $output = str_contains(strtolower($output),'yes') ? 'yes' : 'no';
                $company->quiz()->create(['question_id' => 4,'answer' => trim($output)]);
            }
            $quiz5 = QuizResponse::where('company_id',$this->companyId)->where('question_id',5)->first();
            if(!$quiz5){
                $output = $this->fetchQA5($company->name);
                $company->quiz()->create(['question_id' => 5,'answer' => trim($output)]);
            }
            $quiz6 = QuizResponse::where('company_id',$this->companyId)->where('question_id',6)->first();
            if(!$quiz6){
                $output = $this->fetchQA6($company->name);
                $company->quiz()->create(['question_id' => 6,'answer' => trim($output)]);
            }
            $quiz7 = QuizResponse::where('company_id',$this->companyId)->where('question_id',7)->first();
            if(!$quiz7){
                $output = $this->fetchQA7($company->name);
                $company->quiz()->create(['question_id' => 7,'answer' => trim($output)]);
            }
            $quiz8 = QuizResponse::where('company_id',$this->companyId)->where('question_id',8)->first();
            if(!$quiz8){
                $output = $this->fetchQA8($company->name);
                $company->quiz()->create(['question_id' => 8,'answer' => trim($output)]);
            }
            $quiz9 = QuizResponse::where('company_id',$this->companyId)->where('question_id',9)->first();
            if(!$quiz9){
                $output = $this->fetchQA9($company->name);
                $company->quiz()->create(['question_id' => 9,'answer' => trim($output)]);
            }
            $quiz10 = QuizResponse::where('company_id',$this->companyId)->where('question_id',10)->first();
            if(!$quiz10){
                $output = $this->fetchQA10($company->name);
                $company->quiz()->create(['question_id' => 10,'answer' => trim($output)]);
            }
            $quiz11 = QuizResponse::where('company_id',$this->companyId)->where('question_id',11)->first();
            if(!$quiz11){
                $output = $this->fetchQA11($company->name);
                $company->quiz()->create(['question_id' => 11,'answer' => trim($output)]);
            }
            $quiz12 = QuizResponse::where('company_id',$this->companyId)->where('question_id',12)->first();
            if(!$quiz12){
                $output = $this->fetchQA12($company->name);
                $company->quiz()->create(['question_id' => 12,'answer' => trim($output)]);
            }
            $quiz13 = QuizResponse::where('company_id',$this->companyId)->where('question_id',13)->first();
            if(!$quiz13){
                $output = $this->fetchQA13($company->name);
                $company->quiz()->create(['question_id' => 13,'answer' => trim($output)]);
            }
            $quiz14 = QuizResponse::where('company_id',$this->companyId)->where('question_id',14)->first();
            if(!$quiz14){
                $output = $this->fetchQA14($company->name);
                $company->quiz()->create(['question_id' => 14,'answer' => trim($output)]);
            }
        }
    }
    private function fetchQA1($company){
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
                            "content" => "Based on the company name '".$company."', is it a division of a large corporation? Please respond with 'yes' or 'no'. Do not provide any extra info."
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
    private function fetchQA2($company){
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
                            "content" => "Based on the company name '".$company."', Is the company independent (not part of a large group as a division?) Please respond with 'yes' or 'no'. Do not provide any extra info."
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
    private function fetchQA3($company){
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
                            "content" => "Based on the company name '".$company."', is the headquarters located outside of Germany? Please respond with 'yes' or 'no'. Do not provide any extra info."
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
    private function fetchQA4($company){
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
                            "content" => "Based on the company name '".$company."', is it a state-owned company? Please respond with 'yes' or 'no'. Do not provide any extra info."
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
    private function fetchQA5($company){
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
                            "content" => "Based on the company name '".$company."', is there any available information on innovations such as R&D costs, new warehouses, production facilities, or acquisitions? If yes, please provide a brief summary."
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
    private function fetchQA6($company){
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
                            "content" => "Based on the company name '".$company."', how long have the CEO and CFO been in their positions since they started?"
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
    private function fetchQA7($company){
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
                            "content" => "Based on the company name '".$company."', What did the CEO / CFO do before?"
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
    private function fetchQA8($company){
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
                            "content" => "Based on the company name '".$company."', in which online channels are the CEO and CFO active (e.g., LinkedIn, Facebook, Instagram, charity work like 'dogs in need' or not at all)?"
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
    private function fetchQA9($company){
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
                            "content" => "Based on the company name '".$company."', which types of posts do the CEO and CFO engage with on online channels?"
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
    private function fetchQA10($company){
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
                            "content" => "Based on the company name '".$company."', have the CEO or CFO participated in a podcast, written a book, or been featured in similar media?"
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
    private function fetchQA11($company){
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
                            "content" => "Based on the company name '".$company."', are there signs that the CEO or CFO are focused on improving the implementation of any of the following topics?\n- Transformation\n- Strategy\n- Performance programme"
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
    private function fetchQA12($company){
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
                            "content" => "Based on the company name '".$company."', are the CEO and CFO associated with innovation? If yes, please provide a detailed paragraph."
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
    private function fetchQA13($company){
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
                            "content" => "Based on the company name '".$company."', is there a publicly available known strategy for the company? If yes, please provide a detailed paragraph."
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
    private function fetchQA14($company){
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
                            "content" => "Based on the company name '".$company."', is there a showcase or partnership with consultants for the company? If yes, please provide a detailed paragraph."
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