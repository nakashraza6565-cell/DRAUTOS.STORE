<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
class NotificationController extends Controller
{
    public function index(Request $request){
        $query = Auth()->user()->notifications();

        if($request->date_from){
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if($request->date_to){
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if($request->status){
            if($request->status == 'read'){
                $query->whereNotNull('read_at');
            } elseif($request->status == 'unread') {
                $query->whereNull('read_at');
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(5000);
        return view('backend.notification.index', compact('notifications'));
    }
    public function show(Request $request){
        $notification=Auth()->user()->notifications()->where('id',$request->id)->first();
        if($notification){
            $notification->markAsRead();
            return redirect($notification->data['actionURL']);
        }
    }
    public function delete($id){
        $notification=Notification::find($id);
        if($notification){
            $status=$notification->delete();
            if($status){
                request()->session()->flash('success','Notification successfully deleted');
                return back();
            }
            else{
                request()->session()->flash('error','Error please try again');
                return back();
            }
        }
        else{
            request()->session()->flash('error','Notification not found');
            return back();
        }
    }
}
