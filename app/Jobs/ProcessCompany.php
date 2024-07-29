<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use GuzzleHttp\Client;

class ProcessCompany implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    
    private $client;
    private $companies;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }
    
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->client = new Client();
        $this->companies = Company::where('processed', false)->take(10)->get();
        foreach ($this->companies as $company) {
            $companyName = $company->name;
            try{
                $companyData = $this->fetchCompanyInfo($companyName);
                
                $lines = explode("\n", $companyData);
                $revenue = $this->extractNumeric($lines[0]);
                $wzCode = $this->extractNumeric($lines[1]);
                $employees = $this->extractNumeric($lines[2]);
                
                $company->revenue = $revenue;
                $company->wz_code = $wzCode;
                $company->headcount = $employees;
            }
            catch(\Exception $e){
                \Log::error('Error processing company: ' . $companyName);
            }
            $company->processed = true;
            $company->save();
                
        }
        if(Company::where('processed', false)->count() > 0){
            ProcessCompany::dispatch();
        }
    }



    private function fetchCompanyInfo($companyName)
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
                            'content' => "For the company named \"$companyName\", provide the following details:\n1) Yearly revenue in millions of euros.\n2) 5-digit WZ Code.\n3) Number of employees.\n\nPlease respond with each detail on a separate line, and provide only the numeric values. Do not include any additional text, explanations, or labels."
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
            return 'Error';
        }
    }

    private function extractNumeric($text)
    {
        // Use regex to extract numeric value, handling various formats
        preg_match('/[\d.,]+/', $text, $matches);
        return isset($matches[0]) ? str_replace(',', '', $matches[0]) : null;
    }

}
