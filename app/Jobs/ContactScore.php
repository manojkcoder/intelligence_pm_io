<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Contact;
use App\Models\Setting;

class ContactScore implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    private $activityData;
    public function __construct($id){
        $this->id = $id;
    }
    public function handle(): void{
        try{
            $contact = Contact::find($this->id);
            
        }catch(\Exception $e){
            \Log::error("Error: " . $e->getMessage());
        }
    }
}