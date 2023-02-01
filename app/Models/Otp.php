<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Twilio\rest\Client;

class Otp extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'otp',
        'expire_at',
    ];
    #Sending Sms to the number
    public function sendSMS($receiverNumber,$country_code){
        $msg = 'Your Otp is : '.$this->otp;
        try{
            $account_id = env("TWILIO_SID");
            $auth_token = env("TWILIO_AUTH_TOKEN");
            $twilio_number = env("TWILIO_NUMBER");

            $client= new Client($account_id, $auth_token);
            $client->messages->create('+'.$country_code.$receiverNumber,[
                'from'=>  $twilio_number,
                'body' => $msg,
            ]);
            info('SMS Send Successfully!');
        }catch(\Exception $e){
            info("Error. ".$e->getMessage());
        }

        
    }
}
