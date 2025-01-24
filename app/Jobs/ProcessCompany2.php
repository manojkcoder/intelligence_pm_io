<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use GuzzleHttp\Client;

class ProcessCompany2 implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    
    private $client;
    private $companies;
    private $id;
    private $reverse;

    /**
     * Create a new job instance.
     */
    public function __construct($id = null, $reverse = false)
    {
        $this->id = $id;
        $this->reverse = $reverse;
    }
    
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->client = new Client();
        if($this->id){
            $this->companies = [Company::find($this->id)];
        }else{
            $this->companies = Company::where('processed', false)->orderBy('id', $this->reverse ? 'desc' : 'asc')->limit(10)->get();
        }
        foreach ($this->companies as $company) {
            $companyName = $company->name;
            $country = $company->country;
            try{
                if(!$company->revenue){
                    $companyData = $this->fetchCompanyInfo($companyName, $country);
                }
                if(!$company->headcount){
                    $this->fetchCompanyHeadCount($companyName, $country);
                }
                if(!$company->domain){
                    $company->domain = $this->fetchCompanyWebsite($companyName, $country);
                }
                if(!$company->industry && !$company->wz_code){
                    $this->fetchCompanyIndustry($companyName, $country);
                }
            }
            catch(\Exception $e){
                if($this->id){
                    $company->revenue = null;
                    $company->processed = true;
                    $company->save();
                    throw $e;
                }
                else{
                    \Log::error('Error processing company: ' . $companyName);
                    ProcessCompany::dispatch($company->id);
                    continue;
                }
            }
                
        }
        if($this->id == null && Company::where('processed', false)->count() > 0){
            ProcessCompany2::dispatch(null, $this->reverse);
        }
    }
    private function fetchCompanyInfo($companyName,$country){
        try {
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
                            'content' => "As a research assistant for a consultancy company, you are tasked to research the following details for a company named $companyName based in $country: \n\n

	1.	Yearly revenue.

Utilize the company’s annual reports or other reliable internet sources, or else make an educated guess. Please respond with each detail on a separate line, providing only the numeric value without any additional text, explanations, or labels."
                        ],
                    ],
                    'max_tokens' => 200,
                ],
            ]);
            $body = $response->getBody();
            $data = json_decode($body, true);
            \Log::info('API Response: ', $data);
            // Assuming you have a Company model and the company record is already fetched
            $company = Company::where('name', $companyName)->first();
            if($company){
                $revenue = trim($data['choices'][0]['message']['content']);
                $revenue = (float) $revenue;
                $revenue = floatval($revenue / 1000000);
                $company->revenue = $revenue;
                $company->save();
            }
        }catch(\Exception $e){
            \Log::error('API Error: ' . $e->getMessage());
            return 'Error';
        }
    }
    private function fetchCompanyWebsite($companyName, $country){
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
                            'content' => "As a research assistant for a consultancy company, you are tasked to research the website address for a company named $companyName based in $country. Please respond with only the website domain (ex. abc.com) without any additional text, explanations, or labels."
                        ],
                    ],
                    'max_tokens' => 200,
                ],
            ]);
            
            $body = $response->getBody();
            $data = json_decode($body, true);

            \Log::info('API Response: ', $data);

            $website = trim($data['choices'][0]['message']['content']);

            // Assuming you have a Company model and the company record is already fetched
            $company = Company::where('name', $companyName)->first();
            if ($company) {
                $company->domain = $website;
                $company->save();
            }

            return $website;
        } catch (\Exception $e) {
            \Log::error('API Error: ' . $e->getMessage());
            return 'Error';
        }
    }
    public function fetchCompanyHeadCount($companyName, $country){
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
                            'content' => "As a research assistant for a consultancy company, you are tasked to research the headcount for a company named $companyName based in $country. Please respond with only the numeric value without any additional text, explanations, or labels."
                        ],
                    ],
                    'max_tokens' => 200,
                ],
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);

            \Log::info('API Response: ', $data);
            

            // Assuming you have a Company model and the company record is already fetched
            $company = Company::where('name', $companyName)->first();
            if ($company) {
                $company->headcount = trim($data['choices'][0]['message']['content']);
                $company->save();
            }


        } catch (\Exception $e) {
            \Log::error('API Error: ' . $e->getMessage());
            return 'Error';
        }
    }
    public function fetchCompanyIndustry($companyName,$country){
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
                            'content' => "As a research assistant for a consultancy company, you are tasked to research the industry for a company named $companyName based in $country. Please respond with only the industry name without any additional text, explanations, or labels."
                        ],
                    ],
                    'max_tokens' => 200,
                ],
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);

            \Log::info('API Response: ', $data);

            // Assuming you have a Company model and the company record is already fetched
            $company = Company::where('name', $companyName)->first();
            if ($company) {
                $company->industry = trim($data['choices'][0]['message']['content']);
                $company->save();
            }

        } catch (\Exception $e) {
            \Log::error('API Error: ' . $e->getMessage());
            return 'Error';
        }
        
    }
}
