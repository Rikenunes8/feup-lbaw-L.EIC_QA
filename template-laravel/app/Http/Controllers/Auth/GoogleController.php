<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class GoogleController extends Controller
{
    public function loginWithGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackFromGoogle()
    {
        try {
            $user = Socialite::driver('google')->user();

            // Check Users Email If Already There
            $is_user = User::where('email', $user->getEmail())->first();

            if(!$is_user){

                $saveUser = User::updateOrCreate([
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'username' => $user->getName(),
                    'password' => Hash::make($user->getName().'@'.$user->getId()),
                    'photo' => $user->getAvatar(),
                    'type' => 'Student',
                    'entry_year' => 2018,
                    'google_id' => $user->getId()
                ]);
            }

            else{
                $saveUser = User::where('email',  $user->getEmail())->update([
                    'google_id' => $user->getId(),
                ]);
                $saveUser = User::where('email', $user->getEmail())->first();
            }

            Auth::loginUsingId($saveUser->id);
            return redirect('/home');

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
