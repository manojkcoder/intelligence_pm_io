<?php
namespace App\Jobs;
use App\Models\CompanyClassification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Models\Company;
use App\Models\Contact;

class ImportCognismContacts implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;
    public $timeout = 1000000;
    protected $companyId;
    protected $contacts;
    public function __construct($companyId,$results){
        $this->companyId = $companyId;
        $this->contacts = json_decode($results,true);
    }
    public function handle(){
        ini_set('memory_limit','3G');
        \Log::info('Importing ' . count($this->contacts) . ' contacts For Company # ' . $this->companyId);
        $companyId = $this->getCompanyIdByCognismId($this->companyId);
        foreach($this->contacts as $contactData){
            try{
                if(isset($contactData) && !empty($contactData)){
                    $linkedin = $this->getLinkedInProfileUrl($contactData);
                    $first = (isset($contactData['first']) && !empty($contactData['first'])) ? urldecode($contactData['first']) : null;
                    $last = (isset($contactData['last']) && !empty($contactData['last'])) ? urldecode($contactData['last']) : null;
                    if($linkedin){
                        $contact = Contact::where('linkedin',$linkedin)->first();
                        if(!$contact){
                            $contact = new Contact();
                        }
                    }else if($first && $last){
                        $contact = Contact::where('first_name',$first)->where('last_name',$last)->first();
                        if(!$contact){
                            $contact = new Contact();
                        }
                    }else{
                        $contact = new Contact();
                    }
                    $contact->cognism_id = $contactData["id"];
                    $contact->company_id = $companyId;
                    $contact->first_name = $first;
                    $contact->last_name = $last;
                    if(isset($contactData['image']) && !empty($contactData['image'])){
                        $contact->profile_image = $contactData['image'];
                    }
                    if((isset($contactData['loc']) && isset($contactData['loc']['state']) && !empty($contactData['loc']['state']))){
                        $contact->location = $contactData['loc']['state'];
                    }
                    if((isset($contactData['loc']) && isset($contactData['loc']['country']) && !empty($contactData['loc']['country']))){
                        $contact->country = $contactData['loc']['country'];
                    }
                    if(!empty($linkedin)){
                        $contact->linkedin = $linkedin;
                    }
                    $contact->cognism_data = json_encode($contactData);
                    $contact->save();
                }
            }catch(\Exception $e){
                \Log::error($e->getMessage());
                continue;
            }
        }
    }
    private function getLinkedInProfileUrl($contactData){
        $linkedinUrl = null;
        if(isset($contactData['data']) && !empty($contactData['data'])){
            foreach($contactData['data'] as $item){
                if(isset($item['linkedin']) && !empty($item['linkedin'])){
                    $linkedinUrl = str_replace('https://www.linkedin.com','https://linkedin.com',$item['linkedin']);
                    if(strpos($linkedinUrl,'?') !== false){
                        $linkedinUrl = explode('?',$linkedinUrl)[0];
                    }
                    break;
                }
            }
        }
        return $linkedinUrl;
    }
    private function getCompanyIdByCognismId($data){
        $companyId = null;
        $company = Company::withTrashed()->where('cognism_id',$this->companyId)->first();
        if($company){
            $companyId = $company->id;
        }
        return $companyId;
    }
}