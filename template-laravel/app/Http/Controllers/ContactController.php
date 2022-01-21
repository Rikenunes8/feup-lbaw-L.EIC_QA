<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send()
    {
        $data = request()->validate([
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required'
        ]);

        // Send an Email
        Mail::to('leic.qa@fe.up.qa.pt')->send(new ContactFormMail($data));
        
        return redirect()->back()
            ->with('success', 'Email enviado com sucesso');
    }
}
