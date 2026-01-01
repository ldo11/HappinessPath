<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserQuizResult;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['userJourney']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15);
        
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['user', 'translator', 'consultant', 'admin'])],
            'city' => 'nullable|string|max:255',
            'spiritual_preference' => 'nullable|string|max:255',
            'geo_privacy' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['user', 'translator', 'consultant', 'admin'])],
            'city' => 'nullable|string|max:255',
            'spiritual_preference' => 'nullable|string|max:255',
            'geo_privacy' => 'boolean',
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function resetAssessment(User $user)
    {
        if ($user->role !== 'user') {
            return redirect()->back()->with('error', 'Only users can have their assessment reset.');
        }

        DB::transaction(function () use ($user) {
            DB::table('assessment_answers')->where('user_id', $user->id)->delete();
            DB::table('user_pain_points')->where('user_id', $user->id)->delete();
            UserQuizResult::withTrashed()->where('user_id', $user->id)->forceDelete();

            $user->onboarding_status = 'new';
            $user->save();
        });

        app(AdminService::class)->resetAssessment($user->id);

        return redirect()->back()->with('success', 'Assessment reset successfully.');
    }

    public function verify(User $user)
    {
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();
        }

        return redirect()->route('admin.users.edit', $user)
            ->with('success', 'User email verified successfully.');
    }
}
