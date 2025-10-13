<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewUserRegisteredNotification;
use App\Events\NotificationSent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            // Create new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Assign default role
            $user->assignRole('client');

            // Fetch all admins and super-admins
            $admins = User::role(['admin', 'super-admin'])->get();
            Log::info('Admins found: ' . $admins->count() . ' [' . $admins->pluck('email')->implode(', ') . ']');

            if ($admins->isNotEmpty()) {
                // Send notification to all admins
                Notification::send($admins, new NewUserRegisteredNotification($user));

                // Broadcast notification to all admins in real time
                foreach ($admins as $admin) {
                    event(new NotificationSent(
                        "New user {$user->name} ({$user->email}) has registered.",
                        $admin->email,
                        $admin->id
                    ));
                }

                Log::info('NewUserRegisteredNotification sent to: ' . $admins->pluck('email')->implode(', '));
            } else {
                Log::warning('No admins or super-admins found for NewUserRegisteredNotification');
            }

            // Fire Laravel's built-in Registered event
            event(new Registered($user));

            // Log in the new user
            Auth::login($user);

            return redirect()->intended(route('dashboard', absolute: false))
                ->with('success', 'Registration successful! Welcome aboard.');
        } catch (\Exception $e) {
            Log::error('Error during user registration: ' . $e->getMessage());
            throw $e;
        }
    }
}
