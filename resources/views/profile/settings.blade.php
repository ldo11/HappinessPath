<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile Settings</title>
</head>
<body class="bg-gray-50">
    <div class="max-w-2xl mx-auto p-6">
        <h1 class="text-2xl font-semibold text-gray-900">Profile Settings</h1>

        <form class="mt-6 space-y-6" method="POST" action="{{ route('profile.settings.update') }}">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700" for="spiritual_preference">Spiritual preference</label>
                <select id="spiritual_preference" name="spiritual_preference" class="mt-1 block w-full rounded-md border-gray-300">
                    <option value="buddhism" @selected(old('spiritual_preference', $user->spiritual_preference) === 'buddhism')>Buddhism</option>
                    <option value="christianity" @selected(old('spiritual_preference', $user->spiritual_preference) === 'christianity')>Christianity</option>
                    <option value="secular" @selected(old('spiritual_preference', $user->spiritual_preference) === 'secular')>Secular</option>
                </select>
                @error('spiritual_preference')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <input type="hidden" name="geo_privacy" value="0">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="geo_privacy" value="1" class="rounded border-gray-300" @checked((bool) old('geo_privacy', $user->geo_privacy))>
                    <span class="text-sm text-gray-700">Hide my location (geo privacy)</span>
                </label>
                @error('geo_privacy')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white">Save</button>
            </div>
        </form>
    </div>
</body>
</html>
