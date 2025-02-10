<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ContactUsController extends Controller
{
    // List all contact form submissions
    public function index()
    {
        $contacts = ContactUs::all();
        return response()->json($contacts);
    }

    // Store a new contact form submission
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        $contact = ContactUs::create($validated);

        return response()->json(['message' => 'Your message has been sent successfully!', 'contact' => $contact], 201);
    }

    // Show a specific contact form submission by ID
    public function show($id)
    {
        $contact = ContactUs::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Contact submission not found'], 404);
        }

        return response()->json($contact);
    }
}
