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

        <form class="mt-6 space-y-6" method="POST" action="<?php echo e(route('profile.settings.update')); ?>">
            <?php echo csrf_field(); ?>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="spiritual_preference">Spiritual preference</label>
                <select id="spiritual_preference" name="spiritual_preference" class="mt-1 block w-full rounded-md border-gray-300">
                    <option value="buddhism" <?php if(old('spiritual_preference', $user->spiritual_preference) === 'buddhism'): echo 'selected'; endif; ?>>Buddhism</option>
                    <option value="christianity" <?php if(old('spiritual_preference', $user->spiritual_preference) === 'christianity'): echo 'selected'; endif; ?>>Christianity</option>
                    <option value="secular" <?php if(old('spiritual_preference', $user->spiritual_preference) === 'secular'): echo 'selected'; endif; ?>>Secular</option>
                </select>
                <?php $__errorArgs = ['spiritual_preference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <input type="hidden" name="geo_privacy" value="0">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="geo_privacy" value="1" class="rounded border-gray-300" <?php if((bool) old('geo_privacy', $user->geo_privacy)): echo 'checked'; endif; ?>>
                    <span class="text-sm text-gray-700">Hide my location (geo privacy)</span>
                </label>
                <?php $__errorArgs = ['geo_privacy'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="pt-2">
                <button type="submit" class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white">Save</button>
            </div>
        </form>
    </div>
</body>
</html>
<?php /**PATH /Users/luat/Main/HappinessPath/resources/views/profile/settings.blade.php ENDPATH**/ ?>