<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use App\Models\QuizQuestion;

class QuizResponse extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden = ["updated_at"];
    protected $appends = ["question_name"];
    public function company(){
        return $this->belongsTo(Company::class);
    }
    public function question(){
        return $this->belongsTo(QuizQuestion::class);
    }
    public function getQuestionNameAttribute(){
        return $this->question ? $this->question->name : null;
    }
}