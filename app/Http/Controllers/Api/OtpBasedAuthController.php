<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class OtpBasedAuthController extends Controller
{
    public function register(Request $request){
        $data = $request->validate([
            'name' => 'bail|required|string|max:255',
            'email' => 'bail|nullable|email|unique:users,email',
            'country_code' => 'bail|required|string|min:2',
            'phone' => 'bail|required|string|min:10|max:10|unique:users,phone',
            'password' => 'bail|required|string|min:8',
            'access_token' => 'nullable|string',
            'gender' => 'nullable|string|in:Male,male,Female,female,Others,others',
            'district_id' => 'nullable|numeric|exists:districts,id',
            'address' => 'sometimes|nullable',
            'image' => 'nullable',
        ]);
    
       
         $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'country_code' => $data['country_code'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            // 'access_token' => $data['access_token'],
            'gender' => $data['gender'],
            'district_id' => $data['district_id'],
            'address' => $data['address'],
            'image' => $data['image'],

         ]);
          

         $user->access_token = $user->createToken(env('APP_NAME', 'Xittoo'))->plainTextToken;
         return (new UserResource($user))->additional([
            'status' => true,
            'message' => 'Registration successful.',
            'statusCode' => 200,
        ]);
    }

    public function login(Request $request){      
        if(Auth::attempt(['phone' => $request->phone, 'country_code' => "+" . $request->country_code, 'password' => $request->password,])){
            $user = Auth::user();
            
            //Check for wether User is Blocked or Not
            if ($user->is_blocked == 1) {
                Auth::logout();
                return Response("Your account has been blocked. Please contact to support.", 422);
            }
            $user['access_token'] = $user->createToken(env('APP_NAME', 'Xittoo'))->plainTextToken;
            return (new UserResource($user))->additional([
                'status' => true,
                'message' => 'Login successful.',
                'statusCode' => 200,
            ]);
        }else{
            $response =[
                'success' => false,
                'message' => 'Unauthorized',
            ];
            return response()->json($response);

        }
    }
}
