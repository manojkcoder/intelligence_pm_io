<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikeComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'profile_link',
        'first_name',
        'last_name',
        'post_url',
        'comment',
        'is_comment',
        'is_like',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
