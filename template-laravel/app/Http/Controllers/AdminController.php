<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Uc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $this->authorize('showToAdmin', User::class);
        $users = User::where('active', '=', '1')->where('id', '!=', Auth::user()->id)->orderBy('name')->get();
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

        $this->authorize('showToAdmin', User::class);
        $ucs = Uc::orderBy('name')->get();
        return view('pages.admin.ucs', ['ucs' => $ucs]);
    }

    /**
     * Shows all teachers responsible for an uc in admin format.
     *
     * @param int $id
     * @return Response
     */
    public function listTeachers($id)
    {
        if (!Auth::check()) return redirect('/login');

        $this->authorize('showToAdmin', User::class);
        $uc = Uc::find($id);
        if (is_null($uc)) return App::abort(404);
        $teachersAssoc = $uc->teachers()->orderBy('name')->get();
        $uc_teachers_ids = $teachersAssoc->pluck('id')->toArray();
        $teachersNotAssoc = User::teachers()->where('active', '=', '1')->whereNotIn('id', $uc_teachers_ids)->orderBy('name')->get();
        
        return view('pages.admin.ucTeachers', ['uc' => $uc, 'teachersAssoc' => $teachersAssoc, 'teachersNotAssoc' => $teachersNotAssoc]);
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
