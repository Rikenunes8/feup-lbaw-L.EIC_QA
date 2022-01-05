<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/user';
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function getUser(){
        return $request->user();
    }

    public function home() {
        return redirect('login');
    }

// Google 

    public function loginWithGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackFromGoogle()
    {
        // try {
        //     $user = Socialite::driver('google')->user();

        //     // Check Users Email If Already There
        //     $is_user = User::where('email', $user->getEmail())->first();
        //     if(!$is_user){

        //         $saveUser = User::updateOrCreate([
        //             'google_id' => $user->getId(),
        //         ],[
        //             'name' => $user->getName(),
        //             'email' => $user->getEmail(),
        //             'password' => Hash::make($user->getName().'@'.$user->getId())
        //         ]);
        //     }else{
        //         $saveUser = User::where('email',  $user->getEmail())->update([
        //             'google_id' => $user->getId(),
        //         ]);
        //         $saveUser = User::where('email', $user->getEmail())->first();
        //     }


        //     Auth::loginUsingId($saveUser->id);

        //     return redirect()->route('home');
        // } catch (\Throwable $th) {
        //     throw $th;
        // }
    
       $user = Socialite::driver('google')->user();
    }
}
