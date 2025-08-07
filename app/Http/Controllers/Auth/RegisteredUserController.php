<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:learner,teacher'],
            'preferred_categories' => ['nullable', 'array'],
            'phone' => ['nullable', 'string', 'max:25'],
            'country_code' => ['nullable', 'string', 'size:2'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'preferred_categories' => $request->preferred_categories ?? [],
            'phone' => $request->phone,
            'country_code' => $request->country_code,
            'currency' => $request->currency ?: 'USD',
        ]);

        // Assign role
        $user->assignRole($request->role);

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on role
        if ($request->role === 'teacher') {
            return redirect(route('teacher.dashboard'));
        }

        return redirect(route('dashboard'));
    }
}
