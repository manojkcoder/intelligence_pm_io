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
    private $id;

    /**
     * Create a new job instance.
     */
    public function __construct($id = null)
    {
        $this->id = $id;
    }
    
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->client = new Client();
        if($this->id){
            $this->companies = [Company::find($this->id)];
        }
        else{
            $this->companies = Company::where('processed', false)->take(10)->get();
        }
        foreach ($this->companies as $company) {
            $companyName = $company->name;
            $country = $company->country;
            try{
                $companyData = $this->fetchCompanyInfo($companyName, $country);
                
                $lines = explode("\n", $companyData);
                if(count($lines) < 3){
                    throw new \Exception('Invalid data: ' . $companyData);
                }
                $revenue = trim(str_replace(['1)', ','], '', $lines[0]));
                $industry = trim(str_replace(['2)', ','], '', $lines[1]));
                $employees = trim(str_replace(['3)', ','], '', $lines[2]));
                
                // if either of the values is not numeric and $this->id is set, throw an exception
                if(!is_numeric($revenue) || empty($industry) || !is_numeric($employees)){
                    throw new \Exception('Invalid data: ' . $companyData);
                }
                
                $company->revenue = $revenue;
                $company->industry = $industry;
                $company->headcount = $employees;

                $company->processed = true;
                $company->save();
            }
            catch(\Exception $e){
                if($this->id){
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
            ProcessCompany::dispatch();
        }
    }



    private function fetchCompanyInfo($companyName, $country)
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
                            'content' => "As a research assistant for a consultancy company, you are tasked to research the following details for a company named $companyName based in $country: \n\n

	1.	Yearly revenue (in millions).
    2.  Industry the company operates in.
	3.	Number of employees.

Utilize the company’s annual reports or other reliable internet sources, use the browser tool, to find the data. Please respond with each detail on a separate line, providing only the numeric values without any additional text, explanations, or labels."
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
