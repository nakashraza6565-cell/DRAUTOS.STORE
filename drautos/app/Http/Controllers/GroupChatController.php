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
        $isOpen = $request->input('is_open', '0') === '1';

        // Update user activity and read receipts
        $user = Auth::user();
        if ($user) {
            $user->last_active_at = now();
            if ($isOpen) {
                $maxId = GroupChat::max('id') ?? 0;
                if ($maxId > $user->last_read_message_id) {
                    $user->last_read_message_id = $maxId;
                }
            }
            $user->save();
        }

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
        
        $team = \App\User::whereIn('role', ['admin', 'staff'])
            ->get(['id', 'name', 'last_active_at', 'last_read_message_id'])
            ->map(function($member) {
                if ($member->last_active_at) {
                    $lastActive = \Carbon\Carbon::parse($member->last_active_at);
                    $diffMins = $lastActive->diffInMinutes(now());
                    $member->is_online = $diffMins <= 2;
                    $member->last_active_str = $member->is_online ? 'Online' : $lastActive->diffForHumans();
                } else {
                    $member->is_online = false;
                    $member->last_active_str = 'Never';
                }
                return $member;
            });

        return response()->json([
            'status' => 'success',
            'messages' => $messages,
            'team' => $team
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
            
            // Ensure directory exists in the actual web root (not Laravel's public folder)
            $dir = base_path('../storage/chat_audio');
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
