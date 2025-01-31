<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contact;
use App\Models\CompanyClassification;
use App\Models\QuizResponse;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['name','country','processed','legal_name','domain','industry','headcount','revenue'];
    public function contacts(){
        return $this->hasMany(Contact::class);
    }
    public function getAttributesCount(){
        return collect($this->getAttributes())->filter(function($value){
            return !is_null($value);
        })->count();
    }
    public function classifications(){
        return $this->belongsToMany(CompanyClassification::class,'company_company_classification');
    }
    public function getCompanyClassificationsAttribute(){
        $classifications = $this->classifications->pluck('name')->toArray();
        if($this->custom_classification && !in_array($this->custom_classification,$classifications)){
            $classifications[] = $this->custom_classification;
        }
        return $classifications;
    }
    public function quiz(){
        return $this->hasMany(QuizResponse::class,'company_id')->orderBy('question_id','asc');
    }
}