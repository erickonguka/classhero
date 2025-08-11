<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by role
        if ($request->role) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->status) {
            if ($request->status === 'banned') {
                $query->whereNotNull('banned_at');
            } else {
                $query->whereNull('banned_at');
            }
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $users = $query->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,teacher,learner',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    public function show(User $user)
    {
        $user->load(['courses', 'enrollments.course', 'payments', 'certificates.course']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,teacher,learner',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $data = $request->only(['name', 'email', 'role']);
        
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully!'
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete your own account'], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully!'
        ]);
    }

    public function ban(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        if ($user->id === Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Cannot ban your own account'], 403);
        }

        $user->update([
            'banned_at' => now(),
            'ban_reason' => $request->reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User banned successfully!'
        ]);
    }

    public function unban(User $user)
    {
        $user->update([
            'banned_at' => null,
            'ban_reason' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User unbanned successfully!'
        ]);
    }

    public function loginAs(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Cannot login as yourself');
        }

        session(['admin_user_id' => Auth::id()]);
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Logged in as ' . $user->name);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $users = User::where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->select('id', 'name', 'email', 'role')
                    ->limit(10)
                    ->get();
        
        return response()->json($users);
    }
    
    public function batchAction(Request $request)
    {
        try {
            $request->validate([
                'action' => 'required|in:ban,unban,delete',
                'users' => 'required|array',
                'users.*' => 'exists:users,id',
                'reason' => 'nullable|required_if:action,ban|string|max:500'
            ]);

            $users = User::whereIn('id', $request->users)->get();
            $count = 0;

            foreach ($users as $user) {
                if ($user->id === Auth::id()) continue;

                try {
                    switch ($request->action) {
                        case 'ban':
                            $user->update([
                                'banned_at' => now(),
                                'ban_reason' => $request->reason
                            ]);
                            $count++;
                            break;
                        case 'unban':
                            $user->update([
                                'banned_at' => null,
                                'ban_reason' => null
                            ]);
                            $count++;
                            break;
                        case 'delete':
                            $user->delete();
                            $count++;
                            break;
                    }
                } catch (\Exception $e) {
                    \Log::error('Batch action error for user ' . $user->id . ': ' . $e->getMessage());
                    continue;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully processed {$count} users"
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Batch action error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request'
            ], 500);
        }
    }

    public function certificates()
    {
        $certificates = \App\Models\Certificate::with(['user', 'course'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.certificates.index', compact('certificates'));
    }

    public function viewCertificate(\App\Models\Certificate $certificate)
    {
        return view('certificate.show', compact('certificate'));
    }

    public function downloadCertificate(\App\Models\Certificate $certificate)
    {
        return response()->download(storage_path('app/certificates/' . $certificate->file_path));
    }
}