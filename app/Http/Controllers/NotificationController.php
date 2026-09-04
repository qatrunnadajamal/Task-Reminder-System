<?php
namespace App\Http\Controllers;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // list
    public function index()
    {
        return Notification::where('user_id', Auth::id())
            ->latest()->take(10)->get();
    }

    // count unread
    public function unreadCount()
    {
        return Notification::where('user_id', Auth::id())
            ->where('is_read', false)->count();
    }

    // mark read
    public function markAsRead($id)
    {
        Notification::where('id', $id)->update([
            'is_read' => true
        ]);
        return response()->json(['success' => true]);
    }

    //delete 
    public function destroy($id)
    {
        Notification::where('user_id', Auth::id())
        ->findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}