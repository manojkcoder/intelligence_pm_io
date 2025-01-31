<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\QuizResponse;

class CompanyQuiz implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;
    public $timeout = 100000;
    public function __construct(){
    }
    public function handle(): void{
        $questions = [2,3,4,5,6,7,8,9,10,11,12,13,14];
        $companIds = QuizResponse::where('question_id',1)->orderBy('id','asc')->get(['company_id'])->pluck('company_id')->toArray();
        foreach($companIds as $companId){
            foreach($questions as $question){
                $quiz = QuizResponse::where('company_id',$companId)->where('question_id',$question)->first();
                if(!$quiz){
                    $res = new QuizResponse();
                    $res->company_id = $companId;
                    $res->question_id = $question;
                    $res->save();
                }
            }
        }
    }
}