<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewUserRegisteredNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('client');

        // Notify admins and super-admins
        $admins = User::role(['admin', 'super-admin'])->get();
        \Log::info('Admins found: ' . $admins->count() . ' [' . $admins->pluck('email')->implode(', ') . ']');
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewUserRegisteredNotification($user));
            \Log::info('NewUserRegisteredNotification sent to: ' . $admins->pluck('email')->implode(', '));
        } else {
            \Log::warning('No admins or super-admins found for NewUserRegisteredNotification');
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}