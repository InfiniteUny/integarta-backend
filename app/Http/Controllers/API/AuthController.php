<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Institution;
use App\Traits\Token;
use Validator;

class AuthController extends BaseController
{
    use Token;

    /**
     * Add account api
     */
    public function addAccount(Request $request)
    {
        $institution = Institution::where('brick_institution_id', $request->institution_id)->first();

        if ($institution->type == 'E-Wallet') {
           $data =  $this->generateOTP($institution->institution_id, $request->account_number);
        }
    }

    /**
     * generate OTP
     */
    public function generateOTP($id, $username){
        $endpoint = config('api.brick_url') . 'v1/auth';
        $token = config('api.brick_api_key');
        $response = Http::withToken($token)->get($endpoint, [
            'institution_id' => $id,
            'username' => $username
        ]);
        return $response->json($response->data, 200);
    }

    /**
     * Register api
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken($user->name)->plainTextToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Logout api
     */
    public function logout()
    {   
        try {
            if (Auth::check()) {
                Auth::guard('api')->logout()->currentAccessToken()->delete();
                return $this->sendResponse($success, 'Logout successfully.');
            }
        } catch (\Throwable $th) {
            return $this->sendError('Not logged in.', ['error'=>'Not logged in']);
        }
    }
   
    /**
     * Login email api
     */
    public function loginUsingEmail(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken($user->name)->plainTextToken; 
            $success['name'] =  $user->name;
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    /**
     * Login google api
     */
    public function loginUsingGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callbackFromGoogle(Request $request)
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();

            $checkUser = User::firstWhere('email', $user->getEmail());
            if (!$checkUser) {
                $saveUser = User::firstOrCreate([
                    'email' => $user->getEmail(),
                ],
                [
                    'provider_id' => $user->getId(),
                    'provider' => 'google',
                    'avatar' => $user->getAvatar(),
                    'name' => $user->getName(),
                    'password' => bcrypt($user->getName().'@'.$user->getId())
                ]);
                $authUser = Auth::loginUsingId($saveUser->id);
                $success['token'] =  $authUser->createToken('integarta')->plainTextToken;
                $success['name'] =  $authUser->name;

            }else {
                $authUser = Auth::loginUsingId(User::firstWhere('email', $user->getEmail())->id);
                $success['token'] =  $authUser->createToken('integarta')->plainTextToken;
                $success['name'] =  $authUser->name;
            }

            return $this->sendResponse($success, 'User login successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }

    /**
     * Login facebook api
     */
    public function loginUsingFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    public function callbackFromFacebook(Request $request)
    {
    try {
        $user = Socialite::driver('facebook')->stateless()->user();

        $checkUser = User::firstWhere('email', $user->getEmail());
        if (!$checkUser) {
            $saveUser = User::firstOrCreate([
                'email' => $user->getEmail(),
            ],
            [
                'provider_id' => $user->getId(),
                'provider' => 'facebook',
                'avatar' => $user->getAvatar(),
                'name' => $user->getName(),
                'password' => bcrypt($user->getName().'@'.$user->getId())
            ]);
            $authUser = Auth::loginUsingId($saveUser->id);
            $success['token'] =  $authUser->createToken('integarta')->plainTextToken;
            $success['name'] =  $authUser->name;

        }else {
            $authUser = Auth::loginUsingId(User::firstWhere('email', $user->getEmail())->id);
            $success['token'] =  $authUser->createToken('integarta')->plainTextToken;
            $success['name'] =  $authUser->name;
        }

        return $this->sendResponse($success, 'User login successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }
}
