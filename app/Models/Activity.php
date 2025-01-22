<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contact;

class Activity extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden = ["updated_at"];
    public function contact(){
        return $this->belongsTo(Contact::class);
    }
}