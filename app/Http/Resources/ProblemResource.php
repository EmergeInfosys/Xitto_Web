<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProblemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' =>$this->id,
            'service_id' =>$this->service_id,
            'serviceName' =>$this->service->title,
            'title' => $this->title,
            'description'=>$this->description,
            'image'=>$this->image,
            'time'=>$this->time,
            'price'=>$this->price,
            'service_charge' => $this->service_charge,
            'quantity'=>$this->quantity,
        ];
    }
}
