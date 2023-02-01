<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\District;
use App\Models\Network;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Php;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $usersDetails = DB::table('users')
        //     ->join('networks', 'users.id', '=', 'parent_user_id')// joining the contacts table , where user_id and contact_user_id are same
        //     ->select('users.*', 'networks.referral_code')
        //     ->latest();

        //     return $usersDetails;
      
        if($this->district_id){
            $disctrictDetails =  District::where('id',$this->district_id)->first();
            $districtData = [
                'id' => $disctrictDetails->id,
                'name' => $disctrictDetails->name,
                'province_id' => $disctrictDetails->province_id,
                'provinceName' => $disctrictDetails->province->name,    
            ];

            // $referralDetails =  Network::latest();
            // $referralData = [
            //     'id' => $referralDetails->id,
            //     'referral_code' => $referralDetails->referral_code,
            //     'user_id' => $referralDetails->user_id,
            //     'parent_user_id' => $referralDetails->parent_user_id,    
            // ];
             return [
                'id' => $this->id,
                'accessToken' =>$this->access_token,
                'name' => $this->name,
                'email' => $this->email ?? '',
                'countryCode' => $this->country_code,
                'phone' => $this->phone,
                'gender' => $this->gender ?? '',
                'address' => $this->address,
                'image' => $this->image,
                'IsVendor' => $this->is_vendor,
                'UserReferralCode'=>$this->referral_code,
                'district' => $this->district_id,
                'districtDetails'=> $districtData,
                // 'referralDetails' =>$referralData,
                
            ];
        }
        return [
            'id' => $this->id,
                'accessToken' =>$this->access_token,
                'name' => $this->name,
                'email' => $this->email ?? '',
                'countryCode' => $this->country_code,
                'phone' => $this->phone,
                'gender' => $this->gender ?? '',
                'address' => $this->address,
                'image' => $this->image,
                'IsVendor' => $this->is_vendor,
                'UserReferralCode'=>$this->referral_code, 
                'district' => $this->district_id,        
        ];
    }
}
