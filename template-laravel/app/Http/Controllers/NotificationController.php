<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Shows all notifactions of a user.
     *
     * @return Response
     */
    public function list()
    {
        if (!Auth::check()) return redirect('/login');

        $notification = Auth::user()->notifications()->get();

        return view('pages.notifications', ['notifications' => $notifications]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        if (!Auth::check()) return redirect('/login');

        $notification = Auth::user()->notifications()->find($id);
        if (is_null($notification)) return App::abort(404);
        return view('pages.notification', ['notification' => $notification]);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  Request  $request
     * @param  int  $id
     * @return Uc The uc deleted.
     */
    public function delete(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');
        $uc = Uc::find($id);
        if (is_null($uc)) return App::abort(404);
        $this->authorize('delete', $uc);
        $uc->delete();
        return $uc;
    }

    /**
     * Mark notification as read.
     * 
     * @param  Request  $request
     * @param  int  $id
     * @return Notification The notification read.
     */
    public function read(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');
        $uc = Uc::find($id);
        if (is_null($uc)) return App::abort(404);
        $this->authorize('delete', $uc);
        $uc->delete();
        return $uc;
    }

}
