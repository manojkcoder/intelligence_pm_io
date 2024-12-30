<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use GuzzleHttp\Client;

class WZCodeResearch implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    
    private $client;
    private $companies;
    private $reverse;

    /**
     * Create a new job instance.
     */
    public function __construct($reverse = false)
    {
        $this->reverse = $reverse;
    }
    
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->client = new Client();
        // get all companies that have not been processed yet, with wz_code length of 5 or less
        $this->companies = Company::where('processed', false)->orderBy('id', $this->reverse ? 'desc' : 'asc')->limit(10)->get()->toArray();
        try {
            $output = $this->fetchCompanyInfo($this->companies);
            $lines = explode("\n", $output);
            if(count($lines) != 10){
                throw new \Exception('Invalid data: ' . $output);
            }
            $i = 0;
            foreach ($this->companies as $company) {
                // $parts = explode('.', $lines[$i]);
                // if(count($parts) < 2){
                //     continue;
                // }
                $company = Company::find($company['id']);
                $company['wz_code'] = trim($lines[$i]);
                // keep only digits and dots
                $company['wz_code'] = preg_replace('/[^0-9.]/', '', $company['wz_code']);
                $company['processed'] = true;
                $company->save();
                $i++;
            }
        } catch (\Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
        }
        if(Company::where('processed', false)->count() > 0){
            WZCodeResearch::dispatch($this->reverse);
        }
    }



    private function fetchCompanyInfo($companies)
    {
        try {
            $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o',
                    'messages' => [
                        [
                            'role' => 'user',
                            // 'content' => "You are a research assistant for a consultancy firm. Following is a list of companies (and their country of business) based on their respective domains. Your task is to look up the internet as needed, check the respective imprints of the companies, and return the full company name listed on the imprint page. Provide just the names, each on a new line, without any explanation or numbering. If you can’t find a name, leave the line empty, but the output has to be exact ".count($companies)." lines. The list is as follows:\n\n" . implode("\n", array_map(function($company){return $company['name'].' '.$company['domain'].' ('.$company['country'].')';},$companies)),
                            'content' => "You are a research assistant for a consultancy firm. Following is a list of companies (and their country of business, industry and respective domain). Your task is to look up the internet as needed, check the respective imprints of the companies, and return the most applicable digit wz code (excluding the periods). Return most specific WZ including 5 digits, each on a new line, without any explanation or any other stuff. If you can’t find the code, leave the line empty, but the output has to be exact ".count($companies)." lines. The list is as follows:\n\n" . implode("\n", array_map(function($company){return $company['name'].', Website: '.$company['domain'].',  Industry: '.$company['industry'].' ('.$company['country'].')';},$companies)),
                        ],
                    ],
                    'max_tokens' => 200,
                ],
            ]);
            
            $body = $response->getBody();
            $data = json_decode($body, true);

            \Log::info('API Response: ', $data);

            return trim($data['choices'][0]['message']['content']);
        } catch (\Exception $e) {
            \Log::error('API Error: ' . $e->getMessage());
            dd($e);
            return 'Error';
        }
    }

}
