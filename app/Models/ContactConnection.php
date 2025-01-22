<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ContactConnection extends Pivot
{
    protected $table = 'contact_connections';
    protected $fillable = ['contact_id','connection_id'];
}