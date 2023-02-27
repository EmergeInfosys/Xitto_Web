<?php

namespace App\Http\Resources;

use App\Models\Brand;
use App\Models\Service;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $service = Service::where('id',$this->service_id)->orderBy('id','DESC')->get();

        return [
            'id'=>$this->id,
            'name' => $this->name,
            'service_id' => $this->service_id,
            'serviceName' => $this->service->title,
            // 'serviceDetail'=>ServiceResource::collection($service),
        ];
    }
}
