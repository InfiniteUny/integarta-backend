<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class AuthController extends Controller
{
    /* Login Using Google */
    public function loginUsingGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackFromGoogle(Request $request)
    {
        try {
            $state = $request->get('state');
            $request->session()->put('state',$state);
            $user = Socialite::driver('google')->user();

            $checkUser = User::firstWhere('provider_id', $user->getId());
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
                Auth::loginUsingId($saveUser->id);
            }else {
                Auth::loginUsingId(User::firstWhere('provider_id', $user->getId()));
            }

            return response()->json([
                'status' => 200,
                'message' => 'Ok',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Error',
            ]);
        }
    }

    /* Login Using Facebook */
    public function loginUsingFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function callbackFromFacebook(Request $request)
    {
    try {
        $state = $request->get('state');
        $request->session()->put('state',$state);
        $user = Socialite::driver('facebook')->user();

        $checkUser = User::firstWhere('provider_id', $user->getId());
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
            ]
        );
            Auth::loginUsingId($saveUser->id);
        }else {
            Auth::loginUsingId(User::firstWhere('provider_id', $user->getId()));
        }

        return response()->json([
            'status' => 200,
            'message' => 'Ok',
        ]);
        } catch (\Throwable $th) {
            return $th;
            return response()->json([
                'status' => 500,
                'message' => 'Error',
            ]);
        }
    }
}
