<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrganizationContactController extends Controller
{
    public function create()
    {
        return Inertia::render('Organizations/Contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization_id' => 'required|string|exists:workspaces,public_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $workspace = Workspace::where('public_id', $validated['organization_id'])->firstOrFail();

        ContactMessage::create([
            'workspace_id' => $workspace->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
        ]);

        return redirect()->route('organizations.contact')->with('success', 'Your message has been sent.');
    }
}
