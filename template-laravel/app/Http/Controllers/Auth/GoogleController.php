<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Auth;
use Exception;
use App\Models\User;

class GoogleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle() {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleCallback() {
        try {
            $user = Socialite::drive('google')->user();
            $findUser = User::where('google_id',$user->id)->first();

            if ($findUser) {
                Auth::login($findUser);
                return redirect('/home');
            }

            else {
                $newUser = User::create([
                    'email' => $user->email,
                    'google_id' => $user->id,
                    'password' => encrypt(''),
                    'username' => $user->name,
                    'type_user' => 'Student'
                ]);

                Auth::login($newUser);
                return redirect('/home');
            }
        }
        catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
