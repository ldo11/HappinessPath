<?php

namespace App\Http\Controllers\Translator;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected $middleware = [
        'translator'
    ];

    public function index(Request $request)
    {
        $query = User::query()->whereIn('role', ['user', 'translator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && in_array($request->role, ['user', 'translator'], true)) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15);

        return view('translator.users.index', compact('users'));
    }

    public function create()
    {
        return view('translator.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['user', 'translator'])],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();

        User::create($validated);

        return redirect()->route('translator.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        if (!in_array($user->role, ['user', 'translator'], true)) {
            abort(404);
        }

        return view('translator.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (!in_array($user->role, ['user', 'translator'], true)) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['user', 'translator'])],
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('translator.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('translator.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        if (!in_array($user->role, ['user', 'translator'], true)) {
            abort(404);
        }

        $user->delete();

        return redirect()->route('translator.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
