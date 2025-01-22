<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Connection;

class ContactConnection implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    private $id;
    private $connectionData;
    public function __construct($id,$data){
        $this->id = $id;
        $this->connectionData = $data;
    }
    public function handle(): void{
        try{
            foreach($this->connectionData as $connectionData){
                if(isset($connectionData['linkedin']) && !empty($connectionData['linkedin']) && $connectionData['linkedin']){
                    $linkedinUrl = str_replace('https://www.linkedin.com','https://linkedin.com',urldecode(str_replace("\\/","/",$connectionData['linkedin'])));
                    if(strpos($linkedinUrl,'?') !== false){
                        $linkedinUrl = explode('?',$linkedinUrl)[0];
                    }
                    $connection = Connection::where('linkedin',$linkedinUrl)->first();
                    if(!$connection){
                        $connection = new Connection();
                    }
                    $connection->linkedin = $linkedinUrl ?? null;
                    $connection->name = $connectionData['name'] ?? null;
                    $connection->location = $connectionData['location'] ?? null;
                    $connection->position = $connectionData['position'] ?? null;
                    $connection->image = $connectionData['image'] ? urldecode(str_replace("\\/","/",$connectionData['image'])) : "";
                    $connection->response = json_encode($connectionData);
                    $connection->save();
                    $connection->contacts()->attach($this->id,['connection_id' => $connection->id,'contact_id' => $this->id]);
                }
            }
        }catch(\Exception $e){
            \Log::error("Error: " . $e->getMessage());
        }
    }
}