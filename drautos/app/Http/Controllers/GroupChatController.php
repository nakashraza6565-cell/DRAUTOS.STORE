<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupChat;
use Auth;

class GroupChatController extends Controller
{
    public function fetchMessages(Request $request)
    {
        $last_id = $request->input('last_id', 0);
        
        $messages = GroupChat::with('user:id,name,photo,role')
            ->where('id', '>', $last_id)
            ->orderBy('id', 'asc')
            ->take(100)
            ->get();

        // If it's the first load (last_id == 0), maybe we just want the last 50 messages
        if ($last_id == 0 && $messages->count() > 50) {
            $messages = GroupChat::with('user:id,name,photo,role')
                ->orderBy('id', 'desc')
                ->take(50)
                ->get()
                ->reverse()
                ->values();
        }

        return response()->json([
            'status' => 'success',
            'messages' => $messages
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $chat = GroupChat::create([
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        $chat->load('user:id,name,photo,role');

        return response()->json([
            'status' => 'success',
            'message' => $chat
        ]);
    }
}
