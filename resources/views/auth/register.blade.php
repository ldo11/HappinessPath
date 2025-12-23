<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
</head>
<body class="bg-gray-50">
    <div class="max-w-md mx-auto p-6">
        <h1 class="text-2xl font-semibold text-gray-900">Create account</h1>

        <form class="mt-6 space-y-4" method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700" for="name">Name</label>
                <input id="name" name="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-gray-300" required>
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border-gray-300" required>
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="city">City</label>
                <input id="city" name="city" value="{{ old('city') }}" class="mt-1 block w-full rounded-md border-gray-300" required>
                @error('city')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="spiritual_preference">Spiritual preference</label>
                <select id="spiritual_preference" name="spiritual_preference" class="mt-1 block w-full rounded-md border-gray-300" required>
                    <option value="">Select one</option>
                    <option value="buddhism" @selected(old('spiritual_preference') === 'buddhism')>Buddhism</option>
                    <option value="christianity" @selected(old('spiritual_preference') === 'christianity')>Christianity</option>
                    <option value="secular" @selected(old('spiritual_preference') === 'secular')>Secular</option>
                </select>
                @error('spiritual_preference')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="password">Password</label>
                <input id="password" type="password" name="password" class="mt-1 block w-full rounded-md border-gray-300" required>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300" required>
            </div>

            <div class="pt-2">
                <button type="submit" class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white">Register</button>
            </div>
        </form>
    </div>
</body>
</html>
