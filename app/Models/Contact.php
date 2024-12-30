<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'linkedin',
        'url',
        'company_id',
        'notes',
        'extra',
        'email_domain',
        'approach',
        'target_category',
        'linkedin_hub_url',
        'country'
    ];
    public function company(){
        return $this->belongsTo(Company::class);
    }
    public function likeComments(){
        return $this->hasMany(LikeComment::class);
    }
}