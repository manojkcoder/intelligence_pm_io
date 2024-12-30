<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use GuzzleHttp\Client;

class DescriptionResearch implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    
    private $client;
    private $companies;
    private $reverse;
    private $id;
    public function __construct($id,$reverse = false){
        $this->id = $id;
        $this->reverse = $reverse;
    }
    public function handle(): void{
        $this->client = new Client();
        $this->companies = Company::withTrashed()->where('id',$this->id)->get()->toArray();
        try{
            $output = $this->fetchCompanyInfo($this->companies);
            $i = 0;
            foreach($this->companies as $company){
                $companyObj = Company::withTrashed()->find($company['id']);
                $companyObj['description'] = trim($output);
                $companyObj['processed'] = true;
                $companyObj->save();
                $i++;
            }
        }catch(\Exception $e){
            \Log::error('Error: ' . $e->getMessage());
        }
        // if(Company::withTrashed()->where('processed', false)->count() > 0){
        //     DescriptionResearch::dispatch($this->reverse);
        // }
    }
    private function fetchCompanyInfo($companies){
        try{
            $response = $this->client->post('https://api.openai.com/v1/chat/completions',[
                'headers' => [
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'user',
                            // 'content' => "You are a research assistant for a consultancy firm. Following is a list of companies (and their country of business) based on their respective domains. Your task is to look up the internet as needed, check the respective imprints of the companies, and return the full company name listed on the imprint page. Provide just the names, each on a new line, without any explanation or numbering. If you can’t find a name, leave the line empty, but the output has to be exact ".count($companies)." lines. The list is as follows:\n\n" . implode("\n", array_map(function($company){return $company['name'].' '.$company['domain'].' ('.$company['country'].')';},$companies)),
                            'content' => "You are a research assistant for a consultancy firm. Following is a company (and their country of business, industry and respective domain). Your task is to look up the internet as needed, check the respective imprints of the companies, and return a brief overview of the company's business activities, keep it under 1000 characters please. The company is as follows:\n\n" . implode("\n", array_map(function($company){return $company['name'].', Website: '.$company['domain'].',  Industry: '.$company['industry'].' ('.$company['country'].')';},$companies)),
                        ],
                    ],
                    'max_tokens' => 200
                ]
            ]);
            $body = $response->getBody();
            $data = json_decode($body,true);
            \Log::info('API Response: ',$data);
            return trim($data['choices'][0]['message']['content']);
        }catch(\Exception $e){
            \Log::error('API Error: ' . $e->getMessage());
            dd($e);
            return 'Error';
        }
    }
}