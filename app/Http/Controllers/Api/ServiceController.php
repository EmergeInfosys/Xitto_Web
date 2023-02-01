<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'bail|required|string|max:255',
            'image' => 'bail|required',
        ]);
        if ($validator->fails()) {
            return response()->json([
             'success' => false,
             'message' => 'User is Unauthorised',
            ]);
         }
          $service=new Service;     
         $service->title = $request->title;
 
         if($request->hasFile('image')) { 
             $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
             Storage::disk('public')->put($imageName, file_get_contents($request->image));
             $service->image =  $imageName;
         }
         $service->save();
         return (new ServiceResource($service))->additional([
            'status' => true,
            'message' => 'Service Stored successful.',
            'statusCode' => 200,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $service = Service::paginate(15);

        return ServiceResource::collection(($service))
        ->additional([
            'status' => true,
            'message' => 'Service list successfully fetched.',
            'statusCode' => 200
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $serviceId)
    {
        $data = Service::findOrFail($serviceId);       
        $data->title = $request->title;

        if($request->hasFile('image')) { 
            $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
            Storage::disk('public')->put($imageName, file_get_contents($request->image));
            #Using storage Delete previously saved image
            if(Storage::exists('public/' . $data->image)){
                Storage::delete('public/' . $data->image);
            }
            $data->image =  $imageName;
        }
        $data->save();
        return (new ServiceResource($data))->additional([
            'status' => true,
            'message' => 'Your Service has been updated successfully.',
            'statusCode' => 200
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy($serviceId)
    {
        $data=Service::findOrfail($serviceId);
        if(Storage::exists('public/' . $data->image)){
            Storage::delete('public/' . $data->image);
        }
        
        $data->delete();

    return response()->json([
        'status' => true,
    'statusCode' => 200,
    'message'=>'Service Data deleted successfully!']);
    }
}
