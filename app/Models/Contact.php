<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Activity;
use App\Models\Company;
use App\Models\Connection;
use App\Models\ContactJob;
use App\Models\ContactLicence;
use App\Models\ContactSchool;
use App\Models\LikeComment;

class Contact extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function company(){
        return $this->belongsTo(Company::class);
    }
    public function likeComments(){
        return $this->hasMany(LikeComment::class);
    }
    public function activities(){
        return $this->hasMany(Activity::class);
    }
    public function jobs(){
        return $this->hasMany(ContactJob::class);
    }
    public function licences(){
        return $this->hasMany(ContactLicence::class);
    }
    public function schools(){
        return $this->hasMany(ContactSchool::class);
    }
    public function connections(){
        return $this->belongsToMany(Connection::class,'contact_connections');
    }
}