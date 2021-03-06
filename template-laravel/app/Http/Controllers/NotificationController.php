<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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

        $notifications = Auth::user()->notifications()->orderBy('date', 'DESC')->get();

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
    public function apiRead(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');
        
        $read = $request->input('read');
        if ($read == 'true') $read = true;
        else if ($read == 'false') $read = false;

        $userNotifications = Auth::user()->notifications(); 

        $notification = $userNotifications->find($id);
        if (is_null($notification)) return App::abort(404);

        $userNotifications->updateExistingPivot($notification->id, ['read' => !$read]);
        
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
        // $url = $request->input('link');
        $userNotifications = Auth::user()->notifications(); 

        $notification = $userNotifications->find($id);
        if (is_null($notification)) return App::abort(404);
        $userNotifications->updateExistingPivot($notification->id, ['read' => true]);
        
        if ($notification->type == 'account_status') {
            $link = '/users/'.Auth::user()->id;
        } 
        else {
            if (is_null($notification->intervention())) return App::abort(404);

            $question = $notification->intervention()->first();
            if ($question->isAnswer()) $question = $question->parent()->first();
            else if ($question->isComment()) $question = $question->parent()->parent()->first();
            
            $link = '/questions/'.$question->id.'#'.$notification->intervention()->first()->id;          
        }
        return redirect($link);
    }

}
