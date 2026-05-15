<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Support\SanitizesInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::query()
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    public function myMessages(Request $request)
    {
        $messages = ContactMessage::query()
            ->where('email', $request->user()->email)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    // Stores a contact message sent from the public contact form.
    public function store(Request $request)
    {
        $request->merge([
            'name' => SanitizesInput::plain($request->input('name'), 255),
            'email' => SanitizesInput::email($request->input('email')),
            'subject' => SanitizesInput::plain($request->input('subject'), 255),
            'message' => SanitizesInput::paragraph($request->input('message'), 5000),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $message = ContactMessage::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Votre message a bien ete envoye.',
            'data' => $message,
        ], 201);
    }
}
