<?php

namespace App\Http\Controllers;

use App\Http\Resources\Api\DistrictResource;
use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function list(Request $request){
        $district=District::orderby('name','asc')->get();
        return DistrictResource::collection($district);
    }
}
