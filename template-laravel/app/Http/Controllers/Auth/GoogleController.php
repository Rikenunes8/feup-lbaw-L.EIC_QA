<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;



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
                $data = array('email' => $user->getEmail());
                Validator::make($data, [
                    'email' => 'required|string|email|allowed_domain|max:255|unique:users,email',
                ]);
                // $file = $user->getAvatar();
                // $filename = '_'.time().'_'.Str::random(10).'.'.$file->getClientOriginalExtension();
                // $file->storeAs('users', $filename, 'images_uploads');

                $filename = 'default.jpg';
                if (substr_compare($user->getEmail(), 'up', 0, 2) == 0) {       
                    $saveUser = User::create([
                        'email' => $user->getEmail(), 
                        'username' => $user->getEmail(),
                        'password' => Hash::make($user->getName().'@'.$user->getId()),
                        'name' => $user->getName(),
                        'photo' => $filename,
                        'type' => 'Student',
                        'entry_year' => substr($user->getEmail(), 2, 4),
                        'google_id' => $user->getId()
                    ]);
                }
                else {
                    $saveUser = User::create([
                        'email' => $user->getEmail(), 
                        'username' => $user->getEmail(),
                        'password' => Hash::make($user->getName().'@'.$user->getId()),
                        'name' => $user->getName(),
                        'photo' => $filename,
                        'type' => 'Teacher',
                        'google_id' => $user->getId()
                    ]);
                }

            }
            else{
                $saveUser = User::where('email',  $user->getEmail())->update([
                    'google_id' => $user->getId(),
                ]);
                $saveUser = User::where('email', $user->getEmail())->first();
                if ($saveUser->active == 1) {
                    Auth::loginUsingId($saveUser->id);
                }
                else {
                    $errors = ['email' => trans('auth.notactivated')];
                    return redirect('/login')->withErrors($errors);
                }
            }
            
            return redirect('/user');
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
