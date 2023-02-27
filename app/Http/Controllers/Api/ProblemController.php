<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProblemResource;
use App\Models\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Gloudemans\Shoppingcart\Facades\Cart;

class ProblemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $problems =Problem::orderBy('id','asc')->paginate(15);
       $cart=Cart::content();
       return ProblemResource::collection(($problems))
       ->additional([
           'status' => true,
           'message' => 'All Problems list successfully fetched.',
           'statusCode' => 200
       ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
            'description' => 'bail|required',
            'service_id' => 'required',
            'price' => 'integer|required',
            'time' => 'required',
            'quantity' =>'bail',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
               ]);
        }
          $problem=new Problem;     
         $problem->title = $request->title;
         $problem->description = $request->description;
         $problem->service_id = $request->service_id;
         $problem->price = $request->price;
         $problem->service_charge = $request->service_charge;
         $problem->time = $request->time;
         $problem->quantity = $request->quantity;
 
         if($request->hasFile('image')) { 
             $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
             Storage::disk('public')->put($imageName, file_get_contents($request->image));
             $problem->image =  $imageName;
         }
         $problem->save();
         return (new ProblemResource($problem))->additional([
            'status' => true,
            'message' => 'Problems Stored successful.',
            'statusCode' => 200,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($serviceid)
    {
        $problems =Problem::where('service_id',$serviceid)->orderBy('id','asc')->paginate(15);
        return ProblemResource::collection(($problems))
        ->additional([
            'status' => true,
            'message' => 'All Problems list successfully fetched.',
            'statusCode' => 200
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $problem = Problem::findOrFail($id);       
        $problem->title = $request->title;
        $problem->description = $request->description;
        $problem->service_id = $request->service_id;
        $problem->price = $request->price;
        $problem->service_charge = $request->service_charge;
        $problem->time = $request->time;
        $problem->quantity = $request->quantity;

        if($request->hasFile('image')) { 
            $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
            Storage::disk('public')->put($imageName, file_get_contents($request->image));
            #Using storage Delete previously saved image
            if(Storage::exists('public/' . $problem->image)){
                Storage::delete('public/' . $problem->image);
            }
            $problem->image =  $imageName;
        }
        $problem->save();
        return (new ProblemResource($problem))->additional([
            'status' => true,
            'message' => 'Your Problem has been updated successfully.',
            'statusCode' => 200
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data=Problem::findOrfail($id);
        if(Storage::exists('public/' . $data->image)){
            Storage::delete('public/' . $data->image);
        }
        
        $data->delete();

    return response()->json([
        'status' => true,
    'statusCode' => 200,
    'message'=>'Problem Data deleted successfully!']);
    }
    
}
