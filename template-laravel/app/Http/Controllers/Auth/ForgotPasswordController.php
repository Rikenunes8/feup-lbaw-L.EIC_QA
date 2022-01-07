<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Sentinel;
use Reminder;
use App\Models\User;
use Mail;


class ForgotPasswordController extends Controller
{
    public function forgot() {
   
        return view('auth.reset_password');
   
   
        //     $credentials = request()->validate(['email' => 'required|email']);

    //     Password::sendResetLink($credentials);

    //    // return response()->json(["msg" => 'Reset password link sent on your email id.']);
    //    return $this->respondWithMessage('Reset password link sent on your email id.');
    }

    public function password(Request $request) {

    $user = User::whereEmail($request->email)->first();

    if($user== null){
        return redirect()->back()->with(['error'=> 'Email não existente']);
    }

    $user = Sentinel::findById($user->id);
    $reminder = Reminder::exists($user) ?: Reminder::create($user);
    $this-> sendEmail($user, $reminder->code);
    return redirect ()->back()->with(['success'=> 'Email enviado']);
    }

    public function sendEmail($user,$code){
        Mail::send(
            'email.forgot',
            ['user' => $user, 'code' => $code],
            function ($message) use($user){
                $message->to($user->email);
                $message->subject("$user->name, Recuperar password");
            }
        );
    }

}