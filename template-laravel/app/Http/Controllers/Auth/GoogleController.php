<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use File;
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


            if(is_null($is_user)){
                $data = array('email' => $user->getEmail());
                Validator::make($data, [
                    'email' => 'required|string|email|allowed_domain|max:255|unique:users,email',
                ]);

                $fileContents = file_get_contents($user->getAvatar());
                $filename = '_'.time().'_'.Str::random(10).'.jpg';
                File::put(public_path().'/images/users/'.$filename, $fileContents);

                if (substr_compare($user->getEmail(), 'up', 0, 2) == 0) {       
                    $saveUser = User::create([
                        'email' => $user->getEmail(), 
                        'username' => 'g_'.(explode('@', $user->getEmail())[0]),
                        'password' => Hash::make($user->getName().'@'.$user->getId()),
                        'name' => $user->getName(),
                        'photo' => $filename,
                        'google_id' => $user->getId(),
                        'active' => true,
                        'entry_year' => substr($user->getEmail(), 2, 4),
                        'type' => 'Student'
                    ]);
                }
                else {
                    $saveUser = User::create([
                        'email' => $user->getEmail(),
                        'username' => 'g_'.(explode('@', $user->getEmail())[0]),
                        'password' => Hash::make($user->getName().'@'.$user->getId()),
                        'name' => $user->getName(),
                        'photo' => $filename,
                        'google_id' => $user->getId(),
                        'active' => true,
                        'type' => 'Teacher'
                    ]);
                }
                $saveUser->markEmailAsVerified();
            }
            else {
                $ownUser = User::where('email', $user->getEmail());
                $ownUser->update(['google_id' => $user->getId()]);
                $saveUser = $ownUser->first();
            }
            
            Auth::loginUsingId($saveUser->id);
            return redirect('/user');
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
