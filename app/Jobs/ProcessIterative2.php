<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
// storage
use Illuminate\Support\Facades\Storage;
use App\Models\Company;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;


class ProcessIterative2 implements ShouldQueue
{
    use Queueable;

    private $client;
    private $id;
    private $type;

    /**
     * Create a new job instance.
     */
    public function __construct($id, $type)
    {
        $this->id = $id;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->client = new Client();
        // DB::table('naics_industries')->whereNull('cost_pressure')->get()->each(function ($industry) {
            $industry = DB::table('naics_industries')->where('id', $this->id)->first();
            if(empty($industry->title)){
                return;
            }
            $score = $this->getScore($industry->title);
            // check if exactly 2 lines, and are numbers
            if (count(explode("\n", $score)) == 2) {
                $lines = explode("\n", $score);
                $score = str_replace('Score: ', '', $lines[0]);
                $confidence = str_replace('Confidence: ', '', $lines[1]);
                if (is_numeric($score) && is_numeric($confidence)) {
                    \Log::info([
                        $this->type => $score,
                        $this->type.'_weight' => $confidence,
                    ]);
                    DB::table('naics_industries')->where('id', $this->id)->update([
                        $this->type => $score,
                        $this->type.'_weight' => $confidence,
                    ]);
                }else{
                    throw new \Exception('Invalid data: ' . $score);
                }
            }
        // });
    }



    private function getScore($industry)
    {

        $base_prompts = json_decode(Storage::disk('local')->get('base_prompts.json'), true);

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
                            'content' => 
                                $base_prompts[$this->type] .
                                "Let's start by evaluating the industry: \"".$industry."\", as an output return only 2 lines containing the score on first and the confidence on second, on a scale of 1-10, about the score you came up with.
                                Response Example:
                                Score: 4
                                Confidence: 8",
                        ],
                    ],
                    'max_tokens' => 200,
                ],
            ]);
            
            $body = $response->getBody();
            $data = json_decode($body, true);

            // \Log::info('API Response: ', $data);

            $content = trim($data['choices'][0]['message']['content']);
            \Log::info('API Response: ' . $content);
            return $content;
        } catch (\Exception $e) {
            \Log::error('API Error: ' . $e->getMessage());
            dd($e);
            return 'Error';
        }
    }
}
