<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Nutrition Monitoring System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); min-height: 100vh; }
    </style>
</head>
<body class="flex items-center justify-center">
    <div class="w-full max-w-md p-8">
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Nutrition Monitoring System</h1>
                <p class="text-gray-500 mt-1">Sign in to your account</p>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <form method="POST" action="<?php echo e(url('/login')); ?>" class="space-y-5">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        placeholder="your@email.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-emerald-600 border-gray-300 rounded">
                    <label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
                </div>

                <button type="submit"
                    class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors">
                    Sign In
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-100">
                <p class="text-xs text-gray-500 text-center">Default accounts:</p>
                <div class="mt-2 space-y-1 text-xs text-gray-400 text-center">
                    <p>alice@nms.local / password (Head of Programmes)</p>
                    <p>bob@nms.local / password (System Manager)</p>
                    <p>cook.a@nms.local / password (Cook)</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\mukuk\Downloads\nutrition-app\resources\views/auth/login.blade.php ENDPATH**/ ?>