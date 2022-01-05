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
     * @return Notification The notification deleted.
     */
    public function delete(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');
        $notification = Notification::find($id);
        if (is_null($notification)) return App::abort(404);
        $this->authorize('delete', $notification);
        $notification->delete();
        return $notification;
    }

    /**
     * Remove the association between user and notification.
     * 
     * @param  Request  $request
     * @param  int  $id
     * @return Notification The notification removed.
     */
    public function remove(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');
        
        $notification = Auth::user()->notifications()->find($id);
        if (is_null($notification)) return App::abort(404);
        $this->authorize('remove', $notification);

        Auth::user()->notifications()->detach($notification->id);
    
        return $notification;
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
        $userNotifications = Auth::user()->notifications(); 

        $notification = $userNotifications->find($id);
        if (is_null($notification)) return App::abort(404);
        $this->authorize('read', $notification);

        $userNotifications->updateExistingPivot($notification->id, ['read' => true]);
        
        $notification = Notification::find($id);

        return $notification;
    }

}