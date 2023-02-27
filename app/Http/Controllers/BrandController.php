<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
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
            'name' => 'bail|required|string|max:255',
            'service_id' => 'bail|required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
               ]);
        }
          $brand=new Brand;     
         $brand->name = $request->name;
         $brand->service_id = $request->service_id;
         $brand->save();
         return (new BrandResource($brand))->additional([
            'status' => true,
            'message' => 'Brand Stored successful.',
            'statusCode' => 200,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $brand = Brand::orderby('service_id','asc')->paginate(15);

        return BrandResource::collection(($brand))
        ->additional([
            'status' => true,
            'message' => 'Brand list successfully fetched.',
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
        $data = Brand::findOrFail($id);       
        $data->name = $request->name;
        $data->service_id = $request->service_id;

        $data->save();
        return (new BrandResource($data))->additional([
            'status' => true,
            'message' => 'Your Brand has been updated successfully.',
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
        $data=Brand::findOrfail($id);       
        $data->delete();

    return response()->json([
        'status' => true,
        'statusCode' => 200,
        'message'=>'Brand Data Has Been Deleted successfully!']);
    }
    
}
