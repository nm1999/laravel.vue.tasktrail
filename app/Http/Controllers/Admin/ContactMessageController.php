<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::where('workspace_id', auth()->user()->workspace_id)
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/ContactMessages/Index', [
            'messages' => $messages,
        ]);
    }

    public function markAsRead(ContactMessage $contactMessage)
    {
        abort_unless($contactMessage->workspace_id === auth()->user()->workspace_id, 404);

        $contactMessage->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'Message marked as read.');
    }
}
