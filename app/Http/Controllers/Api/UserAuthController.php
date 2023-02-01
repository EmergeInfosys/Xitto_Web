<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class UserAuthController extends Controller
{

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
               ]);
        }

        if (Hash::check($request->currentPassword, $request->user()->password)) {
            if ($request->user()->update(['password' => bcrypt($request->newPassword)])) {
                return response("Your password has been changed successfully.");
            } else {
                return response("Sorry, your password could not be changed. Please try again later.", 418,);
            }
        }
        return response("Your old password did not match. Please try again.", 418);
    }


    public function updateProfile(Request $request){

        if(auth()->user())
        {
            $validator = Validator::make($request->all(), [
                'id' => 'bail|required',
                'name' => 'bail|required|string|max:255',
                'email' => 'nullable|string|email|unique:users,email,' . $request->user()->id . ',id',
                'gender' => 'nullable|string|in:Male,male,Female,female,Others,others',
                'district_id' => 'nullable|numeric|exists:districts,id',
                'address' => 'sometimes|nullable',
                'image' => 'sometimes|nullable',
            ]);
            if ($validator->fails()) {
                return response()->json([
                 'success' => false,
                 'message' => 'User is Unauthorised',
                ]);
             }
            

             $user = User::find($request->id);       
                $user->name = $request->name;
                $user->email = $request->email;
                $user->gender = $request->gender;
                $user->district_id = $request->district_id;
                $user->address = $request->address;

        if($request->hasFile('image')) { 
            $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
            Storage::disk('public')->put($imageName, file_get_contents($request->image));
            #Using storage
            if(Storage::exists('public/' . $user->image)){
                Storage::delete('public/' . $user->image);
            }
            $user->image =  $imageName;
        }
        $user->save();
        return (new UserResource($user))->additional([
            'status' => true,
            'message' => 'Your profile has been updated successfully.',
            'statusCode' => 200
        ]);

        }else{
            return response()->json([
                'status' =>false,
                'message' => 'User is Unauthorised'
            ]);
        }
    }
    public function Userme(Request $request)
    {
        return new UserResource($request->user());
    }
}
