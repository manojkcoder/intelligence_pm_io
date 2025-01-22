<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use App\Models\QuizQuestion;
use App\Models\QuizResponse;

class CompanyQuiz implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    private $id;
    public function __construct($id){
        $this->id = $id;
    }
    public function handle(): void{
        $company = Company::withTrashed()->where('id',$this->id)->first();
        if($company){
            $qaResponses = $company->qa_responses ? json_decode($company->qa_responses,true) : [];
            foreach($qaResponses as $qaResponse){
                if(str_contains($qaResponse['question'],'large corporation')){
                    $answer = str_contains($qaResponse['answer'],'TAM') ? 'yes' : 'no';
                    $this->AddQuizResponse($company->id,1,null,$answer);
                }else if(str_contains($qaResponse['question'],'company independent') || str_contains($qaResponse['question'],'large group as a division')){
                    $answer = str_contains($qaResponse['answer'],'SAM') ? 'yes' : 'no';
                    $this->AddQuizResponse($company->id,2,null,$answer);
                }else if(str_contains($qaResponse['question'],'located in Germany') || str_contains($qaResponse['question'],'headquarters not located in German')){
                    if(str_contains($qaResponse['answer'],'SAM')){
                        $answer = 'yes';
                    }else if(str_contains($qaResponse['answer'],'TAM')){
                        $answer = 'no';
                    }else{
                        $answer = $qaResponse['answer'];
                    }
                    $this->AddQuizResponse($company->id,3,null,$answer);
                }else if(str_contains($qaResponse['question'],'state-owned company')){
                    $answer = str_contains($qaResponse['answer'],'TAM') ? 'yes' : 'no';
                    $this->AddQuizResponse($company->id,4,null,$answer);
                }else if(str_contains($qaResponse['question'],'there any information on innovations made')){
                    $this->AddQuizResponse($company->id,5,null,$qaResponse['answer']);
                }else if(str_contains($qaResponse['question'],'long has the CEO & CFO been')){
                    $this->AddQuizResponse($company->id,6,null,$qaResponse['answer']);
                }else if(str_contains($qaResponse['question'],'What did the CEO / CFO do before')){
                    $this->AddQuizResponse($company->id,7,null,$qaResponse['answer']);
                }else if(str_contains($qaResponse['question'],'which channels are the CEO')){
                    $this->AddQuizResponse($company->id,8,null,$qaResponse['answer']);
                }else if(str_contains($qaResponse['question'],'online channels')){
                    $this->AddQuizResponse($company->id,9,null,$qaResponse['answer']);
                }else if(str_contains($qaResponse['question'],'part of a podcast')){
                    $this->AddQuizResponse($company->id,10,null,$qaResponse['answer']);
                }else if(str_contains($qaResponse['question'],'signs that the CEO or CFO')){
                    $this->AddQuizResponse($company->id,11,null,$qaResponse['answer']);
                }else if(str_contains($qaResponse['question'],'associated with innovation')){
                    $this->AddQuizResponse($company->id,12,null,$qaResponse['answer']);
                }else if(str_contains($qaResponse['question'],'strategy of the company publicly available')){
                    $this->AddQuizResponse($company->id,13,null,$qaResponse['answer']);
                }else if(str_contains($qaResponse['question'],'showcase with consultants')){
                    $this->AddQuizResponse($company->id,14,null,$qaResponse['answer']);
                }else if(str_contains($qaResponse['question'],'company belong to another parent company')){
                    $answer = str_contains(strtolower($qaResponse['answer']),'yes') ? 'yes' : 'no';
                    $this->AddQuizResponse($company->id,15,null,$qaResponse['answer']);
                }else if(str_contains($qaResponse['question'],'Name of the parent company')){
                    $this->AddQuizResponse($company->id,16,null,$qaResponse['answer']);
                }else if(str_contains($qaResponse['question'],'Location of Headquarter of parent company')){
                    $this->AddQuizResponse($company->id,17,null,$qaResponse['answer']);
                }else{
                    $this->AddQuizResponse($company->id,null,$qaResponse['question'],$qaResponse['answer']);
                }
            }
        }
    }
    public function AddQuizResponse($companyId = null,$questionId = null,$question = null,$answer = null){
        if($companyId && $questionId){
            $quizResponse = QuizResponse::where('company_id',$companyId)->where('question_id',$questionId)->first();
            if(!$quizResponse){
                $quizResponse = new QuizResponse();
                $quizResponse->company_id = $companyId;
                $quizResponse->question_id = $questionId;
                $quizResponse->question_name = $question;
            }
        }else{
            $quizResponse = new QuizResponse();
            $quizResponse->company_id = $companyId;
            $quizResponse->question_id = $questionId;
            $quizResponse->question_name = $question;
        }
        if($answer){
            if(is_array($answer)){
                $answer = json_encode($answer);
            }
            $answer = str_replace(['[1]','[2]','[3]','[4]','[5]','[6]','[7]','[8]','[9]'],'',$answer);
        }
        $quizResponse->answer = $answer;
        $quizResponse->save();
    }
}