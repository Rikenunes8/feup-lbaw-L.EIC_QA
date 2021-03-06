<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends Controller
{

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    // protected $redirectTo = '/user';

    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
                        ? redirect('/user')
                        : view('auth.verify-email');
    }

    public function request(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return redirect()->back()
            ->with('success', trans('auth.verification_sent'));
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect()->to('/home')
            ->with('success', trans('auth.verification_check'));
    }
}
