<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company;

class CompanyClassification extends Model
{
    use HasFactory;
    protected $fillable = ['name','description','revenue_threshold','revenue_max','employee_threshold','employee_max'];
    protected $casts = [
        'wz_codes' => 'json',
        'negative_wz_codes' => 'json',
        'naics_codes' => 'json'
    ];
    public function companies(){
        return $this->belongsToMany(Company::class,'company_company_classification');
    }
}