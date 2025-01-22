<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contact;

class Connection extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden = ["updated_at"];
    public function contacts(){
        return $this->belongsToMany(Contact::class,'contact_connections');
    }
}