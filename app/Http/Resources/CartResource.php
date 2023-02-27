<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'id'=>$request->id,
            'user_id'=>$this->user_id,
            'problemId'=>$request->problem_id,
            'problemTitle'=>$request->title,
            'image'=>$request->image,
            'price'=>$request->price,
            'time'=>$request->time,
            'quantity'=>$request->quantity,
        ];
    }
}
