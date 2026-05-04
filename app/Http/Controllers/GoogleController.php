<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleController extends Controller
{

    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [   
                    'username' => $googleUser->getName(),
                    'avatar' =>$googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)),
                ]
            );

            Auth::login($user);

        return redirect('/home');
        } catch (\Throwable $th) {
            return redirect('login')->with('error', 'lỗi đăng nhập google');
        }
    
    }

}