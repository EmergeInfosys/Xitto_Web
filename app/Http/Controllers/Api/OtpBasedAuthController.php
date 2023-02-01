<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Models\Network;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Exists;

class OtpBasedAuthController extends Controller
{
    public function generate(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required|exists:users,phone',
            'country_code' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
               ]);
        }
            #Generate an OTP
        $userOtp = $this->generateOTP($request->phone,$request->country_code);
        $userOtp->sendSMS($request->phone,$request->country_code);//send otp
        return response()->json([
            'status' => true,
            'data' => $userOtp,
            'message' => 'OTP has been send successfully',
        ]);
    }

    public function generateOTP($phone,$country_code){
       $user = User::where('phone',$phone)->where('country_code',"+".$country_code)->first();
       #User does not have any existing otp
       $userOtp = Otp::where('user_id',$user->id)->latest()->first();
       $now = now();
          #1.Otp already available but not expired
       if($userOtp && $now->isBefore($userOtp->expire_at)){
        return $userOtp;
       }
       #create New Otp
       return $userOtp = Otp::create([
        'user_id' => $user->id,
        'otp' => rand(123456, 999999),
        'expire_at' =>$now->addMinutes(10),
       ]);

    }

    public function verifyOtp(Request $request){
        $validator = Validator::make($request->all(), [
            'otp' => 'required',
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
               ]);
        }
        $userOtp=Otp::where('user_id',$request->user_id)->where('otp',$request->otp)->first();
        $now=now();
        if(!$userOtp){
         return "Your Otp Does not Matched!";   
        }
        #Check Expired Otp
        elseif($userOtp && $now->isAfter($userOtp->expire_at)){
            return "Your Otp Has Been Expired!";
        }
        #check User
        $user = User::whereId($request->user_id)->first();
        if($user){
            #Expire the otp
            $userOtp->update([
                'expire_at' => now(),
            ]);
            Auth::login($user);
            return "Successfully Login!";
        }

        return "Your Otp Is Not Correct!";

        
    }





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
            'referral_code' => 'nullable',
            'address' => 'sometimes|nullable',
            'image' => 'nullable',
        ]);
        #Generate Token
        #$token = $request->user()->createToken(env('APP_NAME', 'Xittoo'))->plainTextToken;
        #generate random refer code
        $referCode= Str::random(10);
        #check referral_code is present or not
        if(isset($request->referral_code)){
            $userReferr=User::where('referral_code',$request->referral_code)->get();
            if(count($userReferr) > 0){
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'country_code' => $data['country_code'],
                    'phone' => $data['phone'],
                    'password' => Hash::make($data['password']),
                    // 'access_token' => $token,
                    'gender' => $data['gender'],
                    'district_id' => $data['district_id'],
                    'address' => $data['address'],
                    'referral_code' => $referCode,
                    'image' => $data['image'],
        
                 ]);
                 $networkData= Network::create([
                    'referral_code' => $request->referral_code,
                    'user_id' => $user->id,
                    'parent_user_id' =>$userReferr[0]['id'],
                 ]);

                 return (new UserResource($user))->additional([
                    'status' => true,
                    'message' => 'Registration With Referral Code.',
                    'used Refferal Code' => $networkData->referral_code,
                    'Parent User Id' => $networkData->parent_user_id,
                    'statusCode' => 200,
                ]);

            }else{
                return response()->json([
                    'message' => 'Referral Code Doesnot Match',
                ]);
            }


        }else{
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'country_code' => $data['country_code'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                // 'access_token' => $token,
                'gender' => $data['gender'],
                'district_id' => $data['district_id'],
                'address' => $data['address'],
                'referral_code' => $referCode,
                'image' => $data['image'],
    
             ]);   
        }
        // $user['access_token'] = $user->createToken(env('APP_NAME', 'Xittoo'))->plainTextToken;
         return (new UserResource($user))->additional([
            'status' => true,
            'message' => 'Registration successful.',
            'statusCode' => 200,
        ]);
    }

    public function login(Request $request)
    {    
        $validator=Validator::make($request->all(),[
            'email' => 'required_without:countryCode,phone|string',
            'countryCode' => 'required_without:email|string',
            'phone' => 'required_without:email|string',
            'password' => 'required|string|min:4',
            'access_token' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
               ]);
        }
        if ($request->email) {
            //Email Based Login
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();

                //Check for Block stat
                if ($user->is_blocked == 1) {
                    Auth::logout();
                    return response("Your account has been blocked. Please contact to support.", 401);
                }

                //Delete all old tokens
                // $user->tokens()->delete();

                $user['access_token'] = $user->createToken(env('APP_NAME', 'SMART HEALTH'))->plainTextToken;
                $user->save();

                return (new UserResource($user))->additional([
                    'status' => true,
                    'message' => 'Login successful.',
                    'statusCode' => 200,
                ]);
            }
            return response("These credentials do not match our records.", 401);
        }else{
            #Phone based Login
            if(Auth::attempt(['phone' => $request->phone, 'country_code' => "+" . $request->country_code, 'password' => $request->password,]))
            {
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
            }
            return Response("These credentials do not match our records.", 422);
        }
       
    }

    public function forgotPasswordOtp(Request $request)
    {
        
        $user=User::where('phone',$request->phone)->where('country_code',"+".$request->country_code)->first();
       
        if (!$user) {
            return response()->json([
                'status' => false, 
                'message' => 'User not found.',
                'statusCode' => 418]);
        }
        $oldOtp = Otp::where('user_id', $request->user_id)->first();
        if ($oldOtp) {
            $oldOtp->delete();
        }
        #Generate an OTP
        $userOtp = $this->generateOTP($request->phone,$request->country_code);
        $userOtp->sendSMS($request->phone,$request->country_code);//send otp
        return response()->json([
            'status' => true,
            'data' => $userOtp,
            'message' => 'OTP has been send successfully',
        ]);
    }

    public function updateForgotPasswordOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string',
            'phone' => 'required|string',
            'country_code' => 'required|string',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
               ]);
        }
        $userOtpExist=Otp::where('user_id',$request->user_id)->where('otp',$request->otp)->first();
        if(!$userOtpExist){
         return "Your Otp Does not Matched!";   
        }
        $user = User::where('phone', $request->phone)->where('country_code', '+' . $request->country_code)->first();

        Otp::where('otp', $request->otp)->delete();

        if ($user->update(['password' => bcrypt($request->password)])) {
            return response("Your password has been set successfully.");
        }
        return response("Sorry, your password could not be set. Please try again later.", 418);
        
    }
}
