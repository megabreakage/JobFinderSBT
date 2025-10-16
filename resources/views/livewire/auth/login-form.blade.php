<div class="max-w-md mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Sign In</h2>

        @if($errorMessage)
            <x-ui.alert type="error" class="mb-4">
                {{ $errorMessage }}
            </x-ui.alert>
        @endif

        @if($isLocked)
            <x-ui.alert type="warning" class="mb-4">
                <strong>Account Locked</strong>
                <p class="mt-1">Your account has been temporarily locked due to multiple failed login attempts. Please try again in {{ $lockoutSeconds }} seconds.</p>
            </x-ui.alert>
        @endif

        <form wire:submit.prevent="submit">
            <x-form.input
                label="Email or Phone"
                name="login"
                wire:model="login"
                placeholder="john.doe@example.com or +1234567890"
                required
                :error="$errors->first('login')"
                :disabled="$isLocked"
            />

            <x-form.input
                label="Password"
                name="password"
                type="password"
                wire:model="password"
                placeholder="••••••••"
                required
                :error="$errors->first('password')"
                :disabled="$isLocked"
            />

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        wire:model="remember"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        @if($isLocked) disabled @endif
                    />
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>

                <a href="{{ route('password.request') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    Forgot password?
                </a>
            </div>

            <x-ui.button
                type="submit"
                variant="primary"
                size="lg"
                class="w-full"
                :disabled="$isLocked"
            >
                Sign In
            </x-ui.button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Create account
                </a>
            </p>
        </div>
    </div>
</div>
