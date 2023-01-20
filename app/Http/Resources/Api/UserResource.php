<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\District;

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
        if($this->district_id){
            $disctrictDetails =  District::where('id',$this->district_id)->first();
            $districtData = [
                'id' => $disctrictDetails->id,
                'name' => $disctrictDetails->name,
                'province_id' => $disctrictDetails->province_id,
                'provinceName' => $disctrictDetails->province->name,    
            ];
            
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
                'district' => $this->district_id,
                'districtDetails'=> $districtData,
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
                'district' => $this->district_id, 
        ];
    }
}
