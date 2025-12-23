<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email</title>
</head>
<body class="bg-gray-50">
    <div class="max-w-md mx-auto p-6">
        <h1 class="text-2xl font-semibold text-gray-900">Verify your email</h1>

        <p class="mt-4 text-sm text-gray-700">We sent you a verification link. Please check your inbox.</p>

        <form class="mt-6" method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white">Resend verification email</button>
        </form>
    </div>
</body>
</html>
