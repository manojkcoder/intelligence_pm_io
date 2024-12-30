<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use GuzzleHttp\Client;

class NewWZCodeResearch implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    private $client;
    private $companies;
    private $id;
    public function __construct($id){
        $this->id = $id;
    }
    public function handle(): void{
        $this->client = new Client();
        $this->companies = Company::withTrashed()->where('id',$this->id)->get()->toArray();
        try{
            $output = $this->fetchCompanyInfo($this->companies);
            foreach($this->companies as $company){
                $wz_code = preg_replace('/[^0-9]/','',$output);
                $companyObj = Company::withTrashed()->find($company['id']);
                $companyObj['new_wz_code'] = $wz_code ?? null;
                $companyObj->save();
            }
        }catch(\Exception $e){
            \Log::error('Error: ' . $e->getMessage());
        }
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
                            'content' => "You are a research assistant for a consultancy firm. Following is a list of companies (and their country of business, industry and respective domain). Your task is to look up the internet as needed, check the respective imprints of the companies, and return the most applicable digit wz code (excluding the periods). Return most specific WZ including 4 digits, each on a new line, without any explanation or any other stuff. If you can’t find the code, leave the line empty, but the output has to be exact ".count($companies)." lines. The list is as follows:\n\n" . implode("\n", array_map(function($company){return $company['name'].', Website: '.$company['domain'].',  Industry: '.$company['industry'].' ('.$company['country'].')';},$companies)),
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