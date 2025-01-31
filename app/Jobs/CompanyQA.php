<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use App\Models\CompanyClassification;
use App\Models\QuizResponse;
use App\Jobs\CompanyQA1;
use App\Jobs\CompanyQA2;
use App\Jobs\CompanyQA3;
use App\Jobs\CompanyQA4;
use App\Jobs\CompanyQA5;
use App\Jobs\CompanyQA6;
use App\Jobs\CompanyQA7;
use App\Jobs\CompanyQA8;
use App\Jobs\CompanyQA9;
use App\Jobs\CompanyQA10;
use App\Jobs\CompanyQA11;
use App\Jobs\CompanyQA12;
use App\Jobs\CompanyQA13;
use App\Jobs\CompanyQA14;

class CompanyQA implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;
    public $timeout = 100000;
    private $offset;
    private $limit;
    public function __construct($offset,$limit = 100){
        $this->offset = $offset;
        $this->limit = $limit;
    }
    public function handle(): void{
        $tamClass = CompanyClassification::where('name','TAM')->first();
        $companIds = Company::withTrashed()->whereHas('classifications',function($q) use ($tamClass){
            $q->where('company_classification_id',$tamClass->id);
        })->orderBy('id','asc')->skip($this->offset)->take($this->limit)->get()->pluck('id')->toArray();
        foreach($companIds as $companId){
            $quiz1 = QuizResponse::where('company_id',$companId)->where('question_id',1)->first();
            if(!$quiz1){
                CompanyQA1::dispatch($companId)->onQueue('perplexity');
            }
            $quiz2 = QuizResponse::where('company_id',$companId)->where('question_id',2)->first();
            if(!$quiz2){
                CompanyQA2::dispatch($companId)->onQueue('perplexity');
            }
            $quiz3 = QuizResponse::where('company_id',$companId)->where('question_id',3)->first();
            if(!$quiz3){
                CompanyQA3::dispatch($companId)->onQueue('perplexity');
            }
            $quiz4 = QuizResponse::where('company_id',$companId)->where('question_id',4)->first();
            if(!$quiz4){
                CompanyQA4::dispatch($companId)->onQueue('perplexity');
            }
            $quiz5 = QuizResponse::where('company_id',$companId)->where('question_id',5)->first();
            if(!$quiz5){
                CompanyQA5::dispatch($companId)->onQueue('perplexity');
            }
            $quiz6 = QuizResponse::where('company_id',$companId)->where('question_id',6)->first();
            if(!$quiz6){
                CompanyQA6::dispatch($companId)->onQueue('perplexity');
            }
            $quiz7 = QuizResponse::where('company_id',$companId)->where('question_id',7)->first();
            if(!$quiz7){
                CompanyQA7::dispatch($companId)->onQueue('perplexity');
            }
            $quiz8 = QuizResponse::where('company_id',$companId)->where('question_id',8)->first();
            if(!$quiz8){
                CompanyQA8::dispatch($companId)->onQueue('perplexity');
            }
            $quiz9 = QuizResponse::where('company_id',$companId)->where('question_id',9)->first();
            if(!$quiz9){
                CompanyQA9::dispatch($companId)->onQueue('perplexity');
            }
            $quiz10 = QuizResponse::where('company_id',$companId)->where('question_id',10)->first();
            if(!$quiz10){
                CompanyQA10::dispatch($companId)->onQueue('perplexity');
            }
            $quiz11 = QuizResponse::where('company_id',$companId)->where('question_id',11)->first();
            if(!$quiz11){
                CompanyQA11::dispatch($companId)->onQueue('perplexity');
            }
            $quiz12 = QuizResponse::where('company_id',$companId)->where('question_id',12)->first();
            if(!$quiz12){
                CompanyQA12::dispatch($companId)->onQueue('perplexity');
            }
            $quiz13 = QuizResponse::where('company_id',$companId)->where('question_id',13)->first();
            if(!$quiz13){
                CompanyQA13::dispatch($companId)->onQueue('perplexity');
            }
            $quiz14 = QuizResponse::where('company_id',$companId)->where('question_id',14)->first();
            if(!$quiz14){
                CompanyQA14::dispatch($companId)->onQueue('perplexity');
            }
        }
    }
}