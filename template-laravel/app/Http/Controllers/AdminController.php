<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Shows all users in admin format.
     *
     * @return Response
     */
    public function listUsers()
    {
        if (!Auth::check()) return redirect('/login');

        $users = User::orderBy('username')->get();
        return view('pages.admin.users', ['users' => $users]);
    }

    /**
     * Shows all ucs in admin format.
     *
     * @return Response
     */
    public function listUcs()
    {
        if (!Auth::check()) return redirect('/login');

        $ucs = Uc::orderBy('name')->get();
        return view('pages.admin.ucs', ['ucs' => $ucs]);
    }

    /**
     * Shows all reports in admin format.
     *
     * @return Response
     */
    public function listReports()
    {
        // TODO
        // Notifications of type report
        return; //view('pages.admin.reports', ['reports' => $reports]);
    }
    
}
