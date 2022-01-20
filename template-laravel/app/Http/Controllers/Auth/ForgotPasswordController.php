<?php 
  
namespace App\Http\Controllers\Auth; 

use App\Models\User; 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

  
class ForgotPasswordController extends Controller
{
    /**
     * Show the form for recover password.
     *
     * @return response()
     */
    public function showForgetPasswordForm()
    {
        if (Auth::check()) return redirect('/user');
        return view('auth.password.forgot');
    }
  
    /**
     * Send a email with the link to recover password
     *
     * @return response()
     */
    public function submitForgetPasswordForm(Request $request)
    { 
        if (Auth::check()) return redirect('/user');

        $request->validate([
            'email' => 'required|email|exists:users',
        ]);


        $status = Password::sendResetLink(
            $request->only('email')
        );
    
        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['message' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show the form for reset a password.
     *
     * @return response()
     */
    public function showResetPasswordForm($token) { 
        if (Auth::check()) return redirect('/user');
        return view('auth.password.reset', ['token' => $token]);
    }

    /**
     * Change the password
     *
     * @return response()
     */
    public function submitResetPasswordForm(Request $request)
    {
        if (Auth::check()) return redirect('/user');

        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);
    
                $user->save();
    
                event(new PasswordReset($user));
            }
        );
    
        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with(['message' => __($status)])
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
