<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $stats = [];
        
        if ($user->role === 'learner') {
            $stats = [
                'courses_enrolled' => $user->enrollments()->count(),
                'courses_completed' => $user->enrollments()->where('progress_percentage', 100)->count(),
                'total_points' => $user->points ?? 0,
                'certificates' => $user->certificates()->count(),
            ];
        } elseif ($user->role === 'teacher') {
            $stats = [
                'courses_created' => $user->courses()->count(),
                'total_students' => $user->courses()->sum('enrolled_count'),
                'total_revenue' => $user->courses()->where('is_free', false)->sum('price') * 0.7,
                'avg_rating' => $user->courses()->avg('rating'),
            ];
        } elseif ($user->role === 'admin') {
            $stats = [
                'total_users' => \App\Models\User::count(),
                'total_courses' => \App\Models\Course::count(),
                'total_revenue' => \App\Models\Payment::where('status', 'completed')->sum('amount'),
                'platform_growth' => \App\Models\User::whereMonth('created_at', now()->month)->count(),
            ];
        }

        return view('profile.edit', compact('user', 'stats'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}