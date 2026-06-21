<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Nutrition System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); min-height: 100vh; }
    </style>
</head>
<body class="flex flex-col min-h-screen items-center justify-between">
    <div class="hidden sm:block"></div>

    <div class="w-full max-w-md p-8">
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-8">
                <img class="w-24 h-24 object-contain mb-4 mx-auto" src="<?php echo e(asset('img/logo.png')); ?>" alt="Logo">
                <h1 class="text-2xl font-bold text-gray-900">Nutrition System</h1>
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

                <div x-data="{ showPassword: false }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" required
                            class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                            placeholder="••••••••">
                        
                        <button type="button" @click="showPassword = !showPassword" 
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>

                            <svg x-show="showPassword" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 014.132-5.4M9.9 4.24a9.123 9.123 0 011.57-.24c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.4M15 12a3 3 0 11-6 0 3 3 0 016 0zm-9.9-5l11.8 11.8" />
                            </svg>
                        </button>
                    </div>
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
        </div>
    </div>

    <footer class="w-full text-center pb-6 text-sm text-slate-400">
        <p>Play it Forward | Digital Skills &copy; 2026</p>
        <p class="text-xs text-slate-500 mt-1">developed by Ori Studio</p>
    </footer>
</body>
</html><?php /**PATH C:\Users\mukuk\Documents\GitHub\pif-meal-app\resources\views/auth/login.blade.php ENDPATH**/ ?>