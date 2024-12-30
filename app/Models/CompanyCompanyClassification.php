<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanyCompanyClassification extends Pivot
{
    protected $table = 'company_company_classification';
    protected $fillable = ['company_id','company_classification_id'];
}