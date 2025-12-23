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

        <form class="mt-6 space-y-4" method="POST" action="<?php echo e(route('login')); ?>">
            <?php echo csrf_field(); ?>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="email">Email</label>
                <input id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" class="mt-1 block w-full rounded-md border-gray-300" required>
                <?php $__errorArgs = ['email'];
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
                <label class="block text-sm font-medium text-gray-700" for="password">Password</label>
                <input id="password" type="password" name="password" class="mt-1 block w-full rounded-md border-gray-300" required>
                <?php $__errorArgs = ['password'];
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
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="remember" value="1" class="rounded border-gray-300" <?php if(old('remember')): echo 'checked'; endif; ?>>
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
<?php /**PATH /Users/luat/Main/HappinessPath/resources/views/auth/login.blade.php ENDPATH**/ ?>