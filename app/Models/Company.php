<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contact;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'country', 'processed', 'legal_name', 'domain', 'industry', 'headcount', 'revenue'];

    // protected $appends = ['company_classifications'];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
    
    public function getAttributesCount()
    {
        // Count non-null attributes
        return collect($this->getAttributes())
            ->filter(function ($value) {
                return !is_null($value);
            })->count();
    }

    public function classifications()
    {
        return $this->belongsToMany(CompanyClassification::class, 'company_company_classification');
    }

    public function getCompanyClassificationsAttribute()
    {
        if($this->custom_classification){
            return array_push($this->classifications->pluck('name')->toArray(), $this->custom_classification);
        }
        return $this->classifications->pluck('name')->toArray();
    }
}


