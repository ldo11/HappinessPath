<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
</head>
<body class="bg-gray-50">
    <div class="max-w-md mx-auto p-6">
        <h1 class="text-2xl font-semibold text-gray-900">Login</h1>

        <form class="mt-6 space-y-4" method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700" for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border-gray-300" required>
                @error('email')
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
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="remember" value="1" class="rounded border-gray-300" @checked(old('remember'))>
                    <span class="text-sm text-gray-700">Remember me</span>
                </label>
            </div>

            <div class="pt-2">
                <button type="submit" class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
