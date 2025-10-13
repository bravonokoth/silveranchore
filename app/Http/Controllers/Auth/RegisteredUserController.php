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

        // Fire Laravel’s built-in Registered event (sends verification email)
        event(new Registered($user));

        // Notify admins
        $admins = User::role(['admin', 'super-admin'])->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewUserRegisteredNotification($user));

            foreach ($admins as $admin) {
                event(new NotificationSent(
                    "New user {$user->name} ({$user->email}) has registered.",
                    $admin->email,
                    $admin->id
                ));
            }
        }

        // ❌ Don’t log them in yet (forces email verification)
        // Auth::login($user);

        return redirect()->route('verification.notice')
            ->with('status', 'verification-link-sent');

    } catch (\Exception $e) {
        Log::error('Error during user registration: ' . $e->getMessage());
        throw $e;
    }
}

}
