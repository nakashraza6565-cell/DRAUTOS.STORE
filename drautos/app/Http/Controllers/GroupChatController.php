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
            'message' => 'nullable|string',
            'audio' => 'nullable|file|max:10240' // 10MB max, accept any file to avoid MIME issues from different browsers
        ]);

        if (!$request->message && !$request->hasFile('audio')) {
            return response()->json(['status' => 'error', 'message' => 'Cannot send empty message.']);
        }

        $filePath = null;
        $fileType = null;

        if ($request->hasFile('audio')) {
            $file = $request->file('audio');
            
            // Ensure directory exists
            $dir = public_path('storage/chat_audio');
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            
            $ext = $file->getClientOriginalExtension() ?: 'webm';
            if (!in_array($ext, ['webm', 'mp3', 'wav', 'ogg', 'mp4', 'm4a', 'aac'])) {
                $ext = 'webm'; // Fallback
            }
            
            $filename = time() . '_' . uniqid() . '.' . $ext;
            
            // Move file
            $file->move($dir, $filename);
            $filePath = '/storage/chat_audio/' . $filename;
            $fileType = 'audio';
        }

        $chat = GroupChat::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'file_path' => $filePath,
            'file_type' => $fileType
        ]);

        $chat->load('user:id,name,photo,role');

        return response()->json([
            'status' => 'success',
            'message' => $chat
        ]);
    }
}
