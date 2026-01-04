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

        return redirect()->route('user.admin.users.index', ['locale' => app()->getLocale()])
            ->with('success', 'User created successfully.');
    }

    public function edit()
    {
        $userModel = $this->resolveUser();
        $userModel->load('painPoints');
        return view('admin.users.edit', ['user' => $userModel]);
    }

    public function update(Request $request)
    {
        $userModel = $this->resolveUser();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userModel->id)],
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

        $userModel->update($validated);

        return redirect()->route('user.admin.users.index', ['locale' => app()->getLocale()])
            ->with('success', 'User updated successfully.');
    }

    public function destroy()
    {
        $userModel = $this->resolveUser();

        if ($userModel->id === auth()->id()) {
            return redirect()->route('user.admin.users.index', ['locale' => app()->getLocale()])
                ->with('error', 'You cannot delete your own account.');
        }

        $userModel->delete();

        return redirect()->route('user.admin.users.index', ['locale' => app()->getLocale()])
            ->with('success', 'User deleted successfully.');
    }

    public function resetAssessment()
    {
        $userModel = $this->resolveUser();

        if ($userModel->role !== 'user') {
            return redirect()->back()->with('error', 'Only users can have their assessment reset.');
        }

        DB::transaction(function () use ($userModel) {
            DB::table('assessment_answers')->where('user_id', $userModel->id)->delete();
            DB::table('user_pain_points')->where('user_id', $userModel->id)->delete();
            UserQuizResult::withTrashed()->where('user_id', $userModel->id)->forceDelete();

            $userModel->onboarding_status = 'new';
            $userModel->save();
        });

        app(AdminService::class)->resetAssessment($userModel->id);

        return redirect()->back()->with('success', 'Assessment reset successfully.');
    }

    public function verify()
    {
        $userModel = $this->resolveUser();

        if (!$userModel->email_verified_at) {
            $userModel->email_verified_at = now();
            $userModel->save();
        }

        return redirect()->route('user.admin.users.edit', ['user' => $userModel, 'locale' => app()->getLocale()])
            ->with('success', 'User email verified successfully.');
    }

    private function resolveUser()
    {
        $user = request()->route('user');
        
        if ($user instanceof User) {
            return $user;
        }
        
        if ($user) {
            return User::withTrashed()->findOrFail($user);
        }
        
        // Fallback: Check parameters array for any numeric value (likely the ID)
        $params = request()->route()->parameters();
        foreach ($params as $key => $value) {
            if ($key !== 'locale' && is_numeric($value)) {
                return User::withTrashed()->findOrFail($value);
            }
        }
        
        abort(404, 'User parameter not found.');
    }
}
