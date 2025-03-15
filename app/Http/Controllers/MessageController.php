<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User; // Import the User model

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $senderId = Auth::id();
        $messageData = [
            'sender_id' => $senderId,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'timestamp' => now(),
        ];

        // Store message in a local file
        Storage::append('messages.txt', json_encode($messageData));

        return response()->json(['status' => 'Message sent!'], 200);
    }

    public function getMessages()
    {
        // Retrieve messages from the local file
        $messages = Storage::get('messages.txt');
        $messagesArray = array_map('json_decode', explode(PHP_EOL, trim($messages)));

        return response()->json($messagesArray);
    }

    public function getUsers()
    {
        // Fetch all users except the authenticated user
        return User::where('id', '!=', Auth::id())->get(['id', 'name']);
    }
}