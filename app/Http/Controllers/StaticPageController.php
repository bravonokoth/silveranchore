<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\NotificationSent;

class StaticPageController extends Controller
{
    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:1000',
        ]);

        // Trigger notification (e.g., to admin)
        event(new NotificationSent(
            "New contact form submission from {$validated['name']}: {$validated['message']}",
            $validated['email'],
            auth()->id(),
            session()->getId()
        ));

        return redirect()->route('contact')->with('success', 'Your message has been sent successfully!');
    }
}